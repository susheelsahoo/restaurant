<?php

namespace App\Http\Controllers;

use App\Mail\CustomerNotificationMail;
use App\Models\Customer;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CustomersController extends Controller
{
    // List
    public function index()
    {
        $customers = Customer::withCount('reservations')
            ->orderByDesc('reservations_count')
            ->latest()
            ->paginate(10);
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
