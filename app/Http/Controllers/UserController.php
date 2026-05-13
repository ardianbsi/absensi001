<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->pluck('name', 'id');
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $role = Role::findById($data['role_id']);
            $user->assignRole($role);

            return redirect()->route('users.index')
                ->with('toast_success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->pluck('name', 'id');
        $userRole = $user->roles->first();
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            $role = Role::findById($data['role_id']);
            $user->syncRoles([$role]);

            return redirect()->route('users.index')
                ->with('toast_success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('toast_error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return back()->with('toast_error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()->route('users.index')
                ->with('toast_success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function toggleStatus(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return back()->with('toast_error', 'You cannot change your own status.');
            }

            $user->is_active = !$user->is_active;
            $user->save();

            $status = $user->is_active ? 'activated' : 'deactivated';

            return redirect()->route('users.index')
                ->with('toast_success', "User {$status} successfully.");
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to toggle user status: ' . $e->getMessage());
        }
    }
}
