<?php

namespace App\Http\Controllers;

use App\Mail\CustomerNotificationMail;
use App\Models\Customer;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class CustomersController extends Controller
{
    // List
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->withCount('reservations')
            ->withMax('reservations', 'visit_date');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $customers->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereRaw("TRIM(CONCAT(first_name, ' ', COALESCE(last_name, ''))) like ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('booking_count')) {
            $bookingCount = $request->input('booking_count');

            if ($bookingCount === '3_plus') {
                $customers->having('reservations_count', '>=', 3);
            } elseif ($bookingCount === '5_plus') {
                $customers->having('reservations_count', '>=', 5);
            } elseif ($bookingCount === '10_plus') {
                $customers->having('reservations_count', '>=', 10);
            } elseif (is_numeric($bookingCount)) {
                $customers->having('reservations_count', '=', (int) $bookingCount);
            }
        }

        if ($request->filled('last_booking_days') && is_numeric($request->input('last_booking_days'))) {
            $days = (int) $request->input('last_booking_days');
            $thresholdDate = Carbon::today()->subDays($days)->toDateString();

            $customers->has('reservations')
                ->whereDoesntHave('reservations', function ($query) use ($thresholdDate) {
                    $query->whereDate('visit_date', '>', $thresholdDate);
                });
        }

        $customers = $customers
            ->orderByDesc('reservations_count')
            ->orderByDesc('reservations_max_visit_date')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $emailTemplates = EmailTemplate::where('is_active', true)
            ->orderByDesc('id')
            ->get(['id', 'slug', 'subject', 'message']);

        return view('admin.customers.index', compact('customers', 'emailTemplates'));
    }

    // Show Create Form
    public function create()
    {
        return view('admin.customers.create');
    }

    // Store
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|max:100',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|max:20',
            'date_of_birth' => 'nullable|date',
            'date_of_anniversary' => 'nullable|date'
        ]);

        Customer::create($request->all());

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer created successfully');
    }

    // Show Edit Form
    public function edit(Customer $customer)
    {
        return view('admin.customers.create', compact('customer'));
    }

    // Update
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'first_name' => 'required|max:100',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|max:20',
            'date_of_birth' => 'nullable|date',
            'date_of_anniversary' => 'nullable|date'
        ]);

        $customer->update($request->all());

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer updated successfully');
    }

    // Delete
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully');
    }

    public function sendNotification(Request $request)
    {
        $validated = $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'integer|exists:customers,id',
            'email_template_id' => 'nullable|integer|exists:email_templates,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $customers = Customer::whereIn('id', $validated['customer_ids'])
            ->whereNotNull('email')
            ->get();

        if ($customers->isEmpty()) {
            return back()->with('error', 'No selected customers have a valid email address.');
        }

        foreach ($customers as $customer) {
            Mail::to($customer->email)->queue(
                new CustomerNotificationMail(
                    customer: $customer,
                    subjectTemplate: $validated['subject'],
                    messageTemplate: $validated['message']
                )
            );
        }

        $sentCount = $customers->count();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', "Queued {$sentCount} customer notification(s) successfully.");
    }
}
