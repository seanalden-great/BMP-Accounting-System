<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Request for Quotation - {{ $purchase->reference_no ?: '#'.$purchase->id }}</title>
    <style>
        /* KERTAS A4 PORTRAIT RESMI */
        @page { size: A4 portrait; margin: 15mm; }

        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #222; margin: 0; padding: 20px; }

        .header { border-bottom: 2px solid #222; padding-bottom: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .company-name { font-size: 22px; font-weight: bold; font-family: 'Arial Black', sans-serif; letter-spacing: 1px; color: #d97706;}
        .company-info { font-size: 11px; margin-top: 5px; line-height: 1.5; color:#555;}

        .doc-title { font-size: 24px; font-weight: bold; letter-spacing: 2px; color: #222; text-align: right; line-height: 1.2; }
        .doc-subtitle { font-size: 13px; font-weight: normal; letter-spacing: 3px; color:#555;}

        .info-grid { display: flex; justify-content: space-between; margin-bottom: 40px; background-color: #fffbeb; padding: 15px; border-radius: 8px; border: 1px solid #fef3c7;}
        .info-box { width: 48%; line-height: 1.6; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px 12px; }
        th { background-color: #d97706; color:#fff; text-align: center; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;}

        .center { text-align: center; }
        .right { text-align: right; }

        .totals-row td { border-top: 2px solid #d97706; font-weight: bold; padding: 8px 12px;}
        .grand-total { background-color: #fffbeb; font-size: 16px; color: #b45309;}

        .notes-area { font-size: 11px; color: #555; line-height: 1.5; border-left: 4px solid #d97706; padding-left: 15px; margin-bottom: 50px;}

        .signatures { display: flex; justify-content: flex-end; margin-top: 50px; text-align: center; }
        .sign-box { width: 35%; }
        .sign-line { border-top: 1px solid #222; margin-top: 80px; width: 90%; margin-left: auto; margin-right: auto; padding-top: 5px; font-weight: bold; font-size: 12px;}

        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <!-- Tombol Cetak -->
    <button class="no-print" onclick="window.print()" style="margin-bottom: 25px; padding: 10px 20px; cursor: pointer; background: #d97706; color: #fff; border:none; border-radius: 6px; font-weight: bold; font-size:14px;">🖨️ Cetak PDF / Kirim ke Supplier</button>

    <div class="header">
        <div>
            <div class="company-name">PT. BERLIAN MANDIRI PERKASA</div>
            <div class="company-info">Jl. Contoh Alamat Perusahaan No. 123, Kota Anda<br>Telp: (021) 1234-5678 | Email: admin@berlianmandiri.com</div>
        </div>
        <div class="doc-title">REQUEST FOR QUOTATION<br><span class="doc-subtitle">PERMINTAAN PENAWARAN HARGA</span></div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <p style="margin: 0 0 5px 0; font-size: 11px; color:#555; text-transform:uppercase; font-weight:bold;">Permintaan Kepada:</p>
            <p style="margin: 0; font-size: 16px; color:#111;"><b>{{ $purchase->contact->name }}</b></p>
            <p style="margin: 5px 0;">{{ $purchase->contact->address ?: 'Alamat tidak tercatat' }}</p>
            <p style="margin: 0;">Telp: {{ $purchase->contact->phone ?: '-' }}</p>
        </div>
        <div class="info-box" style="text-align: right;">
            <p style="margin: 0;">
                <span style="color:#555;">No. Dokumen:</span> <b style="font-size:14px;">{{ $purchase->reference_no ?: '#'.$purchase->id }}</b><br><br>
                <span style="color:#555;">Tanggal Permintaan:</span> <b>{{ \Carbon\Carbon::parse($purchase->date)->translatedFormat('d F Y') }}</b><br>
                <span style="color:#555;">Batas Waktu Penawaran:</span> <b>{{ \Carbon\Carbon::parse($purchase->due_date)->translatedFormat('d F Y') }}</b>
            </p>
        </div>
    </div>

    <p style="margin-bottom: 12px; color: #333;">Dengan hormat,<br>Bersama ini kami mengajukan permintaan penawaran harga dan ketersediaan stok untuk barang-barang berikut:</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 40%;">Deskripsi Barang</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 20%;">Estimasi Harga</th>
                <th style="width: 10%;">PPN</th>
                <th style="width: 25%; text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $dppTotal = 0; @endphp
            @foreach($purchase->items as $index => $item)
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
                <td colspan="2" class="right">Rp {{ number_format($purchase->total_tax, 0, ',', '.') }}</td>
            </tr>
            <tr class="totals-row grand-total">
                <td colspan="4" class="right" style="border:none;">GRAND TOTAL ESTIMASI</td>
                <td colspan="2" class="right">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="notes-area">
        <b>Syarat dan Ketentuan:</b><br>
        1. Dokumen ini adalah Draf Permintaan (RFQ) dan belum mengikat secara hukum sebagai Purchase Order (PO).<br>
        2. Mohon balas dokumen ini dengan konfirmasi ketersediaan barang dan persetujuan harga.<br>
        3. Jika harga disepakati, kami akan menerbitkan dokumen PO resmi.
    </div>

    <div class="signatures">
        <div class="sign-box">
            <p style="margin: 0; color:#555;">Hormat Kami,</p>
            <div class="sign-line">Bagian Pembelian (Purchasing)</div>
        </div>
    </div>
</body>
</html>
