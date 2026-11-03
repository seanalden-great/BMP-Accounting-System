<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;

class SendDueDateAlerts extends Command
{
    // Nama instruksi ini untuk dijalankan di terminal
    protected $signature = 'erp:send-due-alerts';
    protected $description = 'Mengirim notifikasi email otomatis ke semua staf terkait tagihan H-7';

    public function handle()
    {
        $sevenDays = Carbon::now()->addDays(7)->toDateString();

        // Cari Dokumen Mendekati Tenggat Waktu
        $poAlerts = Purchase::with('contact')->whereIn('status', ['Pesanan', 'Pengiriman'])
            ->whereDate('due_date', '<=', $sevenDays)->orderBy('due_date', 'asc')->get();
        $soAlerts = Sale::with('contact')->whereIn('status', ['Pesanan', 'Pengiriman'])
            ->whereDate('due_date', '<=', $sevenDays)->orderBy('due_date', 'asc')->get();

        $totalAlerts = $poAlerts->count() + $soAlerts->count();

        // Jika tidak ada masalah (tidak ada tagihan mendesak), batalkan pengiriman email
        if ($totalAlerts === 0) {
            $this->info('Aman. Tidak ada tagihan H-7 hari ini.');
            return;
        }

        // Tarik semua user/staf di sistem yang kolom email-nya terisi
        $users = User::whereNotNull('email')->where('email', '!=', '')->get();

        if ($users->isEmpty()) {
            $this->error('Gagal mengirim email: Tidak ada satupun user yang mendaftarkan emailnya di profil.');
            return;
        }

        // Susun HTML Email yang Rapi
        $html = "
            <h2 style='color:#b91c1c;'>[URGENT] Peringatan Sistem ERP PT Berlian Mandiri Perkasa</h2>
            <p>Halo, sistem mendeteksi ada <b>{$totalAlerts} Tagihan</b> yang tenggat waktunya kurang dari 7 hari (H-7) atau sudah melampaui batas waktu.</p>
            <hr>
        ";

        if ($poAlerts->isNotEmpty()) {
            $html .= "<h3 style='color:#b91c1c;'>Tagihan Hutang ke Pemasok (PO)</h3><ul>";
            foreach ($poAlerts as $po) {
                $tgl = Carbon::parse($po->due_date)->format('d-M-Y');
                $html .= "<li>PO #{$po->id} - <b>{$po->contact->name}</b> (Jatuh Tempo: <b>{$tgl}</b>)</li>";
            }
            $html .= "</ul>";
        }

        if ($soAlerts->isNotEmpty()) {
            $html .= "<h3 style='color:#2563eb;'>Piutang dari Pelanggan (SO)</h3><ul>";
            foreach ($soAlerts as $so) {
                $tgl = Carbon::parse($so->due_date)->format('d-M-Y');
                $html .= "<li>SO #{$so->id} - <b>{$so->contact->name}</b> (Jatuh Tempo: <b>{$tgl}</b>)</li>";
            }
            $html .= "</ul>";
        }

        $html .= "<br><p>Segera masuk ke Sistem ERP untuk melakukan pengecekan dan penyelesaian.</p>";

        // Tembakkan email ke semua staf tersebut satu per satu
        foreach ($users as $user) {
            Mail::html($html, function ($msg) use ($user) {
                $msg->to($user->email)->subject('ERP Alert: Jatuh Tempo Tagihan (H-7)');
            });
        }

        $this->info("Email peringatan berhasil ditembakkan ke {$users->count()} staf.");
    }
}
