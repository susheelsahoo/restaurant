<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        Department::create($data);

        return redirect()->route('admin.purchase-orders.departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.form', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        $department->update($data);

        return redirect()->route('admin.purchase-orders.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        // Check if department is being used
        if ($department->requests()->exists()) {
            return redirect()->route('admin.purchase-orders.departments.index')
                ->with('error', 'Cannot delete department that has associated requests.');
        }

        $department->delete();
        return redirect()->route('admin.purchase-orders.departments.index')->with('success', 'Department deleted successfully.');
    }
}