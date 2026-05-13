<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHolidayRequest;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->paginate(15);
        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('holidays.create');
    }

    public function store(StoreHolidayRequest $request)
    {
        try {
            Holiday::create($request->validated());

            return redirect()->route('holidays.index')
                ->with('toast_success', 'Holiday created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to create holiday: ' . $e->getMessage());
        }
    }

    public function edit(Holiday $holiday)
    {
        return view('holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $rules = (new StoreHolidayRequest())->rules();
        $rules['date'] = $rules['date'] . ',' . $holiday->id;

        $data = $request->validate($rules);

        try {
            $holiday->update($data);

            return redirect()->route('holidays.index')
                ->with('toast_success', 'Holiday updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to update holiday: ' . $e->getMessage());
        }
    }

    public function destroy(Holiday $holiday)
    {
        try {
            $holiday->delete();

            return redirect()->route('holidays.index')
                ->with('toast_success', 'Holiday deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to delete holiday: ' . $e->getMessage());
        }
    }
}
