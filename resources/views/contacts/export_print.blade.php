<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Daftar Kontak</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 25px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; font-weight: bold; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background-color: #f1f5f9; text-align: center; font-weight: bold; }
        .center { text-align: center; }
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
                <th style="width: 200px;">Nama Kontak</th>
                <th style="width: 100px;">Tipe</th>
                <th style="width: 120px;">No. Telepon</th>
                <th style="width: 150px;">Email</th>
                <th>Alamat Lengkap</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contacts as $index => $contact)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td style="font-weight:bold;">{{ $contact->name }}</td>
                <td class="center">{{ $contact->type }}</td>
                <td>{{ $contact->phone ?: '-' }}</td>
                <td>{{ $contact->email ?: '-' }}</td>
                <td>{{ $contact->address ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center">Tidak ada data kontak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
