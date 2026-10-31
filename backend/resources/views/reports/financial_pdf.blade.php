<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - Masjid BGC</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1B5E20;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            color: #1B5E20;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0;
            color: #666;
            font-size: 10px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-table td {
            padding: 3px;
        }
        .summary-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-box th, .summary-box td {
            border: 1px solid #ddd;
            padding: 6px 10px;
        }
        .summary-box th {
            background-color: #E8F5E9;
            color: #1B5E20;
            text-align: left;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 5px 8px;
        }
        .data-table th {
            background-color: #1B5E20;
            color: white;
            font-weight: bold;
            text-align: left;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
            font-size: 9px;
            color: #777;
        }
        .signature-box {
            margin-top: 20px;
            float: right;
            text-align: center;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $masjid_name }}</h1>
        <p>{{ $address }}</p>
        <p><strong>LAPORAN KEUANGAN MASJID</strong></p>
    </div>

    <table class="meta-table">
        <tr>
            <td width="20%"><strong>Periode</strong></td>
            <td width="30%">: {{ $period_from }} s/d {{ $period_to }}</td>
            <td width="20%"><strong>Penyusun</strong></td>
            <td width="30%">: {{ $exporter_name }} ({{ $exporter_role }})</td>
        </tr>
        <tr>
            <td><strong>Filter Transaksi</strong></td>
            <td>: {{ strtoupper($type) }}</td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ $generated_at }}</td>
        </tr>
    </table>

    <table class="summary-box">
        <tr>
            <th>Saldo Awal</th>
            <th>Total Pemasukan (+)</th>
            <th>Total Pengeluaran (-)</th>
            <th>Saldo Akhir</th>
        </tr>
        <tr>
            <td>Rp {{ number_format($opening_balance, 2, ',', '.') }}</td>
            <td style="color: #2E7D32; font-weight: bold;">Rp {{ number_format($total_income, 2, ',', '.') }}</td>
            <td style="color: #C62828; font-weight: bold;">Rp {{ number_format($total_expense, 2, ',', '.') }}</td>
            <td style="color: #1565C0; font-weight: bold;">Rp {{ number_format($closing_balance, 2, ',', '.') }}</td>
        </tr>
    </table>

    <h3>Rincian Transaksi Terverifikasi</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="12%">Tanggal</th>
                <th width="15%">No. Referensi</th>
                <th width="18%">Kategori</th>
                <th>Keterangan</th>
                <th width="14%" class="text-right">Pemasukan</th>
                <th width="14%" class="text-right">Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $trx->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $trx->reference_number ?? '-' }}</td>
                <td>{{ $trx->category ? $trx->category->name : '-' }}</td>
                <td>{{ $trx->description }}</td>
                <td class="text-right">
                    {{ $trx->type === 'income' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}
                </td>
                <td class="text-right">
                    {{ $trx->type === 'expense' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="clear: both;"></div>

    <div class="signature-box">
        <p>Cisoka, {{ date('d F Y') }}<br>Disusun oleh,</p>
        <br><br><br>
        <p><strong>{{ $exporter_name }}</strong><br>{{ $exporter_role }}</p>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis melalui Sistem Informasi Masjid BGC. Saldo akhir dihitung berdasarkan saldo awal ditambah total penerimaan dikurangi total pengeluaran yang terverifikasi.</p>
    </div>
</body>
</html>
