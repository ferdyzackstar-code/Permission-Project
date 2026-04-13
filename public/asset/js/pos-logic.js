let cart = [];
let totalAmount = 0;

// --- 1. FITUR SEARCH & FILTER ---
document
    .getElementById("product-search")
    .addEventListener("input", filterProducts);

document.querySelectorAll(".btn-category").forEach((btn) => {
    btn.addEventListener("click", function () {
        document
            .querySelectorAll(".btn-category")
            .forEach((b) => b.classList.remove("active"));
        this.classList.add("active");
        filterProducts();
    });
});

function filterProducts() {
    const searchTerm = document
        .getElementById("product-search")
        .value.toLowerCase();
    const activeCategory = document.querySelector(".btn-category.active")
        .dataset.category;

    document.querySelectorAll(".product-item").forEach((item) => {
        const name = item.dataset.name;
        const species = item.dataset.species;
        const category = item.dataset.category;

        const matchesSearch =
            name.includes(searchTerm) || species.includes(searchTerm);
        const matchesCategory =
            activeCategory === "all" || category === activeCategory;

        item.style.display =
            matchesSearch && matchesCategory ? "block" : "none";
    });
}

// --- 2. LOGIKA KERANJANG ---
function addToCart(product) {
    const existing = cart.find((item) => item.id === product.id);
    if (existing) {
        if (existing.qty < product.stock) {
            existing.qty++;
        } else {
            Swal.fire("Oops!", "Stok tidak mencukupi!", "warning");
        }
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            qty: 1,
            max: product.stock,
        });
    }
    renderCart();
}

function updateQty(index, delta) {
    const newQty = cart[index].qty + delta;
    if (newQty > 0) {
        if (newQty <= cart[index].max) {
            cart[index].qty = newQty;
        } else {
            Swal.fire("Maksimal", "Maksimal stok tercapai", "info");
        }
    } else {
        cart.splice(index, 1);
    }
    renderCart();
}

// FUNGSI BARU: KOSONGKAN KERANJANG
function clearCart() {
    if (cart.length === 0) return;

    Swal.fire({
        title: "Kosongkan Keranjang?",
        text: "Semua barang di keranjang akan dihapus.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, Kosongkan!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            cart = [];
            renderCart();
            document.getElementById("paid_amount_format").value = "";
            document.getElementById("paid_amount").value = "";
            Swal.fire("Dihapus!", "Keranjang berhasil dikosongkan.", "success");
        }
    });
}

function renderCart() {
    const tbody = document.getElementById("cart-table-body");
    const cartCount = document.getElementById("cart-count");

    tbody.innerHTML = "";
    totalAmount = 0;
    let totalItems = 0;

    cart.forEach((item, index) => {
        let subtotal = item.price * item.qty;
        totalAmount += subtotal;
        totalItems += item.qty;

        tbody.innerHTML += `
        <tr class="border-bottom">
            <td class="p-2" style="width: 50%">
                <div class="fw-bold small text-truncate">${item.name}</div>
                <div class="text-muted tiny">Rp${formatRupiah(Math.floor(item.price))}</div>
            </td>
            <td class="p-2 text-center" style="width: 30%">
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQty(${index}, -1)">-</button>
                    <span class="fw-bold small mx-3">${item.qty}</span> 
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQty(${index}, 1)">+</button>
                </div>
            </td>
            <td class="p-2 text-end fw-bold small" style="width: 20%">
                Rp${formatRupiah(Math.floor(subtotal))}
            </td>
        </tr>`;
    });

    if (cartCount) cartCount.innerText = `${totalItems} Item`;
    document.getElementById("total-display").innerText =
        "Rp" + formatRupiah(totalAmount);
    calculateChange();
}

// --- 3. FORMAT RUPIAH & KEMBALIAN (DIBERSIHKAN DARI DOUBLE LISTENER) ---
const inputFormat = document.getElementById("paid_amount_format");
const inputReal = document.getElementById("paid_amount");

function formatRupiah(angka) {
    if (angka === undefined || angka === null || angka === "") return "";
    let number_string = angka.toString().replace(/[^0-9]/g, "");
    if (!number_string) return "0";
    return new Intl.NumberFormat("id-ID").format(number_string);
}

// Hanya SATU listener untuk input uang
inputFormat.addEventListener("input", function (e) {
    let rawValue = this.value.replace(/[^0-9]/g, "");
    inputReal.value = rawValue;
    this.value = rawValue ? formatRupiah(rawValue) : "";
    calculateChange();
});

function calculateChange() {
    const method = document.getElementById("payment_method").value;

    if (method === "transfer") {
        document.getElementById("change_amount").innerText = "Rp0";
        return;
    }

    const paid = parseInt(inputReal.value) || 0;
    const change = paid - totalAmount;
    const displayChange = change > 0 ? formatRupiah(change) : "0";
    document.getElementById("change_amount").innerText = "Rp" + displayChange;
}

document.addEventListener("DOMContentLoaded", function () {
    const payment_method = document.getElementById("payment_method");
    const cash_input_group = document.getElementById("cash-input-group");

    function togglePaymentFields() {
        if (payment_method.value === "transfer") {
            cash_input_group.style.display = "none";
            inputReal.value = totalAmount;
            inputFormat.value = "";
        } else {
            cash_input_group.style.display = "block";
            inputReal.value = "0";
            inputFormat.value = "";
        }
        calculateChange();
    }

    payment_method.addEventListener("change", togglePaymentFields);
    togglePaymentFields();
});

// --- 4. SUBMIT TRANSAKSI ---
async function submitTransaction() {
    if (cart.length === 0)
        return Swal.fire("Peringatan", "Keranjang masih kosong!", "warning");

    const method = document.getElementById("payment_method").value;
    const paidValue = parseInt(inputReal.value) || 0;

    if (method === "cash" && paidValue < totalAmount) {
        return Swal.fire(
            "Uang Kurang!",
            "Nominal bayar lebih kecil dari total belanja.",
            "error",
        );
    }

    const btn = document.getElementById("btn-submit");
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> MEMPROSES...';

    const payload = {
        cart: cart,
        payment_method: method,
        total_amount: totalAmount,
        paid_amount: method === "transfer" ? totalAmount : paidValue,
    };

    try {
        const response = await fetch(window.posConfig.storeUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": window.posConfig.csrfToken,
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();
        if (result.success) {
            Swal.fire({
                title: "Transaksi Berhasil!",
                text: "No Invoice: " + result.invoice_number,
                icon: "success",
                confirmButtonText: "Lihat Struk",
                allowOutsideClick: false,
            }).then(() => {
                // Langsung redirect ke halaman struk
                window.location.href = result.receipt_url;
            });
        } else {
            Swal.fire("Gagal", result.message, "error");
            btn.disabled = false;
            btn.innerText = "PROSES TRANSAKSI";
        }
    } catch (error) {
        console.error(error);
        Swal.fire("Error", "Terjadi kesalahan koneksi ke server.", "error");
        btn.disabled = false;
        btn.innerText = "PROSES TRANSAKSI";
    }
}
