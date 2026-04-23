<div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="showModalLabel">
                    <i class="fas fa-info-circle mr-1"></i> Detail Pembelian
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%">No. PO</th>
                        <td width="5%">:</td>
                        <td id="show_number" class="font-weight-bold text-primary"></td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>:</td>
                        <td id="show_supplier"></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>:</td>
                        <td id="show_date"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>:</td>
                        <td id="show_status"></td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>:</td>
                        <td id="show_notes"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
