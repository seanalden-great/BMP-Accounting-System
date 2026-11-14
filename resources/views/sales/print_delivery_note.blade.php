{{-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $sale->reference_no ?: '#'.$sale->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; padding: 30px; font-size: 13px; color: #333; }
        .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 25px; display: flex; justify-content: space-between; }
        .company-name { font-size: 22px; font-weight: bold; font-family: 'Arial Black', sans-serif; }
        .doc-title { font-size: 24px; font-weight: bold; letter-spacing: 3px; color: #444; text-align: right; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 48%; line-height: 1.6; }
        .info-box strong { display: inline-block; width: 110px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 12px 10px; }
        th { background-color: #f4f4f4; text-align: center; font-size: 12px; text-transform: uppercase; }
        .center { text-align: center; }
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; text-align: center; }
        .sign-box { width: 30%; }
        .sign-line { border-top: 1px solid #000; margin-top: 80px; width: 85%; margin-left: auto; margin-right: auto; padding-top: 5px; font-weight: bold;}
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <!-- Tombol cetak ini akan hilang otomatis saat masuk layar Print/PDF -->
    <button class="no-print" onclick="window.print()" style="margin-bottom: 20px; padding: 10px 15px; cursor: pointer; background: #333; color: #fff; border:none; border-radius: 5px;">🖨️ Cetak Dokumen</button>

    <div class="header">
        <div>
            <div class="company-name">PT. BERLIAN MANDIRI PERKASA</div>
            <div>Jl. Contoh Alamat Perusahaan No. 123, Kota Anda</div>
            <div>Telp: (021) 1234-5678 | Email: admin@berlianmandiri.com</div>
        </div>
        <div class="doc-title">SURAT JALAN<br><span style="font-size: 14px; font-weight: normal; letter-spacing: normal;">(DELIVERY NOTE)</span></div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <p style="margin: 0 0 5px 0;"><strong>Kepada Yth,</strong></p>
            <p style="margin: 0; font-size: 15px;"><b>{{ $sale->contact->name }}</b></p>
            <p style="margin: 5px 0;">{{ $sale->contact->address ?: 'Alamat tidak tercatat' }}</p>
            <p style="margin: 0;">Telp: {{ $sale->contact->phone ?: '-' }}</p>
        </div>
        <div class="info-box" style="text-align: right;">
            <p style="margin: 0;">
                <strong>No. Referensi:</strong> {{ $sale->reference_no ?: '#'.$sale->id }}<br>
                <strong>Tanggal Kirim:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                <strong>No. Polisi / Kendaraan:</strong> .....................................<br>
                <strong>Nama Pengemudi:</strong> .....................................
            </p>
        </div>
    </div>

    <p style="margin-bottom: 12px; font-weight: bold;">Harap diterima dengan baik barang-barang di bawah ini:</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 20%;">Kode SKU</th>
                <th style="width: 50%;">Nama Barang / Deskripsi</th>
                <th style="width: 15%;">Jumlah (Qty)</th>
                <th style="width: 10%;">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center font-mono">{{ $item->product_sku }}</td>
                <td>{{ $item->product->name ?? 'Produk tidak dikenal' }}</td>
                <td class="center" style="font-size: 16px; font-weight: bold;">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                <td class="center">{{ $item->product->unit ?? 'Pcs' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 11px; font-style: italic; color: #555;">Catatan: Surat Jalan ini merupakan bukti sah penyerahan barang. Barang yang sudah dibeli dan diterima dengan baik tidak dapat ditukar atau dikembalikan tanpa perjanjian tertulis sebelumnya. Segala risiko kerusakan/kehilangan setelah serah terima menjadi tanggung jawab pembeli.</p>

    <div class="signatures">
        <div class="sign-box">
            <p>Penerima / Pelanggan,</p>
            <div class="sign-line">( Tanda Tangan & Cap )</div>
        </div>
        <div class="sign-box">
            <p>Pengirim / Supir / Kurir,</p>
            <div class="sign-line">( Nama Terang )</div>
        </div>
        <div class="sign-box">
            <p>Hormat Kami,</p>
            <div class="sign-line">PT. Berlian Mandiri Perkasa</div>
        </div>
    </div>
</body>
</html> --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $sale->reference_no ?: '#'.$sale->id }}</title>
    <style>
        /* PENGATURAN KERTAS A5 LANDSCAPE (Setengah A4) */
        @page { size: A5 landscape; margin: 10mm; }

        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }

        .header { border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: flex-end; }
        .company-name { font-size: 16px; font-weight: bold; font-family: 'Arial Black', sans-serif; }
        .company-info { font-size: 10px; margin-top: 3px; }

        .doc-title { font-size: 18px; font-weight: bold; letter-spacing: 2px; color: #333; text-align: right; line-height: 1.2; }
        .doc-subtitle { font-size: 11px; font-weight: normal; letter-spacing: 1px; }

        .info-grid { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .info-box { width: 48%; line-height: 1.5; }
        .info-box strong { display: inline-block; width: 100px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 6px 8px; }
        th { background-color: #f1f5f9; text-align: center; font-size: 10px; text-transform: uppercase; }

        .center { text-align: center; }

        .notes-area { font-size: 9.5px; font-style: italic; color: #555; line-height: 1.4; }

        .signatures { display: flex; justify-content: space-between; margin-top: 25px; text-align: center; }
        .sign-box { width: 28%; }
        .sign-line { border-top: 1px solid #333; margin-top: 50px; width: 90%; margin-left: auto; margin-right: auto; padding-top: 4px; font-weight: bold; font-size: 10px;}

        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <!-- Tombol Cetak -->
    <button class="no-print" onclick="window.print()" style="margin-bottom: 15px; padding: 8px 12px; cursor: pointer; background: #333; color: #fff; border:none; border-radius: 4px; font-weight: bold;">🖨️ Cetak A5 Landscape</button>

    <!-- LOGIKA EKSTRAK NOMOR SURAT JALAN DARI CATATAN SISTEM -->
    @php
        $sjNumber = '-';
        if (preg_match('/\[No\. SJ: (.*?)\]/', $sale->notes, $matches)) {
            $sjNumber = $matches[1];
        }
    @endphp

    <div class="header">
        <div>
            <div class="company-name">PT. BERLIAN MANDIRI PERKASA</div>
            <div class="company-info">Jl. Contoh Alamat Perusahaan No. 123, Kota Anda<br>Telp: (021) 1234-5678 | Email: admin@berlianmandiri.com</div>
        </div>
        <div class="doc-title">SURAT JALAN<br><span class="doc-subtitle">(DELIVERY NOTE)</span></div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <p style="margin: 0 0 3px 0;"><strong>Kepada Yth,</strong></p>
            <p style="margin: 0; font-size: 13px;"><b>{{ $sale->contact->name }}</b></p>
            <p style="margin: 3px 0;">{{ $sale->contact->address ?: 'Alamat tidak tercatat' }}</p>
            <p style="margin: 0;">Telp: {{ $sale->contact->phone ?: '-' }}</p>
        </div>
        <div class="info-box" style="text-align: right;">
            <p style="margin: 0;">
                <strong>No. Pesanan (SO):</strong> {{ $sale->reference_no ?: '#'.$sale->id }}<br>
                <strong>No. Surat Jalan:</strong> <b>{{ $sjNumber }}</b><br>
                <strong>Tanggal Kirim:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </p>
        </div>
    </div>

    <p style="margin-bottom: 8px; font-weight: bold;">Harap diterima dengan baik barang-barang di bawah ini:</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 22%;">Kode SKU</th>
                <th style="width: 53%;">Nama Barang / Deskripsi</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 10%;">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center font-mono">{{ $item->product_sku }}</td>
                <td style="font-weight: bold;">{{ $item->product->name ?? 'Produk tidak dikenal' }}</td>
                <td class="center" style="font-size: 13px; font-weight: bold;">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                <td class="center">{{ $item->product->unit ?? 'Pcs' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="notes-area">
        Catatan: Surat Jalan ini merupakan bukti sah penyerahan fisik barang. Barang yang sudah dibeli dan diterima dengan baik tidak dapat ditukar atau dikembalikan. Risiko kerusakan/kehilangan setelah serah terima menjadi tanggung jawab pembeli sepenuhnya.
    </div>

    {{-- <div class="signatures">
        <div class="sign-box">
            <p style="margin: 0;">Penerima / Pelanggan,</p>
            <div class="sign-line">( Tanda Tangan & Cap )</div>
        </div>
        <div class="sign-box">
            <p style="margin: 0;">Pengirim / Supir,</p>
            <div class="sign-line">( Nama Terang )</div>
        </div>
        <div class="sign-box">
            <p style="margin: 0;">Hormat Kami,</p>
            <div class="sign-line">PT. Berlian Mandiri Perkasa</div>
        </div>
    </div> --}}

    <div class="signatures">
        <div class="sign-box">
            <p style="margin: 0;">Penerima / Pelanggan,</p>
            <div class="sign-line">( Tanda Tangan & Cap )</div>
        </div>
        <div class="sign-box">
            <p style="margin: 0;">Pengirim / Supir,</p>
            <!-- PERBAIKAN: Menyatukan isian Nama Supir dan Nopol Kendaraan -->
            <div class="sign-line">( Nama Terang / Nomor Polisi )</div>
        </div>
        <div class="sign-box">
            <p style="margin: 0;">Hormat Kami,</p>
            <div class="sign-line">PT. Berlian Mandiri Perkasa</div>
        </div>
    </div>
</body>
</html>
