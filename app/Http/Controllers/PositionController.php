<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionRequest;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with('department')->withCount('employees')->orderBy('name')->paginate(15);
        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        $departments = Department::pluck('name', 'id');
        return view('positions.create', compact('departments'));
    }

    public function store(StorePositionRequest $request)
    {
        try {
            Position::create($request->validated());

            return redirect()->route('positions.index')
                ->with('toast_success', 'Position created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to create position: ' . $e->getMessage());
        }
    }

    public function edit(Position $position)
    {
        $departments = Department::pluck('name', 'id');
        return view('positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        $rules = (new StorePositionRequest())->rules();
        $rules['code'] = $rules['code'] . ',' . $position->id;

        $data = $request->validate($rules);

        try {
            $position->update($data);

            return redirect()->route('positions.index')
                ->with('toast_success', 'Position updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to update position: ' . $e->getMessage());
        }
    }

    public function destroy(Position $position)
    {
        try {
            if ($position->employees()->count() > 0) {
                return back()->with('toast_error', 'Cannot delete position with active employees.');
            }

            $position->delete();

            return redirect()->route('positions.index')
                ->with('toast_success', 'Position deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to delete position: ' . $e->getMessage());
        }
    }
}
