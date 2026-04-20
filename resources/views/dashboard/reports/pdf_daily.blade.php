<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Harian PetShop</title>
    <style>
        /* Reset & Base Styles */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
        }

        .sub-header {
            margin-bottom: 20px;
        }

        .sub-header p {
            margin: 2px 0;
            color: #555;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 10px;
            word-wrap: break-word;
        }

        /* Warna Header (Primary - Biru) */
        thead th {
            background-color: #007bff !important;
            color: #ffffff !important;
            text-transform: uppercase;
            font-size: 11px;
            text-align: center;
        }

        /* Warna Footer (Warning - Kuning) */
        .bg-warning-pdf {
            background-color: #ffc107 !important;
            color: #ffffff !important;
        }

        /* Utility Classes */
        .text-white {
            color: #ffffff !important;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Tanda Tangan */
        .ttd-box {
            width: 100%;
            margin-top: 50px;
        }

        .ttd-box table {
            width: 100%;
            border: none;
        }

        .ttd-box td {
            border: none;
            text-align: right;
            padding-right: 50px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>ANDA PETSHOP</h1>
        <p>Jl. Raya Pisangan, Tambun Utara, Bekasi</p>
        <p style="margin: 0; font-weight: bold;">LAPORAN TRANSAKSI HARIAN</p>
    </div>

    <div class="sub-header">
        <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d
            {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
        <p><strong>Dicetak pada:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="42%" class="text-left">Hari, Tanggal</th>
                <th width="20%">Total Transaksi</th>
                <th width="30%" class="text-right">Estimasi Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tableData as $index => $row)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td class="text-left">{{ $row['date_formatted'] }}</td>
                    <td style="text-align: center;">{{ $row['total_trx'] }}</td>
                    <td class="text-right">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right bg-warning-pdf text-white font-bold">
                    ESTIMASI TOTAL KEUNTUNGAN :
                </td>
                <td class="text-right bg-warning-pdf text-white font-bold">
                    Rp {{ number_format($totalKeuntunganKeseluruhan, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd-box">
        <table>
            <tr>
                <td>
                    <p>Bekasi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <br><br><br><br>
                    <p><strong>( ______________________ )</strong></p>
                    <p>Manager Operasional</p>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
