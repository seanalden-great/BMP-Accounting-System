<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense; // Opsional jika kas bon/biaya butuh approval

class ApprovalController extends Controller
{
    public function index()
    {
        // 1. Tarik Dokumen Penjualan (SO)
        $sales = Sale::with('contact')->where('status', 'Menunggu Persetujuan')->get()->map(function($item) {
            $item->doc_type = 'Pesanan Penjualan (SO)';
            $item->type_class = 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800/30';
            $item->icon = 'shopping-bag';
            $item->person = $item->contact->name ?? 'Pelanggan Umum';
            $item->amount = $item->total;
            $item->approve_url = route('sales.approve', $item->id);
            $item->reject_url = route('sales.reject', $item->id);
            return $item;
        });

        // 2. Tarik Dokumen Pembelian (PO)
        $purchases = Purchase::with('contact')->where('status', 'Menunggu Persetujuan')->get()->map(function($item) {
            $item->doc_type = 'Pesanan Pembelian (PO)';
            $item->type_class = 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 border-orange-200 dark:border-orange-800/30';
            $item->icon = 'shopping-cart';
            $item->person = $item->contact->name ?? 'Pemasok Umum';
            $item->amount = $item->total;
            $item->approve_url = route('purchases.approve', $item->id);
            $item->reject_url = route('purchases.reject', $item->id);
            return $item;
        });

        // Gabungkan semua dokumen lalu urutkan dari tanggal paling lampau
        $pendingDocuments = collect()->merge($sales)->merge($purchases)->sortBy('date')->values();

        return view('approvals.index', compact('pendingDocuments'));
    }
}
