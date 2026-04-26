@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

<div class="page-header">
    <h1>Edit User</h1>
    <p>Update user details</p>
</div>

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

            <div class="form-group" style="grid-column: span 2;">
                <label>Full Name <span style="color:red;">*</span></label>
                <input type="text" name="full_name"
                       class="form-control"
                       value="{{ old('full_name', $user->full_name) }}" required>
                @error('full_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Username cannot be changed --}}
            <div class="form-group" style="grid-column: span 2;">
                <label>Username</label>
                <input type="text"
                       class="form-control"
                       value="{{ $user->username }}"
                       disabled
                       style="background:#f9fafb; color:#9ca3af; cursor:not-allowed;">
                <small style="color:#9ca3af; font-size:12px;">
                    Username cannot be changed
                </small>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Email <span style="color:red;">*</span></label>
                <input type="email" name="email"
                       class="form-control"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Role <span style="color:red;">*</span></label>
                <select name="role" class="form-control" required
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    <option value="admin"
                        {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                        Admin — Full access
                    </option>
                    <option value="cashier"
                        {{ old('role', $user->role) === 'cashier' ? 'selected' : '' }}>
                        Cashier — POS only
                    </option>
                </select>
                @if($user->id === auth()->id())
                    {{-- Hidden input because disabled fields don't submit --}}
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <small style="color:#9ca3af; font-size:12px;">
                        You cannot change your own role
                    </small>
                @endif
            </div>

            {{-- Password section --}}
            <div style="grid-column: span 2; border-top:2px solid #f3f4f6;
                        padding-top:16px; margin-top:4px;">
                <p style="font-size:13px; color:#6b7280; margin-bottom:14px;">
                    <i class="fas fa-lock"></i>
                    Leave password fields blank to keep the current password.
                </p>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password"
                       class="form-control"
                       placeholder="Leave blank to keep current">
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation"
                       class="form-control"
                       placeholder="Repeat new password">
            </div>

        </div>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update User
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection