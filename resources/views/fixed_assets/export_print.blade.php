<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Daftar Aset Tetap</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 25px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; font-weight: bold; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background-color: #f1f5f9; text-align: center; font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
        .red { color: #dc2626; }
        .green { color: #047857; }
        .grand-total { background-color: #f8fafc; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <button class="no-print" onclick="window.print()" style="padding:10px; margin-bottom:20px;">🖨️ Cetak Dokumen</button>

    <div class="header">
        <div class="title">PT. BERLIAN MANDIRI PERKASA</div>
        <div class="subtitle">{{ $reportName }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No.</th>
                <th>Nama Aset / Inventaris</th>
                <th style="width: 90px;">Tgl Beli</th>
                <th style="width: 70px;">Umur (Bln)</th>
                <th style="width: 120px;">Harga Beli Awal</th>
                <th style="width: 120px;">Akumulasi Susut</th>
                <th style="width: 120px;">Nilai Buku (Sisa)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBeli = 0; $totalSusut = 0; $totalBuku = 0; @endphp
            @forelse($assets as $index => $ast)
                @php
                    $bookValue = $ast->purchase_price - $ast->accum_depreciation;
                    $totalBeli += $ast->purchase_price;
                    $totalSusut += $ast->accum_depreciation;
                    $totalBuku += $bookValue;
                @endphp
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td style="font-weight:bold;">{{ $ast->name }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($ast->purchase_date)->format('d/m/Y') }}</td>
                <td class="center">{{ $ast->useful_life }}</td>
                <td class="right">Rp {{ number_format($ast->purchase_price, 0, ',', '.') }}</td>
                <td class="right red">Rp {{ number_format($ast->accum_depreciation, 0, ',', '.') }}</td>
                <td class="right green" style="font-weight:bold;">Rp {{ number_format($bookValue, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="center">Tidak ada data aset tetap ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="4" class="right">TOTAL KESELURUHAN</td>
                <td class="right">Rp {{ number_format($totalBeli, 0, ',', '.') }}</td>
                <td class="right red">Rp {{ number_format($totalSusut, 0, ',', '.') }}</td>
                <td class="right green">Rp {{ number_format($totalBuku, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
