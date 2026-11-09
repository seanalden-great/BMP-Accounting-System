<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Daftar Produk</title>
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
                <th style="width: 100px;">SKU</th>
                <th style="width: 200px;">Nama Produk</th>
                <th style="width: 120px;">Kategori</th>
                <th style="width: 60px;">Satuan</th>
                <th style="width: 120px;">HPP Berjalan</th>
                <th style="width: 80px;">Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $p)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center" style="font-weight:bold;">{{ $p->sku }}</td>
                <td style="font-weight:bold;">{{ $p->name }}</td>
                <td class="center">{{ $p->category ?: '-' }}</td>
                <td class="center">{{ $p->unit ?: '-' }}</td>
                <td class="right">Rp {{ number_format($p->hpp, 0, ',', '.') }}</td>
                <td class="center" style="font-weight:bold;">{{ $p->total_stock ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="center">Tidak ada data produk ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
