<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Dashboard - Anda PetShop')</title>

    <link href="{{ asset('asset/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('asset/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/bb8a89d0cb.js" crossorigin="anonymous"></script>

    <style>
        /* ── Global reset ── */
        html,
        body {
            height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Nunito', system-ui, -apple-system, sans-serif !important;
            /* Background abu agar topbar floating card terlihat kontras */
            background-color: #f0f2f8 !important;
        }

        /* ── Layout ── */
        #wrapper {
            display: flex !important;
            overflow: hidden !important;
        }

        #content-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            flex: 1;
            min-width: 0;
            /* margin-left diatur oleh sidebar.blade.php */
        }

        /* ── Content area ──
           Topbar floating pakai top:12px, tinggi 62px.
           Tidak perlu padding-top manual — topbar sticky mengatur sendiri.
           Tapi beri space dari atas supaya topbar tidak overlap konten saat sticky.
        ── */
        #content {
            flex: 1;
            padding-top: 0 !important;
        }

        /* Container konten dapat padding-top setelah topbar */
        #content>.container-fluid {
            padding-top: 1.25rem;
            padding-bottom: 1.5rem;
        }

        /* ── Kill semua style topbar lama dari sb-admin-2 ── */
        .topbar {
            position: unset !important;
            left: unset !important;
            top: unset !important;
            width: unset !important;
            box-shadow: none !important;
        }

        /* ── Footer ── */
        .sticky-footer {
            margin-top: auto;
            background: transparent !important;
            border-top: 1px solid #e3e6f0;
        }

        /* ── Mobile ── */
        @media (max-width: 767.98px) {
            #content-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>

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

            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto py-3">
                        <span class="text-muted small">Copyright &copy; Farel Ferdyawan</span>
                    </div>
                </div>
            </footer>
        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="{{ asset('asset/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('asset/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('asset/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('asset/js/sb-admin-2.min.js') }}"></script>

    @stack('scripts')

    <script>
        $(document).ready(function() {

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            $(document).on('click', '.show_confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Yakin Ingin Menghapus?',
                    text: 'Data yang dihapus tidak bisa dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });

            $(document).on('click', '.btn-update-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: 'Data yang Anda ubah akan diperbarui di sistem.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Update!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#f6c23e',
                    cancelButtonColor: '#858796',
                }).then(result => {
                    if (result.isConfirmed) form[0].submit();
                });
            });

            @if (session('success'))
                (function() {
                    const msg = @json(session('success'));
                    Toast.fire({
                        icon: 'success',
                        title: /hapus|deleted|dihapus/i.test(msg) ? 'Terhapus!' :
                            /update|edit|diperbarui/i.test(msg) ? 'Diperbarui!' :
                            'Berhasil!',
                        text: msg
                    });
                })();
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @json(session('error'))
                });
            @endif

        });
    </script>

</body>

</html>
