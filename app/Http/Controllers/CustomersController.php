<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    // List
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('admin.customers.index', compact('customers'));
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
}
