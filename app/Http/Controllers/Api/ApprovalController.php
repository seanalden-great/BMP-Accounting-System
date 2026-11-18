<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;

class ApprovalController extends Controller
{
    public function index()
    {
        // 1. Tarik Dokumen Penjualan (SO)
        $sales = Sale::with('contact:id,name')->where('status', 'Menunggu Persetujuan')->get()->map(function($item) {
            return [
                'id'           => $item->id,
                'doc_type_code'=> 'SO',
                'doc_type'     => 'Pesanan Penjualan',
                'reference_no' => $item->reference_no ?: 'SO #' . $item->id,
                'person'       => $item->contact->name ?? 'Pelanggan Umum',
                'amount'       => $item->total,
                'date'         => $item->date,
                // Pastikan Anda sudah membuat endpoint API untuk fungsi setujui/tolak ini
                'approve_url'  => url("/api/sales/{$item->id}/approve"),
                'reject_url'   => url("/api/sales/{$item->id}/reject"),
            ];
        });

        // 2. Tarik Dokumen Pembelian (PO)
        $purchases = Purchase::with('contact:id,name')->where('status', 'Menunggu Persetujuan')->get()->map(function($item) {
            return [
                'id'           => $item->id,
                'doc_type_code'=> 'PO',
                'doc_type'     => 'Pesanan Pembelian',
                'reference_no' => $item->reference_no ?: 'PO #' . $item->id,
                'person'       => $item->contact->name ?? 'Pemasok Umum',
                'amount'       => $item->total,
                'date'         => $item->date,
                // Pastikan Anda sudah membuat endpoint API untuk fungsi setujui/tolak ini
                'approve_url'  => url("/api/purchases/{$item->id}/approve"),
                'reject_url'   => url("/api/purchases/{$item->id}/reject"),
            ];
        });

        // Gabungkan dokumen lalu urutkan dari tanggal terlama ke terbaru
        $pendingDocuments = collect()->merge($sales)->merge($purchases)->sortBy('date')->values();

        return response()->json([
            'success' => true,
            'data'    => $pendingDocuments
        ], 200);
    }
}
