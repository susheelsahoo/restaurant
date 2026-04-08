<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Customer;
use App\Models\ReservationStatus;
use App\Services\BookingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\ReservationStatusMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class ReservationController extends Controller
{
    public function create()
    {
        return redirect()->to(url('/') . '?scroll=reservation#reservation');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'visit_date'        => 'required|date',
            'visit_time'        => 'required',
            'guests'            => 'required|integer|min:1',
            'customer_name'     => 'required|string|max:255',
            'phone'             => 'required|string|max:20',
            'email'             => 'required|email',
            'notes'             => 'nullable|string|max:2000',
        ]);

        DB::beginTransaction();

        try {
            $nameParts = explode(' ', trim($validated['customer_name']), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? null;
            $customer = $this->resolveCustomer(
                email: $validated['email'],
                phone: $validated['phone'],
                firstName: $firstName,
                lastName: $lastName
            );

            $pendingStatus = ReservationStatus::where('name', 'pending')->firstOrFail();

            $reservation = Reservation::create([
                'booking_code'  => $this->generateBookingCode(),
                'customer_id'   => $customer->id,
                'customer_name' => $validated['customer_name'],
                'phone'         => $validated['phone'],
                'email'         => $validated['email'],
                'visit_date'    => $validated['visit_date'],
                'visit_time'    => $validated['visit_time'],
                'guests'        => $validated['guests'],
                'status_id'     => $pendingStatus->id,
                'status'        => $pendingStatus->name,
                'notes'         => $validated['notes'] ?? null,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            Log::error('Reservation creation failed.', [
                'customer_name' => $validated['customer_name'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'visit_date' => $validated['visit_date'] ?? null,
                'visit_time' => $validated['visit_time'] ?? null,
                'guests' => $validated['guests'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'error' => $e->getMessage(),
            ]);
            DB::rollBack();
            throw $e;
        }
        try {
            $template = ReservationStatusMail::resolveTemplateForReservation($reservation);
            if ($template) {
                Mail::to($customer->email)
                    ->bcc(config('app.HOTEL_EMAIL'))
                    ->send(new ReservationStatusMail($reservation, $template));
            }
        } catch (\Exception $e) {
            Log::error('Reservation confirmation email failed to send.', [
                'reservation_id' => $reservation->id ?? null,
                'booking_code' => $reservation->booking_code ?? null,
                'customer_id' => $customer->id ?? null,
                'email' => $customer->email ?? null,
                'error' => $e->getMessage(),
            ]);
            report($e);
        }
        return redirect()->back()->with([
            'alert_title' => 'Reservation Request Sent',
            'alert_text'  => 'Thank you for booking a table at Tifliso. Our team will respond soon!',
            'alert_icon'  => 'success',
        ]);
    }
    private function generateBookingCode(): string
    {
        return 'TFL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }

    private function resolveCustomer(string $email, string $phone, string $firstName, ?string $lastName): Customer
    {
        $customerByEmailAndPhone = Customer::where('email', $email)
            ->where('phone', $phone)
            ->first();
        $customerByEmail = Customer::where('email', $email)->first();
        $customerByPhone = Customer::where('phone', $phone)->first();

        if ($customerByEmailAndPhone) {
            $customerByEmailAndPhone->update([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'is_active'  => 1,
            ]);

            return $customerByEmailAndPhone;
        }

        if ($customerByEmail) {
            $customerByEmail->update([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'phone'      => $phone,
                'is_active'  => 1,
            ]);

            return $customerByEmail;
        }

        if ($customerByPhone) {
            $customerByPhone->update([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
                'phone'      => $phone,
                'is_active'  => 1,
            ]);

            return $customerByPhone;
        }

        return Customer::create([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'phone'      => $phone,
            'is_active'  => 1,
        ]);
    }

    public function slots(string $date, BookingService $service): JsonResponse
    {
        return response()->json($service->generateSlots($date));
    }
}
