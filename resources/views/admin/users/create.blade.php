@extends('layouts.app')

@section('title', 'Add User')

@section('content')

<div class="page-header">
    <h1>Add User</h1>
    <p>Create a new admin or cashier account</p>
</div>

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

            <div class="form-group">
                <label>Full Name <span style="color:red;">*</span></label>
                <input type="text" name="full_name"
                       class="form-control"
                       value="{{ old('full_name') }}"
                       placeholder="e.g. Juan dela Cruz" required>
                @error('full_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Username <span style="color:red;">*</span></label>
                <input type="text" name="username"
                       class="form-control"
                       value="{{ old('username') }}"
                       placeholder="e.g. juan123" required>
                @error('username')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Email <span style="color:red;">*</span></label>
                <input type="email" name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       placeholder="e.g. juan@brewtrack.com" required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Role <span style="color:red;">*</span></label>
                <select name="role" class="form-control" required>
                    <option value="">Select role...</option>
                    <option value="admin"
                        {{ old('role') === 'admin' ? 'selected' : '' }}>
                        Admin — Full access
                    </option>
                    <option value="cashier"
                        {{ old('role') === 'cashier' ? 'selected' : '' }}>
                        Cashier — POS only
                    </option>
                </select>
                @error('role')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Password <span style="color:red;">*</span></label>
                <input type="password" name="password"
                       class="form-control"
                       placeholder="Minimum 6 characters" required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Confirm Password <span style="color:red;">*</span></label>
                <input type="password" name="password_confirmation"
                       class="form-control"
                       placeholder="Repeat password" required>
            </div>

        </div>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Add User
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection