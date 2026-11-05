{{-- <!-- resources/views/payroll/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="credit-card" class="w-7 h-7 text-teal-600"></i> Penggajian (Payroll) & HR
            </h1>
            <p class="text-slate-500 mt-1">Kelola data karyawan, komponen gaji, hitung BPJS, dan proses slip gaji.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="user-plus" class="w-4 h-4 text-blue-600"></i> Tambah Karyawan
            </button>
            <button class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="play-circle" class="w-4 h-4"></i> Proses Gaji Bulan Ini
            </button>
        </div>
    </div>

    <!-- Ringkasan HRD (Sekarang Terhubung dengan Database) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-blue-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Total Karyawan Aktif</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ $totalEmployees }} <span class="text-sm font-normal text-slate-400">Orang</span></h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-teal-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Estimasi Beban Gaji Pokok</p>
            <h3 class="text-3xl font-bold text-teal-600">Rp {{ number_format($estimasiGajiPokok, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2">Bulan berjalan</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-rose-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Estimasi Potongan (BPJS 3%)</p>
            <h3 class="text-3xl font-bold text-rose-600">Rp {{ number_format($estimasiPotongan, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2">Dipotong dari gaji kotor</p>
        </div>
    </div>

    <!-- Tabel Data Karyawan (Sekarang Menggunakan Loop Data Asli) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50 font-bold text-slate-700 flex justify-between items-center">
            <span class="flex items-center gap-2"><i data-feather="users" class="w-5 h-5 text-slate-500"></i> Database Karyawan (Master Data)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse min-w-[900px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-slate-600 w-24">NIK</th>
                        <th class="p-4 text-slate-600">Nama Lengkap</th>
                        <th class="p-4 text-slate-600">Jabatan</th>
                        <th class="p-4 text-slate-600 text-right">Gaji Pokok (Rp)</th>
                        <th class="p-4 text-slate-600 text-center">Status BPJS</th>
                        <th class="p-4 text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-mono font-bold text-slate-500">{{ $emp->nik }}</td>
                        <td class="p-4 font-bold text-slate-800">{{ $emp->name }}</td>
                        <td class="p-4"><span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-bold border border-slate-200">{{ $emp->position }}</span></td>
                        <td class="p-4 text-right font-bold text-teal-600">{{ number_format($emp->basic_salary, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            @if($emp->has_bpjs)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold">Terdaftar</span>
                            @else
                                <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded text-xs font-bold">Tidak Terdaftar</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <button class="text-blue-500 hover:bg-blue-50 p-2 rounded transition-colors" title="Edit Karyawan"><i data-feather="edit" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 font-medium">Belum ada data Karyawan yang tersimpan di sistem. Silakan tekan tombol 'Tambah Karyawan'.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection --}}

<!-- resources/views/payroll/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <i data-feather="credit-card" class="w-7 h-7 text-teal-600"></i> Penggajian (Payroll) & HR
            </h1>
            <p class="text-slate-500 mt-1">Kelola data karyawan, komponen gaji, hitung BPJS, dan proses slip gaji.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Tombol Tambah Karyawan yang kini memiliki aksi Onclick -->
            <button onclick="openEmployeeModal()" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="user-plus" class="w-4 h-4 text-blue-600"></i> Tambah Karyawan
            </button>
            <button class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                <i data-feather="play-circle" class="w-4 h-4"></i> Proses Gaji Bulan Ini
            </button>
        </div>
    </div>

    <!-- Ringkasan HRD (Terhubung dengan Database) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-blue-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Total Karyawan Aktif</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ $totalEmployees }} <span class="text-sm font-normal text-slate-400">Orang</span></h3>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-teal-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Estimasi Beban Gaji Pokok</p>
            <h3 class="text-3xl font-bold text-teal-600">Rp {{ number_format($estimasiGajiPokok, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2">Bulan berjalan</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 border-l-4 border-l-rose-500">
            <p class="text-sm text-slate-500 font-bold uppercase mb-2">Estimasi Potongan (BPJS 3%)</p>
            <h3 class="text-3xl font-bold text-rose-600">Rp {{ number_format($estimasiPotongan, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2">Dipotong dari gaji kotor</p>
        </div>
    </div>

    <!-- Tabel Data Karyawan -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50 font-bold text-slate-700 flex justify-between items-center">
            <span class="flex items-center gap-2"><i data-feather="users" class="w-5 h-5 text-slate-500"></i> Database Karyawan (Master Data)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse min-w-[900px]">
                <thead class="bg-white border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-slate-600 w-24">NIK</th>
                        <th class="p-4 text-slate-600">Nama Lengkap</th>
                        <th class="p-4 text-slate-600">Jabatan</th>
                        <th class="p-4 text-slate-600 text-right">Gaji Pokok (Rp)</th>
                        <th class="p-4 text-slate-600 text-center">Status BPJS</th>
                        <th class="p-4 text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-mono font-bold text-slate-500">{{ $emp->nik }}</td>
                        <td class="p-4 font-bold text-slate-800">{{ $emp->name }}</td>
                        <td class="p-4"><span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-bold border border-slate-200">{{ $emp->position }}</span></td>
                        <td class="p-4 text-right font-bold text-teal-600">{{ number_format($emp->basic_salary, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            @if($emp->has_bpjs)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold">Terdaftar</span>
                            @else
                                <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded text-xs font-bold">Tidak Terdaftar</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <button class="text-blue-500 hover:bg-blue-50 p-2 rounded transition-colors" title="Edit Karyawan"><i data-feather="edit" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 font-medium">Belum ada data Karyawan yang tersimpan di sistem. Silakan tekan tombol 'Tambah Karyawan'.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM TAMBAH KARYAWAN -->
    <div id="employeeModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-800">Tambah Data Karyawan Baru</h3>
                <button type="button" onclick="closeEmployeeModal()" class="text-slate-400 hover:text-slate-700 transition-colors"><i data-feather="x"></i></button>
            </div>

            <!-- Form akan otomatis menghapus titik-titik ribuan sebelum dikirim ke server -->
            <form action="{{ route('payroll.storeEmployee') }}" method="POST" class="p-6 space-y-5" onsubmit="unformatRibuan(this)">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">NIK (Nomor Induk Karyawan)</label>
                        <input type="text" name="nik" required placeholder="Cth: EMP-2611-001" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 font-mono text-sm uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Nama sesuai KTP" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Jabatan (Posisi)</label>
                        <input type="text" name="position" required placeholder="Cth: Staf Keuangan" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Rekening Bank (Opsional)</label>
                        <input type="text" name="bank_account" placeholder="Cth: BCA - 123456789" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 text-sm">
                    </div>
                </div>

                <div class="mt-4 border-t border-slate-200 pt-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Gaji Pokok (Basic Salary)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500 text-sm">Rp</span>
                                <input type="text" name="basic_salary" required oninput="formatRibuan(this)" placeholder="0" class="format-ribuan w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-lg font-bold text-slate-700 outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Status BPJS (JHT & Kesehatan)</label>
                            <select name="has_bpjs" required class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-teal-500 text-sm font-medium text-slate-700">
                                <option value="1" selected>Terdaftar (Dipotong 3%)</option>
                                <option value="0">Tidak Terdaftar (Tanpa Potongan)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6">
                    <button type="button" onclick="closeEmployeeModal()" class="px-5 py-2.5 text-slate-600 hover:bg-slate-100 rounded-lg font-bold transition-colors">Batal</button>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg font-bold transition-colors flex items-center gap-2">Simpan Karyawan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // FUNGSI HELPER FORMAT ANGKA RIBUAN
    function formatRibuan(inputElement) {
        let value = inputElement.value.replace(/[^0-9]/g, '');
        if (value !== '') {
            inputElement.value = parseInt(value, 10).toLocaleString('id-ID');
        } else {
            inputElement.value = '';
        }
    }

    function unformatRibuan(formElement) {
        let formattedInputs = formElement.querySelectorAll('.format-ribuan');
        formattedInputs.forEach(input => {
            input.value = input.value.replace(/\./g, '');
        });
    }

    // FUNGSI BUKA TUTUP MODAL
    function openEmployeeModal() {
        document.getElementById('employeeModal').classList.remove('hidden');
    }

    function closeEmployeeModal() {
        document.getElementById('employeeModal').classList.add('hidden');
    }
</script>
@endsection
