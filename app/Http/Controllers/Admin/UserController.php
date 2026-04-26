<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Show all users
    public function index()
    {
        $users = User::orderBy('role')->orderBy('full_name')->get();

        return view('admin.users.index', compact('users'));
    }

    // Show Add User form
    public function create()
    {
        return view('admin.users.create');
    }

    // Save new user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username'  => 'required|string|max:50|unique:users',
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users',
            'role'      => 'required|in:admin,cashier',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        // Hash the password before saving
        $validated['password']  = Hash::make($validated['password']);
        $validated['is_active'] = true;

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', $validated['full_name'] . ' added successfully!');
    }

    // Show edit form
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Save edited user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email,' . $user->id,
            'role'      => 'required|in:admin,cashier',
            // Password is optional on update
            'password'  => 'nullable|string|min:6|confirmed',
        ]);

        // Only update password if a new one was entered
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', $user->full_name . ' updated successfully!');
    }

    // Deactivate a user (soft delete)
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', $user->full_name . ' has been deactivated.');
    }

    // Toggle active/inactive
    public function toggleActive(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', $user->full_name . ' has been ' . $status . '.');
    }
}