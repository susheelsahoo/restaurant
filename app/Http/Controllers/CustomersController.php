<?php

namespace App\Http\Controllers;

use App\Mail\CustomerNotificationMail;
use App\Models\Customer;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;

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

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active');
        $data['is_subscribed'] = $request->boolean('is_subscribed', true);
        $data['unsubscribed_at'] = $data['is_subscribed'] ? null : now();

        Customer::create($data);

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

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active');
        $data['is_subscribed'] = $request->boolean('is_subscribed');
        $data['unsubscribed_at'] = $data['is_subscribed']
            ? null
            : ($customer->unsubscribed_at ?? now());

        $customer->update($data);

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
            ->where('is_subscribed', true)
            ->get();

        if ($customers->isEmpty()) {
            return back()->with('error', 'No selected customers are subscribed and have a valid email address.');
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

    public function unsubscribe(Request $request, Customer $customer): Response
    {
        abort_unless($request->hasValidSignature(), 403);

        if ($customer->is_subscribed) {
            $customer->forceFill([
                'is_subscribed' => false,
                'unsubscribed_at' => now(),
            ])->save();
        }

        return response(<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed</title>
</head>
<body style="margin:0;padding:40px;font-family:Arial,sans-serif;background:#f8f9fa;color:#212529;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #dee2e6;border-radius:12px;padding:32px;">
        <h1 style="margin:0 0 16px;font-size:28px;">You have been unsubscribed</h1>
        <p style="margin:0 0 12px;line-height:1.6;">You will no longer receive promotional emails from us.</p>
        <p style="margin:0;line-height:1.6;">Reservation and booking-related emails may still be sent when needed.</p>
    </div>
</body>
</html>
HTML, 200)->header('Content-Type', 'text/html');
    }
}
