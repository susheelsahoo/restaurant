<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\Customer;
use App\Services\BookingExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationStatusMail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings with filters
     */
    public function index(Request $request)
    {
        $query = Reservation::with([
            'customer' => function ($query) {
                $query->withCount('reservations');
            },
            'reservationStatus',
        ]);

        // Filter by status
        if ($request->filled('status')) {
            $status = ReservationStatus::where('name', $request->status)->first();
            if ($status) {
                $query->where('status_id', $status->id);
            }
        }

        // Filter by date
        if ($request->filled('select_date')) {
            $query->whereDate('visit_date', $request->select_date);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
            });
        }

        $bookings = $query
            ->orderByDesc('visit_date')
            ->orderBy('visit_time')
            ->paginate(50)
            ->withQueryString();

        return view('pages.bookings.index', compact('bookings'));
    }

    /**
     * Show create booking form
     */
    public function create()
    {
        return view('pages.bookings.create', ['booking' => null]);
    }

    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email'         => 'nullable|email|max:150',
            'phone'         => 'required|string|max:25',
            'visit_date'    => 'required|date|after_or_equal:today',
            'visit_time'    => 'required',
            'guests'        => 'required|integer|min:1|max:50',
            'notes'         => 'nullable|string|max:2000',
        ]);

        try {
            // Create or find customer
            ['firstName' => $firstName, 'lastName' => $lastName] = $this->parseCustomerName($validated['customer_name']);

            $customer = Customer::firstOrCreate(
                ['email' => $validated['email'] ?: null, 'phone' => $validated['phone']],
                [
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $validated['email'] ?: null,
                    'phone'      => $validated['phone'],
                    'is_active'  => true,
                ]
            );

            $pendingStatus = ReservationStatus::where('name', 'pending')->firstOrFail();
            $reservation = Reservation::create([
                'booking_code' => $this->generateBookingCode(),
                'customer_id'  => $customer->id,
                'status_id'    => $pendingStatus->id,
                'visit_date'   => $validated['visit_date'],
                'visit_time'   => $validated['visit_time'],
                'guests'       => $validated['guests'],
                'notes'        => $validated['notes'] ?? null,
            ]);

            // Send email notification
            $this->sendReservationEmail($customer->email, $reservation);

            return redirect()
                ->route('admin.bookings.index')
                ->with('success', 'Booking created successfully!');
        } catch (\Exception $e) {
            Log::error('Admin booking creation failed.', [
                'customer_name' => $validated['customer_name'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'visit_date' => $validated['visit_date'] ?? null,
                'visit_time' => $validated['visit_time'] ?? null,
                'guests' => $validated['guests'] ?? null,
                'error' => $e->getMessage(),
            ]);
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Failed to create booking. Please try again.');
        }
    }

    /**
     * Show edit booking form
     */
    public function edit(Reservation $booking)
    {
        $booking->load('customer', 'reservationStatus');
        return view('pages.bookings.create', compact('booking'));
    }

    /**
     * Update booking
     */
    public function update(Request $request, Reservation $booking)
    {
        $statusNames = ReservationStatus::active()->pluck('name')->toArray();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:25',
            'visit_date'    => 'required|date',
            'visit_time'    => 'required|date_format:H:i',
            'guests'        => 'required|integer|min:1|max:50',
            'status'        => ['required', Rule::in($statusNames)],
            'notes'         => 'nullable|string|max:2000',
        ]);

        try {
            // Get old status before update
            $oldStatus = $booking->reservationStatus;
            $oldStatusId = $booking->status_id;
            $newStatus = ReservationStatus::where('name', $validated['status'])->firstOrFail();
            $statusChanged = $oldStatusId !== $newStatus->id;

            // Update booking
            $booking->update([
                'status_id'  => $newStatus->id,
                'visit_date' => $validated['visit_date'],
                'visit_time' => $validated['visit_time'],
                'guests'     => $validated['guests'],
                'notes'      => $validated['notes'] ?? null,
            ]);

            // Send notification whenever the reservation status changes and an email is available.
            $shouldSendEmail = $statusChanged && !empty($validated['email']);
            if ($shouldSendEmail) {
                $this->sendReservationEmail($validated['email'], $booking->fresh());
            }

            // Redirect back to the old status list, not the new status
            $redirectParams = array_filter([
                'status' => $oldStatus->name,
                'select_date' => $request->input('select_date'),
                'search' => $request->input('search'),
            ]);
            return redirect()
                ->route('admin.bookings.index', $redirectParams)
                ->with('success', 'Booking updated successfully!');
        } catch (\Exception $e) {
            Log::error('Admin booking update failed.', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'status' => $validated['status'] ?? null,
                'email' => $validated['email'] ?? null,
                'visit_date' => $validated['visit_date'] ?? null,
                'visit_time' => $validated['visit_time'] ?? null,
                'error' => $e->getMessage(),
            ]);
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Failed to update booking. Please try again.');
        }
    }

    /**
     * Delete booking
     */
    public function destroy(Reservation $booking, Request $request)
    {
        abort_unless(auth()->user()?->getAllPermissions()->contains('name', 'delete'), 403);

        try {
            $booking->delete();
            $redirectParams = $this->getFilterParams($request);

            return redirect()
                ->route('admin.bookings.index', $redirectParams)
                ->with('success', 'Booking deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Admin booking delete failed.', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'error' => $e->getMessage(),
            ]);
            report($e);
            return back()->with('error', 'Failed to delete booking.');
        }
    }

    /**
     * Show booking details
     */
    public function show(Reservation $booking, Request $request)
    {
        $booking->load('customer', 'reservationStatus');
        $filterParams = $this->getFilterParams($request);

        return view('pages.bookings.show', compact('booking', 'filterParams'));
    }

    /**
     * Export bookings to file (XLSX or CSV)
     */
    public function export(Request $request, BookingExportService $exportService)
    {
        $format = $request->input('format', 'xlsx');

        // Prepare filters
        $filters = [];

        if ($request->filled('status')) {
            $status = ReservationStatus::where('name', $request->status)->first();
            if ($status) {
                $filters['status'] = $status->id;
            }
        }

        if ($request->filled('select_date')) {
            $filters['visit_date'] = $request->select_date;
        }

        if ($request->filled('search')) {
            $filters['search'] = $request->search;
        }

        try {
            // Generate export file
            if ($format === 'csv') {
                $filePath = $exportService->exportToCsv($filters);
                $mimeType = 'text/csv';
                $extension = 'csv';
            } else {
                $filePath = $exportService->exportToXlsx($filters);
                $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                $extension = 'xlsx';
            }

            $fileName = 'bookings_' . now()->format('Y-m-d_H-i-s') . '.' . $extension;

            // Download file
            return response()->download($filePath, $fileName, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Booking export failed.', [
                'format' => $format,
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);
            report($e);
            return back()->with('error', 'Failed to export bookings. Please try again.');
        }
    }

    /**
     * Parse customer name into first and last name
     */
    private function parseCustomerName(string $name): array
    {
        $parts = explode(' ', trim($name), 2);
        return [
            'firstName' => $parts[0],
            'lastName'  => $parts[1] ?? null,
        ];
    }

    /**
     * Send reservation status email
     */
    private function sendReservationEmail(?string $email, Reservation $reservation): void
    {
        if (!$email) {
            return;
        }

        try {
            $template = ReservationStatusMail::resolveTemplateForReservation($reservation);
            if (!$template) {
                return;
            }

            Mail::to($email)
                ->bcc(config('app.HOTEL_EMAIL'))
                ->queue(new ReservationStatusMail($reservation, $template));
        } catch (\Exception $e) {
            Log::error('Reservation status email failed to queue.', [
                'reservation_id' => $reservation->id,
                'booking_code' => $reservation->booking_code,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            report($e);
        }
    }

    /**
     * Get filter parameters from request
     */
    private function getFilterParams(Request $request): array
    {
        return array_filter([
            'status' => $request->input('status'),
            'select_date' => $request->input('select_date'),
            'search' => $request->input('search'),
        ]);
    }

    /**
     * Generate unique booking code
     */
    private function generateBookingCode(): string
    {
        return 'TFL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }
}
