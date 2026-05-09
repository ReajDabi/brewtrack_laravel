@extends('layouts.app')

@section('title', 'Settings')

@section('content')

{{-- Mobile Responsive Styles specifically for this view --}}
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .settings-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .settings-actions {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }
    
    /* Mobile Breakpoint */
    @media (max-width: 768px) {
        .page-header {
            text-align: center;
        }
        .settings-grid {
            grid-template-columns: 1fr; /* Stacks the left and right columns */
        }
        .settings-actions {
            flex-direction: column;
        }
        .settings-actions .btn {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    }
</style>

<div class="page-header">
    <h1>Settings</h1>
    <p>Configure your coffee shop system</p>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="settings-grid">

        {{-- LEFT COLUMN --}}
        <div class="settings-column">

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
        <div class="settings-column">

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

            {{-- Printer Settings --}}
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-print"></i> Thermal Printer Settings
                </div>

                <div class="form-group">
                    <label>Printer Name</label>
                    <input type="text" name="printer_name"
                           class="form-control"
                           value="{{ old('printer_name', $settings->get('printer_name', 'XPrinter XP-58IIH')) }}"
                           placeholder="Exact name from Windows Devices and Printers">
                    <small style="color:#9ca3af; font-size:12px;">
                        <i class="fas fa-info-circle"></i>
                        Control Panel → Devices and Printers → right-click → copy exact name
                    </small>
                </div>

                <div class="form-group">
                    <label>Connection Type</label>
                    <select name="printer_connection" class="form-control"
                            id="printerConnectionSelect">
                        @php
                            $conn = old('printer_connection',
                                $settings->get('printer_connection', 'windows'));
                        @endphp
                        <option value="windows" {{ $conn === 'windows' ? 'selected' : '' }}>
                            USB (Windows Driver)
                        </option>
                        <option value="network" {{ $conn === 'network' ? 'selected' : '' }}>
                            Network / LAN (IP Address)
                        </option>
                    </select>
                </div>

                {{-- Network settings (shown only when LAN is selected) --}}
                <div id="networkFields"
                     style="{{ $settings->get('printer_connection') === 'network' ? '' : 'display:none;' }}">
                    <div class="form-group">
                        <label>Printer IP Address</label>
                        <input type="text" name="printer_ip"
                               class="form-control"
                               value="{{ old('printer_ip', $settings->get('printer_ip', '192.168.1.100')) }}"
                               placeholder="e.g. 192.168.1.100">
                    </div>
                    <div class="form-group">
                        <label>Printer Port</label>
                        <input type="number" name="printer_port"
                               class="form-control"
                               value="{{ old('printer_port', $settings->get('printer_port', '9100')) }}"
                               placeholder="9100">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="auto_print" value="1"
                               {{ $settings->get('auto_print', '1') === '1' ? 'checked' : '' }}
                               style="width:16px; height:16px; accent-color:#6F4E37;">
                        <div>
                            <span style="font-weight:500;">Auto-print receipt on order</span>
                            <div style="font-size:11px; color:#9ca3af;">
                                Prints automatically when cashier places an order
                            </div>
                        </div>
                    </label>
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
    <div class="settings-actions">
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

@push('scripts')
<script>
    document.getElementById('printerConnectionSelect')
        .addEventListener('change', function() {
            document.getElementById('networkFields').style.display =
                this.value === 'network' ? '' : 'none';
        });
</script>
@endpush