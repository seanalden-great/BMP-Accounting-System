<?php

// namespace App\Http\Controllers;

// use App\Models\Account;
// use App\Models\JournalEntry;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class AccountController extends Controller
// {
//     // Menampilkan Chart of Accounts beserta saldonya
//     public function index()
//     {
//         $accounts = Account::orderBy('code', 'asc')->get();

//         // Hitung total mutasi Debit & Kredit per akun
//         $sums = JournalEntry::select('account_code', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
//             ->groupBy('account_code')
//             ->get()
//             ->keyBy('account_code');

//         $response = [];

//         foreach ($accounts as $i => $acc) {
//             $debit = 0;
//             $kredit = 0;
//             $sisa = 0;

//             if ($acc->is_header) {
//                 // Jumlahkan nilai dari akun anak berdasarkan LEVEL
//                 for ($j = $i + 1; $j < count($accounts); $j++) {
//                     $child = $accounts[$j];
                    
//                     if ($child->level <= $acc->level) {
//                         break; 
//                     }
                    
//                     if (!$child->is_header && isset($sums[$child->code])) {
//                         $debit += $sums[$child->code]->total_debit;
//                         $kredit += $sums[$child->code]->total_credit;
//                     }
//                 }
//             } else {
//                 if (isset($sums[$acc->code])) {
//                     $debit = $sums[$acc->code]->total_debit;
//                     $kredit = $sums[$acc->code]->total_credit;
//                 }
//             }

//             // if ($acc->normal_balance == 'D') {
//             //     $sisa = $debit - $kredit;
//             // } elseif ($acc->normal_balance == 'K') {
//             //     $sisa = $kredit - $debit;
//             // } else {
//             //     $sisa = $debit - $kredit; // Netral
//             // }

//             if ($acc->normal_balance == 'D') {
//                 $sisa = $debit - $kredit;
//             } elseif ($acc->normal_balance == 'K') {
//                 $sisa = $kredit - $debit;
//             } else {
//                 // Jika Pos '-' (Akun Header/Induk), ikuti saldo normal kelompok kode depannya:
//                 // Kelompok 3 (Kewajiban), 4 (Modal), 5 (Pendapatan), dan 21 (Akum. Penyusutan) bersaldo normal Kredit
//                 if (
//                     str_starts_with($acc->code, '3') || 
//                     str_starts_with($acc->code, '4') || 
//                     str_starts_with($acc->code, '5') || 
//                     str_starts_with($acc->code, '21')
//                 ) {
//                     $sisa = $kredit - $debit;
//                 } else {
//                     $sisa = $debit - $kredit;
//                 }
//             }

//             // OPSI ALTERNATIF: Jika hanya ingin menampilkan Saldo Akhir di salah satu sisi (D atau K saja)
//             $isCreditNormal = ($acc->normal_balance == 'K') || (
//                 $acc->normal_balance == '-' && (
//                     str_starts_with($acc->code, '3') || 
//                     str_starts_with($acc->code, '4') || 
//                     str_starts_with($acc->code, '5') || 
//                     str_starts_with($acc->code, '21')
//                 )
//             );

//             // Jika lunas/habis ($sisa == 0), maka kolom D dan K otomatis kosong (null)
//             $netDebit  = (!$isCreditNormal && $sisa > 0) || ($isCreditNormal && $sisa < 0) ? abs($sisa) : null;
//             $netKredit = ($isCreditNormal && $sisa > 0) || (!$isCreditNormal && $sisa < 0) ? abs($sisa) : null;

//             $item = [
//                 'code'      => $acc->code,
//                 'name'      => $acc->name,
//                 'isHeader'  => $acc->is_header,
//                 'pos'       => $acc->normal_balance,
//                 'level'     => $acc->level,
//                 'debit'     => $debit > 0 ? $debit : null,
//                 'kredit'    => $kredit > 0 ? $kredit : null,
//                 'sisa'      => $sisa,
//             ];

//             $response[] = $item;
//         }

//         // Return ke view blade
//         return view('accounts.index', compact('response'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'code'           => 'required|string|unique:accounts,code',
//             'name'           => 'required|string',
//             'is_header'      => 'boolean',
//             'normal_balance' => 'required|in:D,K,-',
//             'level'          => 'required|integer',
//         ]);

//         try {
//             Account::create($validated);
//             return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Gagal menyimpan akun baru');
//         }
//     }

//     public function update(Request $request, $code)
//     {
//         $account = Account::findOrFail($code);

//         $validated = $request->validate([
//             'name'           => 'required|string',
//             'is_header'      => 'boolean',
//             'normal_balance' => 'required|in:D,K,-',
//             'level'          => 'required|integer',
//         ]);

//         try {
//             $account->update($validated);
//             return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Gagal memperbarui akun');
//         }
//     }

//     public function destroy($code)
//     {
//         // Proteksi jika akun sudah dipakai di Jurnal
//         $count = JournalEntry::where('account_code', $code)->count();
//         if ($count > 0) {
//             return redirect()->back()->with('error', 'Akun tidak bisa dihapus karena memiliki riwayat jurnal!');
//         }

//         try {
//             $account = Account::findOrFail($code);
//             $account->delete();
//             return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Gagal menghapus akun');
//         }
//     }
// }

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    // Menampilkan Chart of Accounts beserta saldonya
    public function index()
    {
        $accounts = Account::orderBy('code', 'asc')->get();

        // Hitung total mutasi Debit & Kredit per akun
        $sums = JournalEntry::select('account_code', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->groupBy('account_code')
            ->get()
            ->keyBy('account_code');

        $response = [];
        
        // Variabel penampung Total Neraca Saldo (hanya menjumlahkan akun detail/anak)
        $grandTotalDebit = 0;
        $grandTotalKredit = 0;

        foreach ($accounts as $i => $acc) {
            $debit = 0;
            $kredit = 0;
            $sisa = 0;

            if ($acc->is_header) {
                // Jumlahkan nilai dari akun anak berdasarkan LEVEL
                for ($j = $i + 1; $j < count($accounts); $j++) {
                    $child = $accounts[$j];
                    
                    if ($child->level <= $acc->level) {
                        break; 
                    }
                    
                    if (!$child->is_header && isset($sums[$child->code])) {
                        $debit += $sums[$child->code]->total_debit;
                        $kredit += $sums[$child->code]->total_credit;
                    }
                }
            } else {
                if (isset($sums[$acc->code])) {
                    $debit = $sums[$acc->code]->total_debit;
                    $kredit = $sums[$acc->code]->total_credit;
                }

                // Tambahkan ke Grand Total (HANYA untuk akun detail agar tidak double-counting)
                $grandTotalDebit += $debit;
                $grandTotalKredit += $kredit;
            }

            if ($acc->normal_balance == 'D') {
                $sisa = $debit - $kredit;
            } elseif ($acc->normal_balance == 'K') {
                $sisa = $kredit - $debit;
            } else {
                // Jika Pos '-' (Akun Header/Induk), ikuti saldo normal kelompok kode depannya:
                // Kelompok 3 (Kewajiban), 4 (Modal), 5 (Pendapatan), dan 21 (Akum. Penyusutan) bersaldo normal Kredit
                if (
                    str_starts_with($acc->code, '3') || 
                    str_starts_with($acc->code, '4') || 
                    str_starts_with($acc->code, '5') || 
                    str_starts_with($acc->code, '21')
                ) {
                    $sisa = $kredit - $debit;
                } else {
                    $sisa = $debit - $kredit;
                }
            }

            // OPSI ALTERNATIF: Jika hanya ingin menampilkan Saldo Akhir di salah satu sisi (D atau K saja)
            $isCreditNormal = ($acc->normal_balance == 'K') || (
                $acc->normal_balance == '-' && (
                    str_starts_with($acc->code, '3') || 
                    str_starts_with($acc->code, '4') || 
                    str_starts_with($acc->code, '5') || 
                    str_starts_with($acc->code, '21')
                )
            );

            $netDebit  = (!$isCreditNormal && $sisa > 0) || ($isCreditNormal && $sisa < 0) ? abs($sisa) : null;
            $netKredit = ($isCreditNormal && $sisa > 0) || (!$isCreditNormal && $sisa < 0) ? abs($sisa) : null;

            $item = [
                'code'     => $acc->code,
                'name'     => $acc->name,
                'isHeader' => $acc->is_header,
                'pos'      => $acc->normal_balance,
                'level'    => $acc->level,
                'debit'    => $debit > 0 ? $debit : null,
                'kredit'   => $kredit > 0 ? $kredit : null,
                'sisa'     => $sisa,
            ];

            $response[] = $item;
        }

        // Return ke view blade beserta Grand Total
        return view('accounts.index', compact('response', 'grandTotalDebit', 'grandTotalKredit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'           => 'required|string|unique:accounts,code',
            'name'           => 'required|string',
            'is_header'      => 'boolean',
            'normal_balance' => 'required|in:D,K,-',
            'level'          => 'required|integer',
        ]);

        try {
            Account::create($validated);
            return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan akun baru');
        }
    }

    public function update(Request $request, $code)
    {
        $account = Account::findOrFail($code);

        $validated = $request->validate([
            'name'           => 'required|string',
            'is_header'      => 'boolean',
            'normal_balance' => 'required|in:D,K,-',
            'level'          => 'required|integer',
        ]);

        try {
            $account->update($validated);
            return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui akun');
        }
    }

    public function destroy($code)
    {
        // Proteksi jika akun sudah dipakai di Jurnal
        $count = JournalEntry::where('account_code', $code)->count();
        if ($count > 0) {
            return redirect()->back()->with('error', 'Akun tidak bisa dihapus karena memiliki riwayat jurnal!');
        }

        try {
            $account = Account::findOrFail($code);
            $account->delete();
            return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus akun');
        }
    }
}