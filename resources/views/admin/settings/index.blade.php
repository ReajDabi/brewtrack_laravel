@extends('layouts.app')

@section('title', 'Settings')

@section('content')

<div class="page-header">
    <h1>Settings</h1>
    <p>Configure your coffee shop system</p>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        {{-- LEFT COLUMN --}}
        <div style="display:flex; flex-direction:column; gap:20px;">

            {{-- Shop Information --}}
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-store"></i> Shop Information
                </div>

                <div class="form-group">
                    <label>Shop Name <span style="color:red;">*</span></label>
                    <input type="text" name="shop_name"
                           class="form-control"
                           value="{{ old('shop_name', $settings->get('shop_name', 'BrewTrack Coffee Shop')) }}"
                           placeholder="e.g. BrewTrack Coffee Shop" required>
                    @error('shop_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="shop_address"
                              class="form-control" rows="2"
                              placeholder="Full address...">{{ old('shop_address', $settings->get('shop_address')) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="shop_contact"
                           class="form-control"
                           value="{{ old('shop_contact', $settings->get('shop_contact')) }}"
                           placeholder="e.g. 09123456789">
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label>Email Address</label>
                    <input type="email" name="shop_email"
                           class="form-control"
                           value="{{ old('shop_email', $settings->get('shop_email')) }}"
                           placeholder="e.g. info@brewtrack.com">
                    @error('shop_email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Tax and Currency --}}
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-percentage"></i> Tax & Currency
                </div>

                <div class="form-group">
                    <label>Tax Rate <span style="color:red;">*</span></label>
                    <div style="position:relative;">
                        <input type="number" name="tax_rate"
                               class="form-control"
                               value="{{ old('tax_rate', $settings->get('tax_rate', '0.12')) }}"
                               step="0.01" min="0" max="1"
                               placeholder="0.12" required
                               style="padding-right:50px;">
                        <span style="position:absolute; right:14px; top:50%;
                                     transform:translateY(-50%);
                                     color:#9ca3af; font-size:13px;">
                            (12%)
                        </span>
                    </div>
                    <small style="color:#9ca3af; font-size:12px;">
                        Enter as decimal: 0.12 = 12%, 0.10 = 10%
                    </small>
                    @error('tax_rate')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label>Currency <span style="color:red;">*</span></label>
                    <select name="currency" class="form-control" required>
                        @php
                            $current = old('currency', $settings->get('currency', 'PHP'));
                        @endphp
                        <option value="PHP" {{ $current === 'PHP' ? 'selected' : '' }}>
                            PHP — Philippine Peso (₱)
                        </option>
                        <option value="USD" {{ $current === 'USD' ? 'selected' : '' }}>
                            USD — US Dollar ($)
                        </option>
                        <option value="EUR" {{ $current === 'EUR' ? 'selected' : '' }}>
                            EUR — Euro (€)
                        </option>
                    </select>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div style="display:flex; flex-direction:column; gap:20px;">

            {{-- Receipt Settings --}}
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-receipt"></i> Receipt Settings
                </div>

                <div class="form-group">
                    <label>Receipt Header Message</label>
                    <input type="text" name="receipt_header"
                           class="form-control"
                           value="{{ old('receipt_header', $settings->get('receipt_header', 'Thank you for visiting!')) }}"
                           placeholder="Message at top of receipt">
                    <small style="color:#9ca3af; font-size:12px;">
                        Shown at the top of every printed receipt
                    </small>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label>Receipt Footer Message</label>
                    <input type="text" name="receipt_footer"
                           class="form-control"
                           value="{{ old('receipt_footer', $settings->get('receipt_footer', 'Please come again!')) }}"
                           placeholder="Message at bottom of receipt">
                    <small style="color:#9ca3af; font-size:12px;">
                        Shown at the bottom of every printed receipt
                    </small>
                </div>
            </div>

            {{-- System Info (read only) --}}
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-info-circle"></i> System Information
                </div>

                <div style="font-size:13px;">
                    <div style="display:flex; justify-content:space-between;
                                padding:10px 0; border-bottom:1px solid #f3f4f6;">
                        <span style="color:#6b7280;">System Name</span>
                        <span style="font-weight:500;">BrewTrack</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;
                                padding:10px 0; border-bottom:1px solid #f3f4f6;">
                        <span style="color:#6b7280;">Version</span>
                        <span style="font-weight:500;">1.0.0</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;
                                padding:10px 0; border-bottom:1px solid #f3f4f6;">
                        <span style="color:#6b7280;">Laravel Version</span>
                        <span style="font-weight:500;">
                            {{ app()->version() }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;
                                padding:10px 0; border-bottom:1px solid #f3f4f6;">
                        <span style="color:#6b7280;">PHP Version</span>
                        <span style="font-weight:500;">
                            {{ PHP_VERSION }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;
                                padding:10px 0;">
                        <span style="color:#6b7280;">Logged in as</span>
                        <span style="font-weight:500;">
                            {{ auth()->user()->full_name }}
                            ({{ ucfirst(auth()->user()->role) }})
                        </span>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-chart-bar"></i> Quick Stats
                </div>

                <div style="font-size:13px;">
                    @php
                        $totalUsers    = \App\Models\User::where('is_active', true)->count();
                        $totalMenuItems= \App\Models\MenuItem::where('is_active', true)->count();
                        $totalInventory= \App\Models\Inventory::where('is_active', true)->count();
                        $totalOrders   = \App\Models\Order::count();
                    @endphp

                    <div style="display:flex; justify-content:space-between;
                                padding:10px 0; border-bottom:1px solid #f3f4f6;">
                        <span style="color:#6b7280;">Active Users</span>
                        <span style="font-weight:600; color:#6F4E37;">
                            {{ $totalUsers }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;
                                padding:10px 0; border-bottom:1px solid #f3f4f6;">
                        <span style="color:#6b7280;">Menu Items</span>
                        <span style="font-weight:600; color:#6F4E37;">
                            {{ $totalMenuItems }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;
                                padding:10px 0; border-bottom:1px solid #f3f4f6;">
                        <span style="color:#6b7280;">Inventory Items</span>
                        <span style="font-weight:600; color:#6F4E37;">
                            {{ $totalInventory }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;
                                padding:10px 0;">
                        <span style="color:#6b7280;">Total Orders</span>
                        <span style="font-weight:600; color:#6F4E37;">
                            {{ $totalOrders }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Save button --}}
    <div style="margin-top:20px; display:flex; gap:10px;">
        <button type="submit" class="btn btn-primary"
                style="padding:12px 32px; font-size:15px;">
            <i class="fas fa-save"></i> Save Settings
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"
           style="padding:12px 24px;">
            Cancel
        </a>
    </div>

</form>

@endsection