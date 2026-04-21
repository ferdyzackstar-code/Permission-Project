<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan PetShop</title>
</head>

<body style="font-family: sans-serif; font-size: 10px; color: #333;">

    <div style="text-align: center; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="margin: 0; letter-spacing: 2px;">ANDA PETSHOP</h2>
        <p style="margin: 5px 0;">Jl. Raya Pisangan, Tambun Utara, Bekasi</p>
        {{-- Diubah dari HARIAN ke BULANAN --}}
        <p style="margin: 0; font-weight: bold; text-transform: uppercase;">LAPORAN TRANSAKSI BULANAN</p>
    </div>

    <div style="margin-bottom: 15px;">
        @php
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
        @endphp

        {{-- Menampilkan Nama Bulan dan Tahun di bagian atas --}}
        Bulan: <strong>{{ $start->translatedFormat('F Y') }}</strong><br>
        Periode: {{ $start->translatedFormat('d F Y') }} s/d {{ $end->translatedFormat('d F Y') }}<br>
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr style="color: white;">
                <th rowspan="2" style="background-color: #4472c4; border: 1px solid #fff; padding: 10px 2px;">NO</th>
                <th rowspan="2"
                    style="background-color: #4472c4; border: 1px solid #fff; padding: 10px 2px; width: 20%;">TANGGAL
                </th>
                <th colspan="3" style="background-color: #4472c4; border: 1px solid #fff; padding: 5px;">STATUS</th>
                <th colspan="2" style="background-color: #4472c4; border: 1px solid #fff; padding: 5px;">METODE
                    PEMBAYARAN</th>
                <th rowspan="2" style="background-color: #4472c4; border: 1px solid #fff; padding: 10px 2px;">TOTAL
                    TRANSAKSI</th>
                <th rowspan="2" style="background-color: #4472c4; border: 1px solid #fff; padding: 10px 2px;">
                    ESTIMASI KEUNTUNGAN</th>
            </tr>
            <tr style="color: white; font-size: 9px;">
                <th style="background-color: #2ebf91; border: 1px solid #fff; padding: 5px;">Completed</th>
                <th style="background-color: #ffc107; border: 1px solid #fff; padding: 5px;">Pending</th>
                <th style="background-color: #ee1313; border: 1px solid #fff; padding: 5px;">Cancel</th>
                <th style="background-color: #2ebf91; border: 1px solid #fff; padding: 5px;">Cash</th>
                <th style="background-color: #00c4ce; border: 1px solid #fff; padding: 5px;">Transfer</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tableData as $index => $row)
                <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
                    <td style="border: 1px solid #ccc; padding: 8px 2px;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px 2px; text-align: left; padding-left: 10px;">
                        {{-- Menggunakan format tanggal (hari) --}}
                        {{ \Carbon\Carbon::parse($row['date'])->translatedFormat('d F Y') }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px 2px;">{{ $row['completed'] }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px 2px;">{{ $row['pending'] }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px 2px;">{{ $row['cancelled'] }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px 2px;">{{ $row['cash'] }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px 2px;">{{ $row['transfer'] }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px 2px; font-weight: bold;">
                        {{ $row['total_trx'] }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px 2px; text-align: right; padding-right: 5px;">
                        Rp {{ number_format($row['revenue'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #ffc107; font-weight: bold;">
                <td colspan="2" style="border: 1px solid #ccc; padding: 10px; text-align: center; color: white;">
                    TOTAL BULANAN:
                </td>
                <td style="border: 1px solid #ccc; color: white;">{{ $totals['completed'] }}</td>
                <td style="border: 1px solid #ccc; color: white;">{{ $totals['pending'] }}</td>
                <td style="border: 1px solid #ccc; color: white;">{{ $totals['cancelled'] }}</td>
                <td style="border: 1px solid #ccc; color: white;">{{ $totals['cash'] }}</td>
                <td style="border: 1px solid #ccc; color: white;">{{ $totals['transfer'] }}</td>
                <td style="border: 1px solid #ccc; color: white;">{{ $totals['total_trx'] }}</td>
                <td style="border: 1px solid #ccc; color: white; text-align: right; padding-right: 5px;">
                    Rp {{ number_format($totals['revenue'], 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; text-align: right; padding-right: 20px;">
        <p>Bekasi, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>( ____________________ )</strong><br>Manager Operasional</p>
    </div>

</body>

</html>
