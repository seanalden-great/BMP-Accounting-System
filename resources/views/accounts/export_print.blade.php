<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Chart of Accounts</title>
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
        .blue { color: #2563eb; }
        .grand-total { background-color: #1e293b; color: white; font-weight: bold; }
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
                <th rowspan="2" style="width: 80px;">CoA</th>
                <th rowspan="2">Nama Account</th>
                <th rowspan="2" style="width: 50px;">Pos</th>
                <th colspan="2">Saldo</th>
                <th rowspan="2" style="width: 130px;">SISA SALDO</th>
            </tr>
            <tr>
                <th style="width: 120px;">D</th>
                <th style="width: 120px;">K</th>
            </tr>
        </thead>
        <tbody>
            @foreach($response as $acc)
            @php
                $fontWeight = $acc['isHeader'] ? 'font-weight:bold;' : 'font-weight:normal;';
                $indent = ($acc['level'] * 15) . 'px';
            @endphp
            <tr>
                <td class="center" style="{{ $fontWeight }}">{{ $acc['code'] }}</td>
                <td style="padding-left: {{ $indent }}; {{ $fontWeight }}">{{ $acc['name'] }}</td>
                <td class="center">{{ $acc['pos'] }}</td>
                <td class="right">{{ $acc['debit'] ? 'Rp ' . number_format($acc['debit'], 0, ',', '.') : '' }}</td>
                <td class="right">{{ $acc['kredit'] ? 'Rp ' . number_format($acc['kredit'], 0, ',', '.') : '' }}</td>
                <td class="right" style="font-weight:bold;">
                    @if($acc['sisa'] > 0)
                        <span class="blue">Rp {{ number_format($acc['sisa'], 0, ',', '.') }}</span>
                    @elseif($acc['sisa'] < 0)
                        <span class="red">(Rp {{ number_format(abs($acc['sisa']), 0, ',', '.') }})</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php $isBalanced = (round($grandTotalDebit, 2) === round($grandTotalKredit, 2)); @endphp
            <tr class="grand-total">
                <td colspan="3" class="right">TOTAL NERACA SALDO (MUTASI DETAIL)</td>
                <td class="right">Rp {{ number_format($grandTotalDebit, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($grandTotalKredit, 0, ',', '.') }}</td>
                <td class="center" style="color: {{ $isBalanced ? '#10b981' : '#ef4444' }};">
                    {{ $isBalanced ? 'SEIMBANG (BALANCE)' : 'TIDAK SEIMBANG' }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
