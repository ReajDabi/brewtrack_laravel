@extends('layouts.app')

@section('title', 'User Management')

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center;
            justify-content:space-between; margin-bottom:20px;">
    <div class="page-header" style="margin-bottom:0;">
        <h1>User Management</h1>
        <p>Manage system users</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add User
    </a>
</div>

{{-- Users table --}}
<div class="card">
    <div class="card-title">
        <i class="fas fa-users"></i> Users
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Last Login</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="font-weight:600;">
                            {{-- Show (You) next to the logged-in user --}}
                            {{ $user->full_name }}
                            @if($user->id === auth()->id())
                                <span style="font-size:11px; color:#9ca3af;
                                             font-weight:400;">(You)</span>
                            @endif
                        </td>
                        <td style="color:#6b7280; font-size:13px;">
                            {{ $user->username }}
                        </td>
                        <td style="font-size:13px;">
                            {{ $user->email }}
                        </td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge"
                                      style="background:#ede9fe; color:#5b21b6;">
                                    Admin
                                </span>
                            @else
                                <span class="badge"
                                      style="background:#e0f2fe; color:#075985;">
                                    Cashier
                                </span>
                            @endif
                        </td>
                        <td style="font-size:12px; color:#6b7280;">
                            @if($user->last_login)
                                {{ $user->last_login->format('M d, Y h:i A') }}
                            @else
                                <span style="color:#d1d5db;">Never</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge"
                                      style="background:#d1fae5; color:#065f46;">
                                    Active
                                </span>
                            @else
                                <span class="badge"
                                      style="background:#fee2e2; color:#991b1b;">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                {{-- Edit button --}}
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Toggle active/inactive --}}
                                @if($user->id !== auth()->id())
                                    <form method="POST"
                                          action="{{ route('admin.users.toggle', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm"
                                                style="background:{{ $user->is_active ? '#fef3c7' : '#d1fae5' }};
                                                       color:{{ $user->is_active ? '#92400e' : '#065f46' }};"
                                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>

                                    {{-- Delete button --}}
                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Deactivate {{ $user->full_name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7"
                            style="text-align:center; padding:60px; color:#9ca3af;">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection