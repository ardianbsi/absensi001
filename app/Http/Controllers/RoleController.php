<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(15);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0] ?? 'general';
        });

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role = Role::create(['name' => $data['name']]);

            if (!empty($data['permissions'])) {
                $role->syncPermissions(Permission::whereIn('id', $data['permissions'])->get());
            }

            return redirect()->route('roles.index')
                ->with('toast_success', 'Role created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0] ?? 'general';
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id . '|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role->update(['name' => $data['name']]);

            if (!empty($data['permissions'])) {
                $role->syncPermissions(Permission::whereIn('id', $data['permissions'])->get());
            } else {
                $role->syncPermissions([]);
            }

            return redirect()->route('roles.index')
                ->with('toast_success', 'Role updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        try {
            if ($role->users()->count() > 0) {
                return back()->with('toast_error', 'Cannot delete role with assigned users.');
            }

            $role->delete();

            return redirect()->route('roles.index')
                ->with('toast_success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to delete role: ' . $e->getMessage());
        }
    }
}
