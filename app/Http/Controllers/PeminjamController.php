<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamController extends Controller
{
    public function index(request $request)
    {
        $query = Tool::with('category');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', '%' . $search . '%');
                    });
            });
        }

        $tools = $query->get();  // ← Gunakan $query, bukan Tool:: lagi

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
                'quantity' => $request->quantity,
                'tanggal_pinjam' => now(),
                'tanggal_kembali_rencana' => $request->tanggal_kembali,
                'status' => 'pending'
            ]);
            ActivityLog::record('Pinjam Alat', 'Meminjam alat baru: ' . $request->nama_alat);
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

    public function requestReturn($id)
    {
        $loan = Loan::where('user_id', Auth::id())->findOrFail($id);

        // Cek apakah status sedang dipinjam (disetujui)
        if ($loan->status != 'disetujui') {
            return back()->with('error', 'Hanya peminjaman yang sedang dipinjam yang dapat diajukan pengembalian.');
        }

        // Cek apakah sudah pernah diajukan sebelumnya
        if ($loan->pengembalian_diajukan) {
            return back()->with('error', 'Pengajuan pengembalian sudah diajukan, mohon tunggu konfirmasi petugas.');
        }

        // Update status menjadi 'pengembalian_diajukan' atau tambah field baru
        $loan->update([
            'status' => 'diajukan',
            'tanggal_pengembalian_diajukan' => now(),
        ]);

        ActivityLog::record('Ajukan Pengembalian', 'Mengajukan pengembalian alat: ' . $loan->tool->nama_alat);

        return back()->with('success', 'Pengajuan pengembalian berhasil, menunggu konfirmasi petugas.');
    }
}
