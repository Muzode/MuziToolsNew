<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamController extends Controller
{
    public function index()
    {
        $tools = Tool::with('category')->get();
        return view('peminjam.dashboard', compact('tools'));
    }
    public function store(Request $request)
    {
        // Cek stok dulu
        $tool = Tool::find($request->tool_id);
        if ($tool->stok > 0) {
            Loan::create([
                'user_id' => Auth::id(),
                'tool_id' => $request->tool_id,
                'tanggal_pinjam' => now(),
                'tanggal_kembali_rencana' => $request->tanggal_kembali,
                'status' => 'pending'
            ]);
            ActivityLog::record('Tambah Alat', 'Menambahkan alat baru: ' . $request->nama_alat);
            // Opsional: Kurangi stok langsung atau saat disetujui (tergantung logika bisnis)
            return back()->with('success', 'Pengajuan berhasil, menunggu persetujuan.');
        }
    }
    public function history()
    {
        $loans = Loan::where('user_id', Auth::id())
            ->with('tool')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('peminjam.riwayat', compact('loans'));
    }
}
