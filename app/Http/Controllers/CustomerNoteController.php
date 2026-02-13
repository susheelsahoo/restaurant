<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerNote;
use Illuminate\Http\Request;
use Illuminate\Container\Attributes\Auth;

class CustomerNoteController extends Controller
{
    public function show(Customer $customer)
    {
        $notes = $customer->notes()->latest()->get();

        return view('admin.customers.partials.notes-list', compact('notes'));
    }

    public function create($customer_id)
    {
        return view('admin.customers.add_note', compact('customer_id'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'note' => 'required|string',
            'note_type' => 'nullable|string|max:50'
        ]);
        $note = CustomerNote::create([
            'customer_id' => $request->customer_id,
            'note' => $request->note,
            'note_type' => $request->note_type,
            'created_by' => auth()->id(),
        ]);
        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Note added successfully');
    }

    public function destroy($id)
    {
        $note = CustomerNote::findOrFail($id);
        $note->delete();

        return response()->json(['success' => true]);
    }
}
