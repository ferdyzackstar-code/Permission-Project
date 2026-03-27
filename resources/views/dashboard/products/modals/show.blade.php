<div class="modal fade" id="modalShowProduct{{ $product->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white text-center d-block">
                <h5 class="modal-title w-100 font-weight-bold">DETAIL PRODUCT</h5>
            </div>
            <div class="modal-body p-0 text-left">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th class="bg-light" width="40%">NAMA PRODUK</th>
                        <td>{{ $product->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">FOTO PRODUK</th>
                        <td>
                            @if ($product->image)
                                <img src="{{ asset('storage/uploads/products/' . $product->image) }}" width="150"
                                    class="img-thumbnail shadow-sm">
                            @else
                                <span class="text-muted small italic text-danger">Belum ada foto</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">SUPPLIER</th>
                        <td>{{ $product->supplier->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">STATUS</th>
                        <td>
                            <span class="badge badge-{{ $product->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">STOK</th>
                        <td>{{ $product->stock ?? 0 }} Pcs</td>
                    </tr>
                    <tr>
                        <th class="bg-light">KATEGORI</th>
                        <td>
                            {{ $product->category->parent->name ?? '' }}
                            <i class="fa fa-angle-right mx-1"></i>
                            {{ $product->category->name ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">CABANG</th>
                        <td>{{ $product->outlet->name ?? 'Tidak ada cabang' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">HARGA</th>
                        <td>Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">DETAIL</th>
                        <td>{{ $product->detail }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
