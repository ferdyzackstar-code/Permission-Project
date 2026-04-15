<!DOCTYPE html>
<html>

<head>
    <title>Laporan Per Jam PetShop</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f8f9fc;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin:0;">ANDA PETSHOP</h2>
        <p style="margin:5px 0;">Laporan Aktivitas Transaksi Per Jam</p>
        <strong>Tanggal: {{ date('d F Y', strtotime($date)) }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th width="20%">Jam</th>
                <th width="40%">Total Transaksi</th>
                <th width="40%">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach ($reportData as $data)
                @if ($data['total_transaksi'] > 0)
                    @php $grandTotal += $data['revenue']; @endphp
                    <tr>
                        <td>{{ $data['hour_label'] }}</td>
                        <td>{{ $data['total_transaksi'] }} Transaksi</td>
                        <td class="text-right">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot style="font-weight: bold; background: #eee;">
            <tr>
                <td colspan="2" class="text-right">TOTAL PENDAPATAN HARI INI:</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
