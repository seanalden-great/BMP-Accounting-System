{{-- <!-- resources/views/expenses/kbd_print.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Anggaran Dana (KBD)</title>
    <!-- Kita tidak menggunakan Tailwind agar layout print A4 rapi, kita gunakan murni CSS inline -->
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px 40px;
        }
        .header-company {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .doc-title-container {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .doc-title {
            font-size: 14px;
        }
        .doc-number {
            font-size: 14px;
        }
        .border-line {
            border-top: 1.5px solid #000;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        td {
            padding: 4px 0;
            vertical-align: top;
        }
        .col-no { width: 30px; text-align: center; }
        .col-desc { width: 60%; }
        .col-curr { width: 30px; text-align: left; }
        .col-amount { text-align: right; width: 120px; }

        .total-row td {
            font-weight: bold;
            padding-top: 10px;
        }
        .total-amount-box {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 4px 0;
            text-align: right;
        }
        .total-curr-box {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 4px 0;
        }

        .signatures {
            margin-top: 40px;
            width: 100%;
            text-align: center;
        }
        .signatures table { width: 100%; }
        .signatures td { padding-top: 60px; font-weight: bold; }
        .city-date { text-align: left; margin-bottom: 20px; }

        /* Auto print saat halaman dimuat */
        @media print {
            @page { margin: 15mm; size: A4 portrait; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header-company">PT. BERLIAN MANDIRI PERKASA</div>

    <div class="doc-title-container">
        <div class="doc-title">ANGGARAN DANA</div>
        <div class="doc-number">No. {{ $kbdNo }}</div>
    </div>

    <div class="border-line"></div>

    <div style="font-weight: bold; margin-bottom: 5px;">Keperluan :</div>

    <table>
        <tbody>
            @php $i = 1; @endphp
            @foreach($expenses as $exp)
            <tr>
                <td class="col-no">{{ $i++ }}</td>
                <td class="col-desc">{{ $exp->description }}</td>
                <td class="col-curr">Rp</td>
                <td class="col-amount">{{ number_format($exp->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <!-- Baris Kosong Pemisah Total -->
            <tr><td colspan="4" style="height: 15px;"></td></tr>

            <tr class="total-row">
                <td colspan="2"></td>
                <td class="total-curr-box">Rp</td>
                <td class="total-amount-box">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="city-date">
        Surabaya, {{ $dateStr }}
    </div>

    <div class="signatures">
        <table>
            <tr>
                <td style="text-align: left; font-weight: normal; padding-top:0;">Dibuat Oleh</td>
                <td style="text-align: center; font-weight: normal; padding-top:0;">Mengetahui,</td>
                <td style="text-align: right; font-weight: normal; padding-top:0;">Menyetujui,</td>
            </tr>
            <tr>
                <td style="text-align: left;">{{ $creatorName }}</td>
                <td style="text-align: center;">Melisa</td>
                <td style="text-align: right;">HARIJANTO</td>
            </tr>
        </table>
    </div>

</body>
</html> --}}

<!-- resources/views/expenses/kbd_print.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <!-- Judul Tab Browser Dinamis -->
    <title>{{ $reportTitle ?? 'Anggaran Dana (KBD)' }}</title>
    <!-- Kita tidak menggunakan Tailwind agar layout print A4 rapi, kita gunakan murni CSS inline -->
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px 40px;
        }
        .header-company {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .doc-title-container {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .doc-title {
            font-size: 15px; /* Sedikit dibesarkan agar kata PAJAK/INTERNAL lebih tegas */
        }
        .doc-number {
            font-size: 14px;
            margin-top: 3px;
        }
        .border-line {
            border-top: 1.5px solid #000;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        td {
            padding: 4px 0;
            vertical-align: top;
        }
        .col-no { width: 30px; text-align: center; }
        .col-desc { width: 60%; }
        .col-curr { width: 30px; text-align: left; }
        .col-amount { text-align: right; width: 120px; }

        .total-row td {
            font-weight: bold;
            padding-top: 10px;
        }
        .total-amount-box {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 4px 0;
            text-align: right;
        }
        .total-curr-box {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 4px 0;
        }

        .signatures {
            margin-top: 40px;
            width: 100%;
            text-align: center;
        }
        .signatures table { width: 100%; }
        .signatures td { padding-top: 60px; font-weight: bold; }
        .city-date { text-align: left; margin-bottom: 20px; }

        /* Auto print saat halaman dimuat */
        @media print {
            @page { margin: 15mm; size: A4 portrait; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header-company">PT. BERLIAN MANDIRI PERKASA</div>

    <div class="doc-title-container">
        <!-- PERHATIAN: Memanggil Variabel Judul Dinamis dari Controller -->
        <div class="doc-title">{{ $reportTitle ?? 'ANGGARAN DANA' }}</div>
        <div class="doc-number">No. {{ $kbdNo }}</div>
    </div>

    <div class="border-line"></div>

    <div style="font-weight: bold; margin-bottom: 5px;">Keperluan :</div>

    <table>
        {{-- <tbody>
            @php $i = 1; @endphp
            @foreach($expenses as $exp)
            <tr>
                <td class="col-no">{{ $i++ }}</td>
                <td class="col-desc">{{ $exp->description }}</td>
                <td class="col-curr">Rp</td>
                <td class="col-amount">{{ number_format($exp->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <!-- Baris Kosong Pemisah Total -->
            <tr><td colspan="4" style="height: 15px;"></td></tr>

            <tr class="total-row">
                <td colspan="2"></td>
                <td class="total-curr-box">Rp</td>
                <td class="total-amount-box">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody> --}}

        <tbody>
            @php $i = 1; @endphp
            @foreach($expenses as $exp)
            @php
                // Hitung Netto yang dibayar
                $netPaid = $exp->amount + $exp->ppn_amount - $exp->pph_amount;
            @endphp
            <tr>
                <td class="col-no">{{ $i++ }}</td>
                <td class="col-desc">
                    {{ $exp->description }}

                    <!-- Munculkan rincian jika ada unsur PPN atau PPh -->
                    @if($exp->ppn_amount > 0 || $exp->pph_amount > 0)
                        <div style="font-size: 11px; color: #555; margin-top: 3px; font-weight: normal;">
                            (DPP: Rp {{ number_format($exp->amount, 0, ',', '.') }}
                            @if($exp->ppn_amount > 0) | <span style="color:#059669;">+ PPN: Rp {{ number_format($exp->ppn_amount, 0, ',', '.') }}</span> @endif
                            @if($exp->pph_amount > 0) | <span style="color:#e11d48;">- PPh: Rp {{ number_format($exp->pph_amount, 0, ',', '.') }}</span> @endif
                            )
                        </div>
                    @endif
                </td>
                <td class="col-curr">Rp</td>
                <td class="col-amount">{{ number_format($netPaid, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <!-- Baris Kosong Pemisah Total -->
            <tr><td colspan="4" style="height: 15px;"></td></tr>

            <tr class="total-row">
                <td colspan="2"></td>
                <td class="total-curr-box">Rp</td>
                <td class="total-amount-box">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="city-date">
        Surabaya, {{ $dateStr }}
    </div>

    <div class="signatures">
        <table>
            <tr>
                <td style="text-align: left; font-weight: normal; padding-top:0;">Dibuat Oleh</td>
                <td style="text-align: center; font-weight: normal; padding-top:0;">Mengetahui,</td>
                <td style="text-align: right; font-weight: normal; padding-top:0;">Menyetujui,</td>
            </tr>
            <tr>
                <td style="text-align: left;">{{ $creatorName }}</td>
                <td style="text-align: center;">Melisa</td>
                <td style="text-align: right;">HARIJANTO</td>
            </tr>
        </table>
    </div>

</body>
</html>
