<div class="modal fade" id="modalShowProduct{{ $product->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Nama Produk:</strong> {{ $product->name }}</p>
                <p><strong>Kategori:</strong> {{ $product->category->name ?? '-' }}</p>
                <p><strong>Cabang:</strong> {{ $product->branch_name }}</p>
                <hr>
                <p><strong>Detail:</strong><br>{{ $product->detail }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>