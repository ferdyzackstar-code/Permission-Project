let cart = [];
let totalAmount = 0;

// --- 1. FITUR SEARCH & FILTER ---
document
    .getElementById("product-search")
    .addEventListener("input", function (e) {
        filterProducts();
    });

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
            alert("Stok tidak mencukupi!");
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
            alert("Maksimal stok tercapai");
        }
    } else {
        cart.splice(index, 1);
    }
    renderCart();
}

// --- 3. FORMAT RUPIAH (Gaya Contekan) & HITUNG KEMBALIAN ---

const inputFormat = document.getElementById("paid_amount_format");
const inputReal = document.getElementById("paid_amount");

// Listener untuk input uang dibayar
inputFormat.addEventListener("keyup", function (e) {
    // 1. Format tampilan (titik-titik)
    this.value = formatRupiah(this.value);
    
    // 2. Simpan angka aslinya ke input hidden untuk kalkulasi
    inputReal.value = this.value.replace(/\./g, "");
    
    // 3. Jalankan hitung kembalian
    calculateChange();
});

// Gunakan Intl.NumberFormat agar stabil (Standard Modern)
function formatRupiah(angka) {
    if (angka === undefined || angka === null || angka === "") return "";
    
    // Pastikan hanya angka yang diproses (buang titik/Rp sebelumnya)
    let number_string = angka.toString().replace(/[^0-9]/g, "");
    if (!number_string) return "0";

    // Format otomatis ke gaya Indonesia (1.000, 10.000, dsb)
    return new Intl.NumberFormat('id-ID').format(number_string);
}

// Listener Input Bayar agar tidak "Reset"
inputFormat.addEventListener("input", function (e) {
    // Ambil angka murni saja untuk disimpan ke database
    let rawValue = this.value.replace(/[^0-9]/g, "");
    inputReal.value = rawValue;
    
    // Tampilkan ke user dengan format titik yang rapi
    this.value = rawValue ? formatRupiah(rawValue) : "";
    
    calculateChange();
});

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

        // Tambahkan mx-3 agar tombol + dan - tidak menempel
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
        </tr>
    `;
    });

    if (cartCount) cartCount.innerText = `${totalItems} Item`;
    document.getElementById("total-display").innerText = "Rp" + formatRupiah(totalAmount);
    calculateChange();
}

// ... (Bagian filter, addToCart, renderCart tetap sama) ...

// --- 3. FORMAT RUPIAH & HITUNG KEMBALIAN ---
// Pastikan listener hanya satu kali (tadi di kode kamu ada double listener keyup & input)
inputFormat.addEventListener("input", function (e) {
    let rawValue = this.value.replace(/[^0-9]/g, "");
    inputReal.value = rawValue;
    this.value = rawValue ? formatRupiah(rawValue) : "";
    calculateChange();
});

function calculateChange() {
    const method = document.getElementById("payment_method").value;
    
    // Jika transfer, kembalian selalu 0
    if (method === 'transfer') {
        document.getElementById("change_amount").innerText = "Rp0";
        return;
    }

    const paid = parseInt(inputReal.value) || 0;
    const change = paid - totalAmount;
    const displayChange = change > 0 ? formatRupiah(change) : "0";
    document.getElementById("change_amount").innerText = "Rp" + displayChange;
}

// Tambahkan trigger hitung ulang saat ganti metode pembayaran
document.addEventListener('DOMContentLoaded', function() {
    const payment_method = document.getElementById("payment_method");
    const cash_input_group = document.getElementById('cash-input-group');

    function togglePaymentFields() {
        if (payment_method.value === 'transfer') {
            cash_input_group.style.display = 'none';
            inputReal.value = totalAmount; // Isi otomatis dengan total biar validasi lewat
            inputFormat.value = ""; 
        } else {
            cash_input_group.style.display = 'block';
            inputReal.value = "0"; // Reset jika balik ke cash
            inputFormat.value = "";
        }
        calculateChange();
    }

    payment_method.addEventListener('change', togglePaymentFields);
    togglePaymentFields();
});

// --- 4. SUBMIT TRANSAKSI (PERBAIKAN LOGIKA VALIDASI) ---
async function submitTransaction() {
    if (cart.length === 0) return alert("Keranjang masih kosong!");
    
    const method = document.getElementById("payment_method").value;
    const paidValue = parseInt(inputReal.value) || 0;

    // VALIDASI PERBAIKAN:
    // Hanya cek "Uang kurang" JIKA metode pembayarannya adalah CASH
    if (method === 'cash' && paidValue < totalAmount) {
        return alert("Uang bayar kurang!");
    }

    const btn = document.getElementById("btn-submit");
    btn.disabled = true;
    btn.innerText = "MEMPROSES...";

    // Sesuaikan nilai paid_amount yang dikirim ke server
    // Jika transfer, paid_amount dianggap sama dengan total_amount
    const payload = {
        cart: cart,
        payment_method: method,
        total_amount: totalAmount,
        paid_amount: (method === 'transfer') ? totalAmount : paidValue,
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
            alert("Transaksi Berhasil!");
            // Sesuai request sebelumnya: arahkan ke show modal atau reload
            window.location.reload();
        } else {
            alert("Gagal: " + result.message);
            btn.disabled = false;
            btn.innerText = "PROSES TRANSAKSI";
        }
    } catch (error) {
        console.error(error);
        alert("Terjadi kesalahan koneksi ke server.");
        btn.disabled = false;
        btn.innerText = "PROSES TRANSAKSI";
    }
}


