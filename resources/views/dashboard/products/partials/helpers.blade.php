<script>

    function previewImageAdvanced(inputId, previewId, wrapId, zoneId) {
        var input = document.getElementById(inputId);
        if (!input || !input.files || !input.files[0]) return;

        var file = input.files[0];

        var maxMB = 2;
        if (file.size > maxMB * 1024 * 1024) {
            Swal.fire({
                icon: 'warning',
                title: 'File Terlalu Besar',
                text: 'Ukuran foto maksimal ' + maxMB + 'MB.',
                confirmButtonColor: '#4e73df',
            });
            input.value = '';
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }

            if (wrapId) {
                var wrap = document.getElementById(wrapId);
                if (wrap) wrap.classList.remove('d-none');
            }

            if (zoneId) {
                var zone = document.getElementById(zoneId);
                if (zone) {
                    zone.style.borderColor = '#1cc88a';
                    zone.style.background = '#f0fff8';
                    zone.querySelector('.upload-icon').style.color = '#1cc88a';
                }
            }
        };
        reader.readAsDataURL(file);
    }

    /* Legacy alias — backward compat dengan kode lama */
    function previewImage(inputId, previewId) {
        previewImageAdvanced(inputId, previewId, null, null);
    }

    /* ══════════════════════════════════════════════════════
       2. REMOVE IMAGE (reset upload zone)
    ══════════════════════════════════════════════════════ */
    function removeImage(inputId, previewId, wrapId, zoneId) {
        var input = document.getElementById(inputId);
        if (input) input.value = '';

        if (wrapId) {
            var wrap = document.getElementById(wrapId);
            if (wrap) wrap.classList.add('d-none');
        }

        if (zoneId) {
            var zone = document.getElementById(zoneId);
            if (zone) {
                zone.style.borderColor = '#d1d3e2';
                zone.style.background = '#f8f9fc';
                var icon = zone.querySelector('.upload-icon');
                if (icon) icon.style.color = '#b7bac7';
            }
        }
    }

    /* ══════════════════════════════════════════════════════
       3. RUPIAH FORMATTER
    ══════════════════════════════════════════════════════ */
    function formatRupiah(angka) {
        var str = angka.replace(/[^,\d]/g, '');
        var split = str.split(',');
        var sisa = split[0].length % 3;
        var rupiah = split[0].substr(0, sisa);
        var ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
        return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    }

    $(document).on('keyup', '.input-rupiah', function() {
        $(this).val(formatRupiah($(this).val()));
    });

    /* Strip dots before submit so backend gets plain integer */
    $(document).on('submit', 'form', function() {
        $(this).find('.input-rupiah').each(function() {
            $(this).val($(this).val().replace(/\./g, ''));
        });
    });

    /* ══════════════════════════════════════════════════════
       4. SUB-CATEGORY FETCH
    ══════════════════════════════════════════════════════ */
    function fetchSubCategories(parentId, targetSelect) {
        if (!parentId) {
            targetSelect.prop('disabled', true).empty()
                .append('<option value="">-- Pilih Species Dulu --</option>');
            return;
        }

        targetSelect.empty()
            .append('<option value=""><i class="fas fa-spinner fa-spin"></i> Memuat kategori...</option>')
            .prop('disabled', true);

        $.ajax({
            url: '/dashboard/get-subcategories/' + parentId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                targetSelect.empty().append('<option value="">-- Pilih Kategori --</option>');
                if (data.length > 0) {
                    $.each(data, function(k, v) {
                        targetSelect.append('<option value="' + v.id + '">' + v.name + '</option>');
                    });
                    targetSelect.prop('disabled', false);
                } else {
                    targetSelect.append('<option value="">Tidak ada sub-kategori</option>');
                }
            },
            error: function(xhr) {
                console.error('fetchSubCategories error:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Kategori',
                    text: 'Terjadi kesalahan saat mengambil data. Coba lagi.',
                    confirmButtonColor: '#4e73df',
                });
                targetSelect.prop('disabled', false).empty()
                    .append('<option value="">-- Error --</option>');
            }
        });
    }

    $(document).on('change', '#species_select', function() {
        fetchSubCategories($(this).val(), $('#category_select'));
    });

    $(document).on('change', '.species-edit', function() {
        var productId = $(this).data('product-id');
        fetchSubCategories($(this).val(), $('#category_edit' + productId));
    });

    /* ══════════════════════════════════════════════════════
       5. DELETE CONFIRMATION (SweetAlert2)
    ══════════════════════════════════════════════════════ */
    $(document).on('click', '.btn-delete-product', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var name = $(this).data('name') || 'produk ini';

        Swal.fire({
            title: 'Hapus Produk?',
            html: 'Kamu akan menghapus <strong>' + name +
                '</strong>.<br><span class="text-muted small">Tindakan ini tidak dapat dibatalkan.</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus!',
            cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
            customClass: {
                popup: 'swal-rounded',
                confirmButton: 'swal-btn-confirm',
                cancelButton: 'swal-btn-cancel',
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });
                form[0].submit();
            }
        });
    });

    /* ══════════════════════════════════════════════════════
       6. UPDATE (EDIT) CONFIRMATION
    ══════════════════════════════════════════════════════ */
    $(document).on('click', '.btn-update-confirm', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Pastikan semua data sudah benar sebelum menyimpan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f6c23e',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-save mr-1"></i> Ya, Update!',
            cancelButtonText: 'Cek Lagi',
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });
                form[0].submit();
            }
        });
    });

    /* ══════════════════════════════════════════════════════
       7. IMPORT FILE HANDLER (drop-zone & input)
    ══════════════════════════════════════════════════════ */
    function handleImportFile(input) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var allowed = ['xlsx', 'xls', 'csv'];
            var ext = file.name.split('.').pop().toLowerCase();
            if (!allowed.includes(ext)) {
                Swal.fire('Format Tidak Valid', 'Hanya file .xlsx, .xls, atau .csv yang diterima.', 'warning');
                input.value = '';
                return;
            }
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSelected').classList.remove('d-none');

            var zone = document.getElementById('dropZone');
            if (zone) {
                zone.style.borderColor = '#1cc88a';
                zone.style.background = '#f0fff8';
            }
        }
    }

    function clearImportFile() {
        document.getElementById('importFile').value = '';
        document.getElementById('fileSelected').classList.add('d-none');
        var zone = document.getElementById('dropZone');
        if (zone) {
            zone.style.borderColor = '#d1d3e2';
            zone.style.background = '#f8f9fc';
        }
    }

    /* ══════════════════════════════════════════════════════
       8. DRAG & DROP (import modal drop-zone)
    ══════════════════════════════════════════════════════ */
    $(document).ready(function() {
        var dropZone = document.getElementById('dropZone');
        if (!dropZone) return;

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#4e73df';
            this.style.background = '#f0f4ff';
        });

        dropZone.addEventListener('dragleave', function() {
            this.style.borderColor = '#d1d3e2';
            this.style.background = '#f8f9fc';
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#d1d3e2';
            this.style.background = '#f8f9fc';
            var fileInput = document.getElementById('importFile');
            if (e.dataTransfer.files[0]) {
                // DataTransfer trick untuk assign files ke input
                try {
                    fileInput.files = e.dataTransfer.files;
                } catch (err) {
                    /* Fallback – sebagian browser read-only .files */
                }
                handleImportFile({
                    files: e.dataTransfer.files
                });
            }
        });
    });

    /* ══════════════════════════════════════════════════════
       9. SWAL CUSTOM STYLES (inject once)
    ══════════════════════════════════════════════════════ */
    (function injectSwalStyles() {
        var style = document.createElement('style');
        style.textContent = `
        .swal2-popup { border-radius: .85rem !important; font-family: inherit !important; }
        .swal2-title  { font-size: 1.15rem !important; color: #2d3748 !important; }
        .swal2-html-container { font-size: .88rem !important; }
        .swal2-confirm, .swal2-cancel {
            border-radius: .5rem !important;
            font-size: .82rem !important;
            font-weight: 700 !important;
            padding: .5rem 1.2rem !important;
        }
    `;
        document.head.appendChild(style);
    }());
</script>
