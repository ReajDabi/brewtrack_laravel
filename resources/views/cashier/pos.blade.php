@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')

@push('styles')
<style>
    .pos-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 16px;
        height: calc(100vh - 90px);
    }
    .menu-panel {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    .category-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .cat-btn {
        padding: 7px 16px;
        border-radius: 20px;
        border: 2px solid #e5e7eb;
        background: white;
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        transition: all 0.2s;
    }
    .cat-btn:hover  { border-color: #6F4E37; color: #6F4E37; }
    .cat-btn.active { border-color: #6F4E37; background: #6F4E37; color: white; }
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        flex: 1;
    }
    .menu-item-card {
        border: 2px solid #f3f4f6;
        border-radius: 10px;
        padding: 14px 10px;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
        background: white;
    }
    .menu-item-card:hover {
        border-color: #6F4E37;
        box-shadow: 0 4px 12px rgba(111,78,55,0.15);
        transform: translateY(-2px);
    }
    .menu-item-name  { font-size: 13px; font-weight: 600; color: #1a1a2e; margin-bottom: 4px; }
    .menu-item-price { font-size: 14px; font-weight: 700; color: #6F4E37; }
    .menu-item-cat   { font-size: 11px; color: #9ca3af; margin-top: 3px; }
    .cart-panel {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .cart-header {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1a1a2e;
    }
    .cart-header i { color: #6F4E37; }
    .cart-items {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
        margin-bottom: 12px;
    }
    .cart-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .cart-item-name  { flex: 1; font-size: 13px; font-weight: 500; }
    .cart-item-total {
        font-size: 13px; font-weight: 700;
        color: #6F4E37; min-width: 70px; text-align: right;
    }
    .qty-wrap { display: flex; align-items: center; gap: 6px; }
    .qty-btn {
        width: 24px; height: 24px;
        border-radius: 6px; border: none;
        background: #f3f4f6; color: #374151;
        font-size: 14px; font-weight: 700;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s;
    }
    .qty-btn:hover { background: #6F4E37; color: white; }
    .qty-num { font-size: 13px; font-weight: 600; min-width: 20px; text-align: center; }
    .remove-btn {
        background: none; border: none;
        color: #ef4444; cursor: pointer;
        font-size: 13px; padding: 2px 4px;
    }
    .cart-empty { text-align: center; padding: 40px 0; color: #9ca3af; }
    .cart-empty i { font-size: 36px; display: block; margin-bottom: 10px; }
    .cart-totals { border-top: 2px solid #f3f4f6; padding-top: 12px; font-size: 13px; }
    .total-row {
        display: flex; justify-content: space-between;
        padding: 3px 0; color: #6b7280;
    }
    .total-row.grand {
        font-size: 16px; font-weight: 700; color: #1a1a2e;
        margin-top: 6px; padding-top: 8px; border-top: 2px solid #1a1a2e;
    }
    .checkout-form { margin-top: 12px; }
    .checkout-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .pos-input {
        width: 100%; padding: 9px 12px;
        border: 2px solid #e5e7eb; border-radius: 8px;
        font-size: 13px; font-family: 'Poppins', sans-serif;
        margin-bottom: 8px;
    }
    .pos-input:focus { outline: none; border-color: #6F4E37; }
    .change-box {
        padding: 8px 12px; border-radius: 8px;
        font-size: 13px; font-weight: 600;
        text-align: center; margin-bottom: 8px; display: none;
    }
    .change-ok  { background: #d1fae5; color: #065f46; }
    .change-bad { background: #fee2e2; color: #991b1b; }
    .btn-checkout {
        width: 100%; padding: 13px;
        background: #6F4E37; color: white;
        border: none; border-radius: 10px;
        font-size: 15px; font-weight: 600;
        font-family: 'Poppins', sans-serif;
        cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center;
        justify-content: center; gap: 8px;
        margin-bottom: 6px;
    }
    .btn-checkout:hover    { background: #5a3d2b; }
    .btn-checkout:disabled { opacity: 0.5; cursor: not-allowed; }
    .btn-clear-cart {
        width: 100%; padding: 9px;
        background: #fee2e2; color: #991b1b;
        border: none; border-radius: 8px;
        font-size: 13px; font-weight: 500;
        font-family: 'Poppins', sans-serif; cursor: pointer;
    }
</style>
@endpush

<div class="pos-layout">

    {{-- LEFT: Menu panel --}}
    <div class="menu-panel">
        <div class="category-tabs">
            <button class="cat-btn active" onclick="filterCategory('all', this)">
                All
            </button>
            @foreach($categories as $cat)
                <button class="cat-btn"
                        onclick="filterCategory({{ $cat->id }}, this)">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <div class="menu-grid">
            @foreach($categories as $cat)
                @foreach($cat->menuItems as $item)
                    <div class="menu-item-card"
                         data-category="{{ $cat->id }}"
                         onclick="addToCart(
                             {{ $item->id }},
                             '{{ addslashes($item->name) }}',
                             {{ $item->price }}
                         )">
                        <div class="menu-item-name">{{ $item->name }}</div>
                        <div class="menu-item-price">
                            &#8369;{{ number_format($item->price, 2) }}
                        </div>
                        <div class="menu-item-cat">{{ $cat->name }}</div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- RIGHT: Cart panel --}}
    <div class="cart-panel">
        <div class="cart-header">
            <i class="fas fa-shopping-cart"></i> Current Order
        </div>

        <div class="cart-items" id="cartItems">
            <div class="cart-empty" id="cartEmpty">
                <i class="fas fa-coffee"></i>
                Add items to start an order
            </div>
        </div>

        <div class="cart-totals">
            <div class="total-row">
                <span>Subtotal</span><span id="subtotal">&#8369;0.00</span>
            </div>
            <div class="total-row">
                <span>Discount</span><span id="discountDisplay">&#8369;0.00</span>
            </div>
            <div class="total-row">
                <span>Tax (12%)</span><span id="taxAmount">&#8369;0.00</span>
            </div>
            <div class="total-row grand">
                <span>TOTAL</span><span id="grandTotal">&#8369;0.00</span>
            </div>
        </div>

        <div class="checkout-form">
            <input type="text" id="customerName" class="pos-input"
                   placeholder="Customer name (optional)">

            <div class="checkout-row">
                <select id="paymentMethod" class="pos-input"
                        onchange="updateChange()">
                    <option value="cash">Cash</option>
                    <option value="gcash">GCash</option>
                    <option value="maya">Maya</option>
                    <option value="card">Card</option>
                </select>
                <input type="number" id="discountInput" class="pos-input"
                       placeholder="Discount ₱" min="0" value="0"
                       oninput="recalculate()">
            </div>

            <div id="tenderWrap">
                <input type="number" id="amountTendered" class="pos-input"
                       placeholder="Amount tendered ₱"
                       min="0" oninput="updateChange()">
            </div>

            <div class="change-box" id="changeBox"></div>

            <button class="btn-checkout" id="checkoutBtn"
                    onclick="placeOrder()" disabled>
                <i class="fas fa-check-circle"></i> Place Order
            </button>

            <button class="btn-clear-cart" onclick="clearCart()">
                <i class="fas fa-trash"></i> Clear Cart
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const TAX_RATE = 0.12;
let cart = {};

function filterCategory(catId, btn) {
    document.querySelectorAll('.cat-btn').forEach(function(b) {
        b.classList.remove('active');
    });
    btn.classList.add('active');
    document.querySelectorAll('.menu-item-card').forEach(function(card) {
        card.style.display = (catId === 'all' || card.dataset.category == catId) ? '' : 'none';
    });
}

function addToCart(id, name, price) {
    if (cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = { id: id, name: name, price: price, qty: 1 };
    }
    renderCart();
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    recalculate();
    renderCart();
}

function removeItem(id) {
    delete cart[id];
    recalculate();
    renderCart();
}

function clearCart() {
    cart = {};
    recalculate();
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const empty     = document.getElementById('cartEmpty');
    const keys      = Object.keys(cart);

    if (keys.length === 0) {
    container.innerHTML = '';
    container.appendChild(empty);
    document.getElementById('checkoutBtn').disabled = true;

    document.getElementById('subtotal').innerHTML        = '&#8369;0.00';
    document.getElementById('discountDisplay').innerHTML = '&#8369;0.00';
    document.getElementById('taxAmount').innerHTML       = '&#8369;0.00';
    document.getElementById('grandTotal').innerHTML      = '&#8369;0.00';

    document.getElementById('amountTendered').value = '';
    document.getElementById('changeBox').style.display = 'none';
    recalculate();
    return; 
}

    var html = '';
    keys.forEach(function(id) {
        var item  = cart[id];
        var total = (item.price * item.qty).toFixed(2);
        html += '<div class="cart-item">'
              +   '<div class="cart-item-name">' + item.name + '</div>'
              +   '<div class="qty-wrap">'
              +     '<button class="qty-btn" onclick="changeQty(' + id + ',-1)">&#8722;</button>'
              +     '<span class="qty-num">' + item.qty + '</span>'
              +     '<button class="qty-btn" onclick="changeQty(' + id + ',1)">+</button>'
              +   '</div>'
              +   '<div class="cart-item-total">&#8369;' + total + '</div>'
              +   '<button class="remove-btn" onclick="removeItem(' + id + ')">'
              +     '<i class="fas fa-times"></i>'
              +   '</button>'
              + '</div>';
    });

    container.innerHTML = html;
    document.getElementById('checkoutBtn').disabled = false;
    recalculate();
}

function recalculate() {
    var subtotal = 0;
    Object.values(cart).forEach(function(item) {
        subtotal += Number(item.price) * Number(item.qty);
    });
    
    var discount = parseFloat(document.getElementById('discountInput').value) || 0;
    var taxable  = subtotal - discount;
    var tax      = taxable > 0 ? taxable * TAX_RATE : 0;
    var total    = taxable > 0 ? taxable + tax : 0;

    var total = subtotal - discount;
    if (total < 0) total = 0;

    document.getElementById('subtotal').innerHTML       = '&#8369;' + subtotal.toFixed(2);
    document.getElementById('discountDisplay').innerHTML = '&#8369;' + discount.toFixed(2);
    document.getElementById('taxAmount').innerHTML      = '&#8369;' + tax.toFixed(2);
    document.getElementById('grandTotal').innerHTML     = '&#8369;' + total.toFixed(2);
  
    updateChange();
}

function updateChange() {
    var method    = document.getElementById('paymentMethod').value;
    var changeBox = document.getElementById('changeBox');
    var tenderWrap= document.getElementById('tenderWrap');

    tenderWrap.style.display = method === 'cash' ? '' : 'none';

    if (method !== 'cash') {
        changeBox.style.display = 'none';
        return;
    }

    var totalText = document.getElementById('grandTotal').textContent;
    var total     = parseFloat(totalText.replace('₱','').replace(/,/g,'')) || 0;
    var tendered  = parseFloat(document.getElementById('amountTendered').value) || 0;

    if (total < 0) total = 0;
    if (tendered <= 0) {
        changeBox.style.display = 'none';
        return;
    }

    var change = tendered - total;
    changeBox.style.display = 'block';

    if (change >= 0) {
        changeBox.className   = 'change-box change-ok';
        changeBox.textContent = 'Change: &#8369;' + change.toFixed(2);
        changeBox.innerHTML   = 'Change: &#8369;' + change.toFixed(2);
    } else {
        changeBox.className   = 'change-box change-bad';
        changeBox.innerHTML   = '&#9888; Short by &#8369;' + Math.abs(change).toFixed(2);
    }
}

async function placeOrder() {
    var keys = Object.keys(cart);
    if (keys.length === 0) return;

    var btn = document.getElementById('checkoutBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    var items = keys.map(function(id) {
        return {
            menu_item_id:  parseInt(id),
            quantity:      cart[id].qty,
            customization: '',
        };
    });

    var payload = {
        customer_name:   document.getElementById('customerName').value,
        payment_method:  document.getElementById('paymentMethod').value,
        amount_tendered: document.getElementById('amountTendered').value || null,
        discount_amount: document.getElementById('discountInput').value  || 0,
        items:           items,
    };

    try {
        var response = await fetch('{{ route("cashier.orders.store") }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(payload),
        });

        var data = await response.json();

        if (data.success) {
            window.location.href = data.receipt_url;
        } else {
            alert('Error placing order. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Place Order';
        }
    } catch (error) {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Place Order';
    }
}
</script>
@endpush