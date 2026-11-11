<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penawaran Harga - {{ $sale->reference_no ?: '#'.$sale->id }}</title>
    <style>
        /* KERTAS A4 PORTRAIT RESMI */
        @page { size: A4 portrait; margin: 15mm; }

        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #222; margin: 0; padding: 20px; }

        .header { border-bottom: 2px solid #222; padding-bottom: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .company-name { font-size: 22px; font-weight: bold; font-family: 'Arial Black', sans-serif; letter-spacing: 1px; color: #1e3a8a;}
        .company-info { font-size: 11px; margin-top: 5px; line-height: 1.5; color:#555;}

        .doc-title { font-size: 24px; font-weight: bold; letter-spacing: 2px; color: #222; text-align: right; line-height: 1.2; }
        .doc-subtitle { font-size: 13px; font-weight: normal; letter-spacing: 3px; color:#555;}

        .info-grid { display: flex; justify-content: space-between; margin-bottom: 40px; background-color: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;}
        .info-box { width: 48%; line-height: 1.6; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px 12px; }
        th { background-color: #1e3a8a; color:#fff; text-align: center; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;}

        .center { text-align: center; }
        .right { text-align: right; }

        .totals-row td { border-top: 2px solid #1e3a8a; font-weight: bold; padding: 8px 12px;}
        .grand-total { background-color: #f1f5f9; font-size: 16px; color: #1e3a8a;}

        .notes-area { font-size: 11px; color: #555; line-height: 1.5; border-left: 4px solid #1e3a8a; padding-left: 15px; margin-bottom: 50px;}

        .signatures { display: flex; justify-content: flex-end; margin-top: 50px; text-align: center; }
        .sign-box { width: 35%; }
        .sign-line { border-top: 1px solid #222; margin-top: 80px; width: 90%; margin-left: auto; margin-right: auto; padding-top: 5px; font-weight: bold; font-size: 12px;}

        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <!-- Tombol Cetak -->
    <button class="no-print" onclick="window.print()" style="margin-bottom: 25px; padding: 10px 20px; cursor: pointer; background: #1e3a8a; color: #fff; border:none; border-radius: 6px; font-weight: bold; font-size:14px;">🖨️ Cetak PDF / Kirim ke Klien</button>

    <div class="header">
        <div>
            <div class="company-name">PT. BERLIAN MANDIRI PERKASA</div>
            <div class="company-info">Jl. Contoh Alamat Perusahaan No. 123, Kota Anda<br>Telp: (021) 1234-5678 | Email: admin@berlianmandiri.com</div>
        </div>
        <div class="doc-title">QUOTATION<br><span class="doc-subtitle">PENAWARAN HARGA</span></div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <p style="margin: 0 0 5px 0; font-size: 11px; color:#555; text-transform:uppercase; font-weight:bold;">Penawaran Untuk:</p>
            <p style="margin: 0; font-size: 16px; color:#111;"><b>{{ $sale->contact->name }}</b></p>
            <p style="margin: 5px 0;">{{ $sale->contact->address ?: 'Alamat tidak tercatat' }}</p>
            <p style="margin: 0;">Telp: {{ $sale->contact->phone ?: '-' }}</p>
        </div>
        <div class="info-box" style="text-align: right;">
            <p style="margin: 0;">
                <span style="color:#555;">No. Dokumen:</span> <b style="font-size:14px;">{{ $sale->reference_no ?: '#'.$sale->id }}</b><br><br>
                <span style="color:#555;">Tanggal:</span> <b>{{ \Carbon\Carbon::parse($sale->date)->translatedFormat('d F Y') }}</b><br>
                <span style="color:#555;">Masa Berlaku:</span> <b>{{ \Carbon\Carbon::parse($sale->due_date)->translatedFormat('d F Y') }}</b>
            </p>
        </div>
    </div>

    <p style="margin-bottom: 12px; color: #333;">Dengan hormat,<br>Bersama ini kami sampaikan penawaran harga untuk barang-barang berikut:</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 40%;">Deskripsi Barang</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 20%;">Harga Satuan</th>
                <th style="width: 10%;">PPN</th>
                <th style="width: 25%; text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $dppTotal = 0; @endphp
            @foreach($sale->items as $index => $item)
            @php $dppItem = $item->quantity * $item->price; $dppTotal += $dppItem; @endphp
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td><b style="color:#111;">{{ $item->product->name ?? 'Produk tidak dikenal' }}</b><br><span style="font-size:10px; color:#666;">SKU: {{ $item->product_sku }}</span></td>
                <td class="center font-bold">{{ number_format($item->quantity, 0, ',', '.') }} {{ $item->product->unit ?? 'Pcs' }}</td>
                <td class="right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="center">{{ $item->tax_rate }}%</td>
                <td class="right font-bold" style="color:#111;">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tbody>
            <tr class="totals-row">
                <td colspan="4" class="right" style="border:none;">Total Dasar Pengenaan Pajak (DPP)</td>
                <td colspan="2" class="right">Rp {{ number_format($dppTotal, 0, ',', '.') }}</td>
            </tr>
            <tr class="totals-row">
                <td colspan="4" class="right" style="border:none;">Total Pajak (PPN)</td>
                <td colspan="2" class="right">Rp {{ number_format($sale->total_tax, 0, ',', '.') }}</td>
            </tr>
            <tr class="totals-row grand-total">
                <td colspan="4" class="right" style="border:none;">GRAND TOTAL</td>
                <td colspan="2" class="right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="notes-area">
        <b>Syarat dan Ketentuan:</b><br>
        1. Harga yang tertera di atas dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya jika melewati masa berlaku.<br>
        2. Ketersediaan stok (inden/ready) bergantung pada saat konfirmasi pemesanan dilakukan (Purchase Order diterbitkan).<br>
        3. Pembayaran penuh atau Down Payment (DP) dilakukan ke rekening resmi perusahaan.
    </div>

    <div class="signatures">
        <div class="sign-box">
            <p style="margin: 0; color:#555;">Hormat Kami,</p>
            <div class="sign-line">Tim Sales / Pemasaran</div>
        </div>
    </div>
</body>
</html>
