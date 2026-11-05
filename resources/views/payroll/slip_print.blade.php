<!-- resources/views/payroll/slip_print.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $detail->employee->name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 40px; color: #333; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 22px; font-weight: bold; letter-spacing: 1px; }
        .subtitle { font-size: 14px; color: #555; margin-top: 5px; }
        .emp-info { margin-bottom: 30px; width: 100%; border-collapse: collapse; }
        .emp-info td { padding: 6px; }
        .salary-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; border: 1.5px solid #000; }
        .salary-table th, .salary-table td { border: 1px solid #777; padding: 12px; text-align: left; }
        .salary-table th { background-color: #f4f4f4; text-align: center; }
        .right { text-align: right; }
        .net-pay { font-size: 16px; font-weight: bold; background-color: #e2e8f0; }
        .signatures { width: 100%; margin-top: 50px; text-align: center; }
        .signatures td { padding-top: 80px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <button class="no-print" onclick="window.print()" style="padding:10px 15px; margin-bottom:20px; font-weight:bold; cursor:pointer;">🖨️ Cetak Dokumen</button>

    <div class="header">
        <div class="title">PT BERLIAN MANDIRI PERKASA</div>
        <div class="subtitle">SLIP GAJI KARYAWAN (CONFIDENTIAL)</div>
        <div class="subtitle" style="font-weight:bold; margin-top:10px;">Periode: Bulan {{ $payroll->period_month }} Tahun {{ $payroll->period_year }}</div>
    </div>

    <table class="emp-info">
        <tr>
            <td width="150"><strong>NIK Karyawan</strong></td><td width="10">:</td><td>{{ $detail->employee->nik }}</td>
            <td width="150"><strong>Jabatan / Posisi</strong></td><td width="10">:</td><td>{{ $detail->employee->position }}</td>
        </tr>
        <tr>
            <td><strong>Nama Lengkap</strong></td><td>:</td><td>{{ $detail->employee->name }}</td>
            <td><strong>Rekening Bank</strong></td><td>:</td><td>{{ $detail->employee->bank_account ?: '-' }}</td>
        </tr>
    </table>

    <table class="salary-table">
        <thead>
            <tr>
                <th>KETERANGAN (KOMPONEN GAJI)</th>
                <th class="right">NOMINAL (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok (Basic Salary)</td>
                <td class="right">{{ number_format($detail->basic_salary, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="background-color: #f8f9fa; font-weight: bold; font-style:italic;">KOMPONEN POTONGAN</td>
            </tr>
            <tr>
                <td>Potongan BPJS (Kesehatan & Jaminan Hari Tua)</td>
                <td class="right" style="color: red;">({{ number_format($detail->bpjs_deduction, 0, ',', '.') }})</td>
            </tr>
            <tr>
                <td>Potongan Absensi / Kehadiran</td>
                <td class="right" style="color: red;">({{ number_format($detail->absence_deduction, 0, ',', '.') }})</td>
            </tr>
            <tr class="net-pay">
                <td>TOTAL GAJI BERSIH (TAKE HOME PAY)</td>
                <td class="right">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td width="50%">Penerima,<br><br><br><br><br><strong>{{ $detail->employee->name }}</strong></td>
            <td width="50%">Surabaya, {{ \Carbon\Carbon::parse($payroll->created_at)->format('d F Y') }}<br>Dibuat Oleh,<br><br><br><br><br><strong>HRD & Keuangan</strong></td>
        </tr>
    </table>
</body>
</html>
