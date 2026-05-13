<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('employees')->orderBy('name')->paginate(15);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(StoreDepartmentRequest $request)
    {
        try {
            Department::create($request->validated());

            return redirect()->route('departments.index')
                ->with('toast_success', 'Department created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to create department: ' . $e->getMessage());
        }
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $rules = (new StoreDepartmentRequest())->rules();
        $rules['code'] = $rules['code'] . ',' . $department->id;

        $data = $request->validate($rules);

        try {
            $department->update($data);

            return redirect()->route('departments.index')
                ->with('toast_success', 'Department updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to update department: ' . $e->getMessage());
        }
    }

    public function destroy(Department $department)
    {
        try {
            if ($department->employees()->count() > 0) {
                return back()->with('toast_error', 'Cannot delete department with active employees.');
            }

            $department->delete();

            return redirect()->route('departments.index')
                ->with('toast_success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to delete department: ' . $e->getMessage());
        }
    }
}
