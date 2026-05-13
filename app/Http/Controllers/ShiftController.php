<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShiftRequest;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::orderBy('name')->paginate(15);
        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(StoreShiftRequest $request)
    {
        try {
            Shift::create($request->validated());

            return redirect()->route('shifts.index')
                ->with('toast_success', 'Shift created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to create shift: ' . $e->getMessage());
        }
    }

    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $rules = (new StoreShiftRequest())->rules();
        $rules['code'] = $rules['code'] . ',' . $shift->id;

        $data = $request->validate($rules);

        try {
            $shift->update($data);

            return redirect()->route('shifts.index')
                ->with('toast_success', 'Shift updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to update shift: ' . $e->getMessage());
        }
    }

    public function destroy(Shift $shift)
    {
        try {
            $shift->delete();

            return redirect()->route('shifts.index')
                ->with('toast_success', 'Shift deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to delete shift: ' . $e->getMessage());
        }
    }
}
