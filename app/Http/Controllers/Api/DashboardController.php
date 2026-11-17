<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. PENDAPATAN, PENGELUARAN, LABA
        $pendapatan = Sale::whereIn('status', ['Pengiriman', 'Tagihan'])
            ->whereMonth('date', $currentMonth)->whereYear('date', $currentYear)->sum('total');
        $pembelian = Purchase::whereIn('status', ['Pengiriman', 'Tagihan'])
            ->whereMonth('date', $currentMonth)->whereYear('date', $currentYear)->sum('total');
        $biaya = Expense::whereMonth('date', $currentMonth)->whereYear('date', $currentYear)->sum('amount');

        $pengeluaran = $pembelian + $biaya;
        $labaBersih = $pendapatan - $pengeluaran;

        // 2. KAS & BANK
        $bankAccounts = Account::where(function($query) {
            $query->where('code', 'LIKE', '10%')->orWhere('code', 'LIKE', '11%');
        })->where('is_header', false)->get();
        $bankCodes = $bankAccounts->pluck('code')->toArray();
        $saldoKas = JournalEntry::whereIn('account_code', $bankCodes)
            ->selectRaw('SUM(debit) - SUM(credit) as balance')->value('balance') ?? 0;

        // 3. KPI (PROFIT MARGIN & TARGET)
        $profitMargin = $pendapatan > 0 ? round(($labaBersih / $pendapatan) * 100, 1) : 0;
        $targetPenjualan = 250000000;
        $persentaseTarget = $pendapatan > 0 ? min(100, round(($pendapatan / $targetPenjualan) * 100, 1)) : 0;

        // 4. PERINGATAN STOK MENIPIS
        $allProducts = Product::withSum('stocks as total_stock', 'quantity')->get();
        $lowStocks = $allProducts->filter(function($p) { return ($p->total_stock ?? 0) <= 10; })
                                 ->sortBy('total_stock')->take(5)->values(); // values() me-reset indeks array

        // 5. TOP 5 PRODUK TERLARIS
        $topProducts = \App\Models\SaleItem::select('product_sku', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('sale', function($q) use ($currentMonth, $currentYear) {
                $q->whereMonth('date', $currentMonth)->whereYear('date', $currentYear);
            })->groupBy('product_sku')->orderBy('total_qty', 'desc')->take(5)->with('product:sku,name')->get();

        // 6. TOP 5 KLIEN TERBAIK
        $topCustomers = Sale::select('contact_id', DB::raw('SUM(total) as total_spent'))
            ->whereMonth('date', $currentMonth)->whereYear('date', $currentYear)
            ->whereIn('status', ['Pengiriman', 'Tagihan'])
            ->groupBy('contact_id')->orderBy('total_spent', 'desc')->take(5)->with('contact:id,name')->get();

        // 7. RIWAYAT AKTIVITAS
        $recentActivities = \App\Models\Journal::orderBy('id', 'desc')->take(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'pendapatan' => $pendapatan,
                    'pengeluaran' => $pengeluaran,
                    'laba_bersih' => $labaBersih,
                    'saldo_kas' => $saldoKas,
                    'profit_margin' => $profitMargin,
                    'target_penjualan' => $targetPenjualan,
                    'persentase_target' => $persentaseTarget,
                    'bulan' => Carbon::now()->translatedFormat('F Y'),
                ],
                'low_stocks' => $lowStocks,
                'top_products' => $topProducts,
                'top_customers' => $topCustomers,
                'recent_activities' => $recentActivities
            ]
        ], 200);
    }
}
