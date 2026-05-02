{{-- ╔══════════════════════════════════════════════════════╗
     ║  MODAL — SHOW / DETAIL PRODUCT                       ║
     ╚══════════════════════════════════════════════════════╝ --}}

@php
    $showPath = 'storage/uploads/products/' . $product->image;
    $showUrl =
        $product->image && file_exists(public_path($showPath))
            ? asset($showPath)
            : asset('storage/uploads/products/default-product.jpg');

    $isActive = $product->status === 'active';
    $stock = $product->stock ?? 0;
    $stockClass = $stock === 0 ? 'stock-zero' : ($stock <= 5 ? 'stock-low' : 'stock-ok');
    $stockIcon = $stock === 0 ? 'fa-times-circle' : ($stock <= 5 ? 'fa-exclamation-circle' : 'fa-check-circle');
@endphp

<div class="modal fade modal-product" id="modalShowProduct{{ $product->id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px" role="document">
        <div class="modal-content">

            {{-- ── Header ────────────────────────────────── --}}
            <div class="modal-header-brand" style="background:linear-gradient(135deg,#36b9cc 0%,#258391 100%)">
                <div class="d-flex align-items-center">
                    <div class="modal-icon">
                        <i class="fas fa-eye text-white"></i>
                    </div>
                    <div>
                        <div class="modal-title-text">Detail Produk</div>
                        <div class="modal-subtitle">Informasi lengkap produk</div>
                    </div>
                </div>
                <button type="button" class="close text-white ml-auto" data-dismiss="modal" style="opacity:.8">
                    <span>&times;</span>
                </button>
            </div>

            {{-- ── Body ─────────────────────────────────── --}}
            <div class="modal-body p-0">

                {{-- Product hero section --}}
                <div class="d-flex align-items-center p-4 pb-3"
                    style="background:linear-gradient(135deg,#f8f9fc,#eef0f8);border-bottom:1px solid #e3e6f0">
                    <div class="mr-3 flex-shrink-0">
                        <img src="{{ $showUrl }}" alt="{{ $product->name }}"
                            style="width:90px;height:90px;object-fit:cover;border-radius:.75rem;
                                    border:3px solid #fff;box-shadow:0 6px 20px rgba(54,185,204,.25)">
                    </div>
                    <div>
                        <h5 class="mb-1 font-weight-700" style="color:#2d3748;font-size:1rem">
                            {{ $product->name }}
                        </h5>
                        <div class="d-flex flex-wrap gap-1" style="gap:.35rem">
                            <span class="badge-status {{ $isActive ? 'badge-active' : 'badge-inactive' }}">
                                <i class="fas fa-circle mr-1" style="font-size:.5rem"></i>
                                {{ ucfirst($product->status) }}
                            </span>
                            <span class="badge-status stock-pill {{ $stockClass }}">
                                <i class="fas {{ $stockIcon }}" style="font-size:.65rem"></i>
                                {{ $stock }} Pcs
                            </span>
                        </div>
                        <div class="mt-2" style="font-size:1.1rem;font-weight:800;color:#36b9cc">
                            Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Detail rows --}}
                <div class="px-4 py-3">

                    {{-- Kategori --}}
                    <div class="detail-row">
                        <div class="detail-icon" style="background:rgba(78,115,223,.1);color:#4e73df">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Kategori</div>
                            <div class="detail-value">
                                <span class="cat-badge cat-parent">
                                    {{ $product->category->parent->name ?? '—' }}
                                </span>
                                <i class="fas fa-chevron-right mx-1" style="font-size:.6rem;color:#b7bac7"></i>
                                <span class="cat-badge cat-child">
                                    {{ $product->category->name ?? '—' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="detail-divider"></div>

                    {{-- Harga --}}
                    <div class="detail-row">
                        <div class="detail-icon" style="background:rgba(28,200,138,.1);color:#1cc88a">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Harga Jual</div>
                            <div class="detail-value font-weight-700" style="color:#1cc88a;font-size:.95rem">
                                Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="detail-divider"></div>

                    {{-- Stok --}}
                    <div class="detail-row">
                        <div class="detail-icon" style="background:rgba(246,194,62,.15);color:#d4a017">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Stok Tersedia</div>
                            <div class="detail-value">
                                <span class="stock-pill {{ $stockClass }}">
                                    <i class="fas {{ $stockIcon }}"></i>
                                    {{ $stock }} Pcs
                                    @if ($stock === 0)
                                        — Habis
                                    @elseif($stock <= 5)
                                        — Hampir Habis
                                    @else
                                        — Tersedia
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="detail-divider"></div>

                    {{-- Detail / Deskripsi --}}
                    <div class="detail-row align-items-start">
                        <div class="detail-icon mt-1" style="background:rgba(90,92,105,.1);color:#5a5c69">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Deskripsi Produk</div>
                            <div class="detail-value" style="line-height:1.6;color:#5a5c69">
                                {{ $product->detail ?? '—' }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── Footer ───────────────────────────────── --}}
            <div class="modal-footer-custom justify-content-between">
                <div class="small text-muted">
                    <i class="fas fa-clock mr-1"></i>
                    Diupdate: {{ $product->updated_at ? $product->updated_at->diffForHumans() : '—' }}
                </div>
                <div class="d-flex gap-2" style="gap:.5rem">
                    <button type="button" class="btn-modal-cancel" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Tutup
                    </button>
                    @can('product.edit')
                        <button type="button" class="btn-modal-submit"
                            style="background:linear-gradient(135deg,#f6c23e,#d4a017);box-shadow:0 4px 12px rgba(246,194,62,.35)"
                            data-dismiss="modal" data-toggle="modal" data-target="#modalEditProduct{{ $product->id }}">
                            <i class="fas fa-edit"></i> Edit Produk
                        </button>
                    @endcan
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Inline styles scoped to show modal --}}
<style>
    .detail-row {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: .6rem 0;
    }

    .detail-icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        border-radius: .45rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .75rem;
    }

    .detail-content {
        flex: 1;
        min-width: 0;
    }

    .detail-label {
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #b7bac7;
        margin-bottom: .15rem;
    }

    .detail-value {
        font-size: .84rem;
        color: #2d3748;
        font-weight: 500;
    }

    .detail-divider {
        height: 1px;
        background: linear-gradient(90deg, #e3e6f0 0%, transparent 100%);
        margin: .1rem 0;
    }

    .cat-badge {
        font-size: .72rem;
        font-weight: 700;
        padding: .2em .65em;
        border-radius: 2rem;
        display: inline-block;
    }

    .cat-parent {
        background: rgba(78, 115, 223, .1);
        color: #4e73df;
        border: 1px solid rgba(78, 115, 223, .2);
    }

    .cat-child {
        background: rgba(54, 185, 204, .1);
        color: #258391;
        border: 1px solid rgba(54, 185, 204, .2);
    }
</style>
