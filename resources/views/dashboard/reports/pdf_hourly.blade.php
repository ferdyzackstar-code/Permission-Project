<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi PetShop</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #000;
            letter-spacing: 2px;
        }

        .info-filter {
            margin-bottom: 15px;
            font-style: italic;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            border: 1px solid #ccc;
            padding: 10px 5px;
        }

        td {
            border: 1px solid #ccc;
            padding: 8px 5px;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .footer-table {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .badge-status {
            padding: 3px 7px;
            border-radius: 4px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .bg-primary {
            background-color: #007bff !important;
            color: #ffffff !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
            color: #ffffff !important;
        }

        .text-white {
            color: #ffffff !important;
        }

        tr.bg-primary th {
            background-color: #007bff !important;
            color: #ffffff !important;
        }

        tr.bg-warning td {
            background-color: #ffc107 !important;
            color: #ffffff !important;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="" alt="">
        <h2>ANDA PETSHOP</h2>
        <p style="margin: 5px 0;">Jl. Raya Pisangan, Tambun Utara, Bekasi</p>
        <p style="margin: 0; font-weight: bold;">LAPORAN TRANSAKSI PER-JAM</p>
    </div>

    <div class="info-filter">
        Periode: <strong>{{ date('d M Y', strtotime($startDate)) }}</strong> s/d
        <strong>{{ date('d M Y', strtotime($endDate)) }}</strong><br>
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr class="bg-primary text-white">
                <th width="5%">No</th>
                <th width="15%">Waktu</th>
                <th width="20%">Nama Kasir</th>
                <th width="15%">Status</th>
                <th width="15%">Metode</th>
                <th width="30%">Estimasi Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalKeseluruhan = 0; @endphp
            @foreach ($orders as $index => $order)
                @php $totalKeseluruhan += $order->total_amount; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $order->created_at->format('j F H:i') }}</td>
                    <td class="text-left">{{ $order->user->name ?? 'Unknown' }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ ucfirst(optional($order->payment)->payment_method) ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-table bg-warning text-white">
                <td colspan="5" class="text-right">ESTIMASI TOTAL KEUNTUNGAN:</td>
                <td class="text-right">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Bekasi, {{ date('d M Y') }}</p>
        <br><br><br>
        <p>( ____________________ )<br>Manager Operasional</p>
    </div>

</body>

</html>
