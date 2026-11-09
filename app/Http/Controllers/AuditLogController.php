<?php
namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan hanya atasan yang boleh mengintip log ini
        if (!in_array(auth()->user()->role, ['Admin', 'Direktur'])) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak ke Audit Trail.');
        }

        $query = AuditLog::with('user')->orderBy('id', 'desc');

        if ($request->has('module') && $request->module != '') {
            $query->where('module', $request->module);
        }

        $logs = $query->paginate(50); // Pagination agar tidak berat saat data mencapai ribuan
        $modules = AuditLog::select('module')->distinct()->pluck('module');

        return view('audit_logs.index', compact('logs', 'modules'));
    }
}
