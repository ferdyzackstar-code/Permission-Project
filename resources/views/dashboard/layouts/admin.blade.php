<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Admin Anda PetShop')</title>

    <link href="{{ asset('asset/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="{{ asset('asset/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        @include('dashboard.layouts.partials.sidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('dashboard.layouts.partials.topbar')

                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Farel Ferdyawan</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('asset/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('asset/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('asset/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('asset/js/sb-admin-2.min.js') }}"></script>
    @stack('scripts')

    <script>
        $(document).ready(function() {
            // --- 1. SETINGAN TOAST GLOBAL ---
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // --- 2. KONFIRMASI HAPUS ---
            $(document).on('click', '.show_confirm', function(event) {
                event.preventDefault();
                let form = $(this).closest("form");

                Swal.fire({
                    title: "Yakin Ingin Menghapus?",
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            $(document).ready(function() {

                // --- KONFIRMASI EDIT UNTUK SEMUA MODAL (Product, Supplier, dll) ---
                // Kita gunakan delegated event agar tombol di dalam modal tetap terbaca
                $(document).on('click', '.btn-update-confirm', function(e) {
                    e.preventDefault();

                    // Ambil form terdekat dari tombol yang diklik
                    let form = $(this).closest('form');

                    Swal.fire({
                        title: "Simpan Perubahan?",
                        text: "Data yang Anda ubah akan diperbarui di sistem.",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Update!",
                        cancelButtonText: "Batal",
                        confirmButtonColor: "#f6c23e", // Warna kuning/warning agar eye-catching
                        cancelButtonColor: "#858796",
                        reverseButtons: true // Opsional: tombol 'Batal' di kiri, 'Update' di kanan
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit form asli
                            form[0].submit();
                        }
                    });
                });

            });

            // --- 4. NOTIFIKASI SETELAH BERHASIL ---
            @if (session('success'))
                let msg = "{{ session('success') }}";
                if (msg.includes('hapus') || msg.includes('deleted') || msg.includes('dihapus')) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: msg
                    });
                } else if (msg.includes('update') || msg.includes('edit') || msg.includes('diperbarui')) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Diperbarui!',
                        text: msg
                    });
                } else {
                    Toast.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: msg
                    });
                }
            @endif
        });
    </script>
</body>

</html>
