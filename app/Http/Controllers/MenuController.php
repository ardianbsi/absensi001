<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('children')->whereNull('parent_id')->orderBy('order')->paginate(15);
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $parentMenus = Menu::whereNull('parent_id')->orderBy('name')->pluck('name', 'id');
        return view('menus.create', compact('parentMenus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
            'permission_name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            Menu::create($data);

            return redirect()->route('menus.index')
                ->with('toast_success', 'Menu created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to create menu: ' . $e->getMessage());
        }
    }

    public function edit(Menu $menu)
    {
        $parentMenus = Menu::whereNull('parent_id')->where('id', '!=', $menu->id)
            ->orderBy('name')->pluck('name', 'id');

        return view('menus.edit', compact('menu', 'parentMenus'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
            'permission_name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            if ($data['parent_id'] == $menu->id) {
                return back()->with('toast_error', 'Menu cannot be its own parent.');
            }

            $menu->update($data);

            return redirect()->route('menus.index')
                ->with('toast_success', 'Menu updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to update menu: ' . $e->getMessage());
        }
    }

    public function destroy(Menu $menu)
    {
        try {
            if ($menu->children()->count() > 0) {
                return back()->with('toast_error', 'Cannot delete menu with sub-menus.');
            }

            $menu->delete();

            return redirect()->route('menus.index')
                ->with('toast_success', 'Menu deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to delete menu: ' . $e->getMessage());
        }
    }
}
