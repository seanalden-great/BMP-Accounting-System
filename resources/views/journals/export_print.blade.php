{{-- <!-- resources/views/journals/export_print.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportName }} - {{ $bank->name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px double #000; padding-bottom: 15px; }
        .header h1 { margin: 0 0 5px 0; font-size: 18px; text-transform: uppercase; }
        .header h2 { margin: 0; font-size: 15px; font-weight: normal; }
        .info { margin-bottom: 15px; font-weight: bold; font-size: 13px; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #777; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; text-align: center; text-transform: uppercase; font-size: 11px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .print-btn { margin-bottom: 20px; padding: 10px 20px; cursor: pointer; background: #0f172a; color: #fff; border: none; border-radius: 5px; font-weight: bold; }
        .print-btn:hover { background: #1e293b; }
        @media print {
            @page { margin: 1cm; size: landscape; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <button onclick="window.print()" class="print-btn">🖨️ Cetak Dokumen PDF</button>

    <div class="header">
        <h1>PT. BERLIAN MANDIRI PERKASA</h1>
        <h2>{{ $reportName }}</h2>
    </div>

    <div class="info">
        Rekening Bank &nbsp;&nbsp;&nbsp;&nbsp;: {{ $bank->name }} ({{ $bank->code }}) <br>
        Periode Cetak &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} s/d {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir' }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Tanggal</th>
                <th>Uraian / Keterangan</th>
                <th width="80">Akun Lawan</th>
                <th width="100">Debet (Rp)</th>
                <th width="100">Kredit (Rp)</th>
                <th width="120">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $running = $saldoAwal;
                $no = 1;
            @endphp

            @if($startDate)
            <tr style="background-color: #f9f9f9; font-weight: bold; font-style: italic;">
                <td class="center">-</td>
                <td class="center">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                <td>SALDO AWAL (Pindahan Mutasi Sebelum Tanggal Ini)</td>
                <td class="center">-</td>
                <td></td>
                <td></td>
                <td class="right" style="{{ $running < 0 ? 'color:red;' : '' }}">{{ number_format($running, 0, ',', '.') }}</td>
            </tr>
            @endif

            @foreach($journals as $journal)
                @foreach($journal->entries as $entry)
                    @if($entry->account_code == $bank->code)
                        @php
                            $running += ($entry->debit - $entry->credit);
                            $lawan = $journal->entries->where('account_code', '!=', $bank->code)->first();
                        @endphp
                        <tr>
                            <td class="center">{{ $no++ }}</td>
                            <td class="center">{{ \Carbon\Carbon::parse($journal->date)->format('d/m/Y') }}</td>
                            <td>{{ $journal->description }} {{ $journal->reference_no ? '- '.$journal->reference_no : '' }}</td>
                            <td class="center">{{ $lawan ? $lawan->account_code : '' }}</td>
                            <td class="right">{{ $entry->debit > 0 ? number_format($entry->debit, 0, ',', '.') : '' }}</td>
                            <td class="right">{{ $entry->credit > 0 ? number_format($entry->credit, 0, ',', '.') : '' }}</td>
                            <td class="right font-bold" style="{{ $running < 0 ? 'color:red;' : '' }}">{{ number_format($running, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html> --}}

<!-- resources/views/journals/export_print.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Mutasi Bank</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; font-weight: bold; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background-color: #f1f5f9; text-align: center; font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
        .red { color: #dc2626; }
        .blue { color: #2563eb; }
        .spacer { border: none; background-color: transparent; width: 20px; }
        .info-table { width: 50%; border: none; margin-bottom: 20px; }
        .info-table td { border: none; padding: 3px; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <button class="no-print" onclick="window.print()" style="padding:10px; margin-bottom:20px;">🖨️ Cetak Dokumen</button>

    <div class="header">
        <div class="title">PT. BERLIAN MANDIRI PERKASA</div>
        <div class="subtitle">{{ $reportName }}</div>
    </div>

    <table class="info-table">
        <tr><td>Buku Besar Akun</td><td>:</td><td>{{ $bank->name }} ({{ $bank->code }})</td></tr>
        <tr><td>Periode</td><td>:</td><td>{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} s/d {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir' }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Uraian / Keterangan</th>
                <th>No. AKUN</th>
                <th>DEBET</th>
                <th>KREDIT</th>
                <th>SISA SALDO</th>
                <th class="spacer"></th>
                <th>KETERANGAN / LAWAN</th>
            </tr>
        </thead>
        <tbody>
            @php $runningBalance = $saldoAwal; $rowNumber = 1; @endphp

            @if($startDate)
            <tr style="background-color: #eff6ff;">
                <td class="center">-</td>
                <td class="center">{{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }}</td>
                <td style="font-weight:bold;">SALDO AWAL (Pindahan)</td>
                <td></td><td></td><td></td>
                <td class="right font-bold {{ $runningBalance < 0 ? 'red' : 'blue' }}">
                    {{ $runningBalance < 0 ? '(Rp ' . number_format(abs($runningBalance), 0, ',', '.') . ')' : 'Rp ' . number_format($runningBalance, 0, ',', '.') }}
                </td>
                <td class="spacer"></td>
                <td class="center" style="font-style:italic; color:#555;">Akumulasi historis</td>
            </tr>
            @endif

            @foreach($journals as $journal)
                @foreach($journal->entries as $entry)
                    @php
                        $isBankRow = ($entry->account_code == $bank->code);
                        if ($isBankRow) { $runningBalance += ($entry->debit - $entry->credit); }

                        $desc = $journal->description;
                        if ($journal->reference_no) { $desc .= ' - ' . $journal->reference_no; }
                    @endphp
                    <tr>
                        <td class="center">{{ $rowNumber++ }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($journal->date)->format('d-M-Y') }}</td>
                        <td>{{ $desc }}</td>
                        <td class="center" style="font-weight:bold;">{{ $entry->account_code }}</td>
                        <td class="right">{{ $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 0, ',', '.') : '' }}</td>
                        <td class="right">{{ $entry->credit > 0 ? 'Rp ' . number_format($entry->credit, 0, ',', '.') : '' }}</td>
                        <td class="right" style="font-weight:bold;">
                            @if($isBankRow)
                                <span class="{{ $runningBalance < 0 ? 'red' : 'blue' }}">
                                    {{ $runningBalance < 0 ? '(Rp ' . number_format(abs($runningBalance), 0, ',', '.') . ')' : 'Rp ' . number_format($runningBalance, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>
                        <td class="spacer"></td>
                        <td class="center">{{ $entry->account ? $entry->account->name : 'Tanpa Keterangan' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
