<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(15);
        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(StoreAnnouncementRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();

            Announcement::create($data);

            return redirect()->route('announcements.index')
                ->with('toast_success', 'Announcement published successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to create announcement: ' . $e->getMessage());
        }
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate((new StoreAnnouncementRequest())->rules());

        try {
            $announcement->update($data);

            return redirect()->route('announcements.index')
                ->with('toast_success', 'Announcement updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to update announcement: ' . $e->getMessage());
        }
    }

    public function destroy(Announcement $announcement)
    {
        try {
            $announcement->delete();

            return redirect()->route('announcements.index')
                ->with('toast_success', 'Announcement deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to delete announcement: ' . $e->getMessage());
        }
    }
}
