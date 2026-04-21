<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi PetShop</title>
</head>

<body style="font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px;">

    <div style="text-align: center; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="margin: 0; letter-spacing: 2px; font-size: 18px;">ANDA PETSHOP</h2>
        <p style="margin: 5px 0;">Jl. Raya Pisangan, Tambun Utara, Bekasi</p>
        <p style="margin: 0; font-weight: bold; text-transform: uppercase;">Laporan Transaksi Per-Jam</p>
    </div>

    <div style="margin-bottom: 15px; color: #555;">
        Periode: <strong>{{ date('d F Y', strtotime($startDate)) }}</strong> s/d
        <strong>{{ date('d F Y', strtotime($endDate)) }}</strong><br>
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: center; border: 1px solid #ccc;">
        <thead>
            <tr style="color: white;">
                <th rowspan="2"
                    style="background-color: #4472c4; border: 1px solid #ffffff; padding: 12px 5px; vertical-align: middle;">
                    No</th>
                <th rowspan="2"
                    style="background-color: #4472c4; border: 1px solid #ffffff; padding: 12px 5px; vertical-align: middle;">
                    Waktu</th>
                <th colspan="3" style="background-color: #4472c4; border: 1px solid #ffffff; padding: 8px 5px;">
                    Status</th>
                <th colspan="2" style="background-color: #4472c4; border: 1px solid #ffffff; padding: 8px 5px;">
                    Metode Pembayaran</th>
                <th rowspan="2"
                    style="background-color: #4472c4; border: 1px solid #ffffff; padding: 12px 5px; vertical-align: middle;">
                    Total Transaksi</th>
                <th rowspan="2"
                    style="background-color: #4472c4; border: 1px solid #ffffff; padding: 12px 5px; vertical-align: middle;">
                    Estimasi Keuntungan</th>
            </tr>
            <tr style="color: white; font-size: 10px;">
                <th style="background-color: #2ebf91; border: 1px solid #ffffff; padding: 8px 2px;">Completed</th>
                <th style="background-color: #ffc107; border: 1px solid #ffffff; padding: 8px 2px;">Pending</th>
                <th style="background-color: #fb3909; border: 1px solid #ffffff; padding: 8px 2px;">Cancelled</th>
                <th style="background-color: #4facfe; border: 1px solid #ffffff; padding: 8px 2px;">Cash</th>
                <th style="background-color: #00c4ce; border: 1px solid #ffffff; padding: 8px 2px;">Transfer</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tableData as $index => $row)
                <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
                    <td style="border: 1px solid #eee; padding: 8px 5px; color: #777;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #eee; padding: 8px 5px;">
                        <span
                            style="background: #e9ecef; padding: 2px 6px; border-radius: 4px; font-weight: bold; color: #495057;">
                            {{ $row['waktu'] }}
                        </span>
                    </td>
                    <td style="border: 1px solid #eee; padding: 8px 5px;">{{ $row['completed'] }}</td>
                    <td style="border: 1px solid #eee; padding: 8px 5px;">{{ $row['pending'] }}</td>
                    <td style="border: 1px solid #eee; padding: 8px 5px;">{{ $row['cancelled'] }}</td>
                    <td style="border: 1px solid #eee; padding: 8px 5px;">{{ $row['cash'] }}</td>
                    <td style="border: 1px solid #eee; padding: 8px 5px;">{{ $row['transfer'] }}</td>
                    <td style="border: 1px solid #eee; padding: 8px 5px; font-weight: bold; color: #444;">
                        {{ $row['total_trx'] }}</td>
                    <td
                        style="border: 1px solid #eee; padding: 8px 5px; text-align: right; font-weight: bold; color: #28a745; padding-right: 15px;">
                        Rp {{ number_format($row['revenue'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="padding: 30px; color: #999; border: 1px solid #eee;">Data transaksi tidak
                        tersedia untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #ffc107; color: white; font-weight: bold; font-size: 12px;">
                <td colspan="2"
                    style="border: 1px solid #ffc107; padding: 12px 5px; text-align: right; padding-right: 10px; text-transform: uppercase;">
                    Total :</td>
                <td style="border: 1px solid #ffc107; padding: 12px 5px;">{{ $totals['completed'] }}</td>
                <td style="border: 1px solid #ffc107; padding: 12px 5px;">{{ $totals['pending'] }}</td>
                <td style="border: 1px solid #ffc107; padding: 12px 5px;">{{ $totals['cancelled'] }}</td>
                <td style="border: 1px solid #ffc107; padding: 12px 5px;">{{ $totals['cash'] }}</td>
                <td style="border: 1px solid #ffc107; padding: 12px 5px;">{{ $totals['transfer'] }}</td>
                <td style="border: 1px solid #ffc107; padding: 12px 5px;">{{ $totals['total_trx'] }}</td>
                <td style="border: 1px solid #ffc107; padding: 12px 5px; text-align: right; padding-right: 15px;">
                    Rp {{ number_format($totals['revenue'], 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 40px; text-align: right; padding-right: 30px;">
        <p>Bekasi, {{ date('d F Y') }}</p>
        <div style="margin-top: 60px;">
            <p style="margin-bottom: 0; font-weight: bold;">( ____________________ )</p>
            <p style="margin-top: 5px; color: #666;">Manager Operasional</p>
        </div>
    </div>

</body>

</html>
