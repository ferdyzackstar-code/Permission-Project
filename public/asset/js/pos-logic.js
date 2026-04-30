/**
 * pos-logic.js — Anda Petshop POS
 * Compatible dengan pos.blade.php versi baru
 */

let cart = [];
let totalAmount = 0;

// ── SEARCH ──────────────────────────────────────────────────────
document.getElementById('product-search').addEventListener('input', function () {
    const keyword = this.value.toLowerCase().trim();
    const items   = document.querySelectorAll('.product-item');
    const empty   = document.getElementById('search-empty');
    let visible   = 0;

    items.forEach(item => {
        const match = item.dataset.name.includes(keyword);
        item.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    empty.style.display = (visible === 0 && keyword !== '') ? 'flex' : 'none';
});

// ── ADD TO CART ──────────────────────────────────────────────────
function addToCart(product) {
    const existing = cart.find(i => i.id === product.id);

    if (existing) {
        if (existing.qty < product.stock) {
            existing.qty++;
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Stok Habis',
                text: `Stok ${product.name} hanya ${product.stock} unit.`,
                confirmButtonColor: '#1565C0',
                timer: 2000,
                showConfirmButton: false,
            });
            return;
        }
    } else {
        cart.push({
            id:    product.id,
            name:  product.name,
            price: product.price,
            image: product.image,
            qty:   1,
            max:   product.stock,
        });
    }

    // Animasi badge
    const badge = document.getElementById('cart-count');
    badge.classList.remove('pop');
    void badge.offsetWidth; // reflow
    badge.classList.add('pop');

    renderCart();
}

// ── UPDATE QTY ───────────────────────────────────────────────────
function updateQty(index, delta) {
    const newQty = cart[index].qty + delta;

    if (newQty <= 0) {
        cart.splice(index, 1);
    } else if (newQty > cart[index].max) {
        Swal.fire({
            icon: 'info',
            title: 'Batas Stok',
            text: `Maksimal ${cart[index].max} unit.`,
            confirmButtonColor: '#1565C0',
            timer: 1800,
            showConfirmButton: false,
        });
        return;
    } else {
        cart[index].qty = newQty;
    }

    renderCart();
}

// ── CLEAR CART ───────────────────────────────────────────────────
function clearCart() {
    if (cart.length === 0) return;

    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: 'Semua produk di keranjang akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C62828',
        cancelButtonColor: '#78909C',
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (result.isConfirmed) {
            cart = [];
            document.getElementById('paid_amount_format').value = '';
            document.getElementById('paid_amount').value = '0';
            renderCart();
        }
    });
}

// ── RENDER CART ──────────────────────────────────────────────────
function renderCart() {
    const wrap       = document.getElementById('cart-table-body');
    const emptyState = document.getElementById('cart-empty-state');
    const badge      = document.getElementById('cart-count');

    wrap.innerHTML = '';
    totalAmount    = 0;
    let totalItems = 0;

    if (cart.length === 0) {
        emptyState.style.display = 'flex';
        badge.textContent = '0 Item';
        document.getElementById('total-display').textContent = 'Rp0';
        calculateChange();
        return;
    }

    emptyState.style.display = 'none';

    cart.forEach((item, index) => {
        const subtotal  = item.price * item.qty;
        totalAmount    += subtotal;
        totalItems     += item.qty;

        const imgUrl = item.image
            ? `${window.posConfig.assetUrl}/${item.image}`
            : `${window.posConfig.assetUrl}/default-product.jpg`;

        wrap.innerHTML += `
        <div class="cart-item">
            <img class="cart-item-img"
                 src="${imgUrl}"
                 alt="${item.name}"
                 onerror="this.src='${window.posConfig.assetUrl}/default-product.jpg'">
            <div class="cart-item-info">
                <div class="cart-item-name" title="${item.name}">${item.name}</div>
                <div class="cart-item-price">Rp${formatRupiah(item.price)}</div>
            </div>
            <div class="qty-ctrl">
                <button class="qty-btn minus" onclick="updateQty(${index}, -1)">
                    <i class="fas fa-minus" style="font-size:.6rem;"></i>
                </button>
                <span class="qty-num">${item.qty}</span>
                <button class="qty-btn plus" onclick="updateQty(${index}, 1)">
                    <i class="fas fa-plus" style="font-size:.6rem;"></i>
                </button>
            </div>
            <div class="cart-item-subtotal">Rp${formatRupiah(subtotal)}</div>
        </div>`;
    });

    badge.textContent = `${totalItems} Item`;
    document.getElementById('total-display').textContent = 'Rp' + formatRupiah(totalAmount);
    calculateChange();
}

// ── FORMAT & KEMBALIAN ───────────────────────────────────────────
function formatRupiah(angka) {
    if (!angka && angka !== 0) return '0';
    return new Intl.NumberFormat('id-ID').format(Math.floor(angka));
}

const inputFormat = document.getElementById('paid_amount_format');
const inputReal   = document.getElementById('paid_amount');

inputFormat.addEventListener('input', function () {
    const raw = this.value.replace(/[^0-9]/g, '');
    inputReal.value = raw || '0';
    this.value = raw ? formatRupiah(raw) : '';
    calculateChange();
});

function calculateChange() {
    const method = document.getElementById('payment_method').value;
    if (method === 'transfer') {
        document.getElementById('change_amount').textContent = 'Rp0';
        return;
    }
    const paid   = parseInt(inputReal.value) || 0;
    const change = paid - totalAmount;
    document.getElementById('change_amount').textContent =
        'Rp' + (change > 0 ? formatRupiah(change) : '0');
}

// ── SUBMIT TRANSAKSI ─────────────────────────────────────────────
async function submitTransaction() {
    if (cart.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Keranjang Kosong',
            text: 'Tambahkan produk terlebih dahulu.',
            confirmButtonColor: '#1565C0',
        });
        return;
    }

    const method    = document.getElementById('payment_method').value;
    const paidValue = parseInt(inputReal.value) || 0;

    if (method === 'cash' && paidValue < totalAmount) {
        Swal.fire({
            icon: 'error',
            title: 'Uang Kurang!',
            text: `Kurang Rp${formatRupiah(totalAmount - paidValue)} dari total belanja.`,
            confirmButtonColor: '#1565C0',
        });
        return;
    }

    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>MEMPROSES...';

    const payload = {
        cart:           cart,
        payment_method: method,
        total_amount:   totalAmount,
        paid_amount:    method === 'transfer' ? totalAmount : paidValue,
    };

    try {
        const response = await fetch(window.posConfig.storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  window.posConfig.csrfToken,
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (result.success) {
            window.location.href =
                result.receipt_url +
                '?status=success&invoice=' +
                result.invoice_number;
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Transaksi Gagal',
                text: result.message,
                confirmButtonColor: '#1565C0',
            });
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>PROSES TRANSAKSI';
        }
    } catch (err) {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Koneksi Error',
            text: 'Terjadi kesalahan koneksi ke server.',
            confirmButtonColor: '#1565C0',
        });
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>PROSES TRANSAKSI';
    }
}