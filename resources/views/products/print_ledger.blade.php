<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Stok - {{ $product->sku }}</title>
    <style>
        /* CETAK A4 PORTRAIT */
        @page { size: A4 portrait; margin: 15mm; }

        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #222; margin: 0; padding: 0; }

        .header { border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
        .company-name { font-size: 20px; font-weight: bold; font-family: 'Arial Black', sans-serif; color: #1e40af;}

        .doc-title { font-size: 18px; font-weight: bold; color: #222; text-align: right; }
        .doc-subtitle { font-size: 11px; font-weight: normal; color:#555;}

        .info-grid { display: flex; justify-content: space-between; margin-bottom: 20px; background-color: #f8fafc; padding: 10px 15px; border: 1px solid #e2e8f0;}

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; }
        th { background-color: #1e40af; color:#fff; text-align: center; font-size: 11px; text-transform: uppercase;}

        .center { text-align: center; }
        .right { text-align: right; }

        .row-in { background-color: #ecfdf5; } /* Hijau Muda */
        .row-out { background-color: #fff1f2; } /* Merah Muda */
        .row-adj { background-color: #f8fafc; } /* Abu-abu Muda */

        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <button class="no-print" onclick="window.print()" style="margin-bottom: 20px; padding: 10px 15px; cursor: pointer; background: #1e40af; color: #fff; border:none; border-radius: 4px; font-weight: bold;">🖨️ Cetak Laporan (PDF)</button>

    <div class="header">
        <div>
            <div class="company-name">PT. BERLIAN MANDIRI PERKASA</div>
        </div>
        <div class="doc-title">{{ $reportTitle }}</div>
    </div>

    <div class="info-grid">
        <div>
            <p style="margin: 0; font-size: 13px;">Nama Barang: <b style="font-size: 16px;">{{ $product->name }}</b></p>
            <p style="margin: 3px 0 0 0;">SKU: <b>{{ $product->sku }}</b> | Kategori: <b>{{ $product->category ?: '-' }}</b></p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0;">Waktu Cetak:</p>
            <p style="margin: 2px 0 0 0; font-weight: bold;">{{ $dateExport }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 15%;">No. Dokumen</th>
                <th style="width: 25%;">Keterangan / Relasi</th>
                <th style="width: 10%;">Masuk</th>
                <th style="width: 10%;">Keluar</th>
                <th style="width: 10%;">Saldo Sisa</th>
                <th style="width: 20%; text-align:right;">Harga Ref. Satuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($history as $row)
            <tr class="{{ $row['type'] == 'Barang Masuk (PO)' ? 'row-in' : ($row['type'] == 'Barang Keluar (SO)' ? 'row-out' : 'row-adj') }}">
                <td class="center">{{ $row['date']->format('d/m/Y') }}</td>
                <td class="center font-bold">{{ $row['ref'] }}</td>
                <td>
                    <b>{{ $row['type'] }}</b><br>
                    <span style="font-size: 10px; color: #555;">{{ $row['contact'] }}</span>
                </td>
                <td class="center font-bold" style="color: #059669;">{{ $row['qty_in'] > 0 ? '+'.$row['qty_in'] : '-' }}</td>
                <td class="center font-bold" style="color: #e11d48;">{{ $row['qty_out'] > 0 ? '-'.$row['qty_out'] : '-' }}</td>
                <td class="center font-bold" style="font-size:13px; color:#1e40af;">{{ $row['balance'] }}</td>
                <td class="right">{{ $row['price'] > 0 ? 'Rp '.number_format($row['price'], 0, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="center" style="padding: 20px;">Belum ada pergerakan transaksi untuk produk ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
