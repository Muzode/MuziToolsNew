<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Tool;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PetugasController extends Controller
{
    public function index()
    {
        //data yang statusnya pending
        $loans = Loan::where('status', 'pending')->with(['user', 'tool'])->get();

        //data yang statusnya disetujui (sedang dipinjam)
        $activeLoans = Loan::where('status', 'disetujui')->with(['user', 'tool'])->get();

        //data yang statusnya kembali
        $sudahDikembalikan = Loan::where('status', 'kembali')->with(['user', 'tool'])->latest('tanggal_kembali_aktual')->get();

        return view('petugas.dashboard', compact('loans', 'activeLoans', 'sudahDikembalikan'));
    }

    public function approve($id) 
    {
        $loan = Loan::findOrFail($id);
        $loan->update([
            'status' => 'disetujui',
            'petugas_id' => Auth::id()
        ]);

        // Kurangi stok alat
        $tool = Tool::find($loan->tool_id);
        if ($tool) {
            $tool->decrement('stok');
        }

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function processReturn(Request $request, $id) 
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
        ]);
    
        $loan = Loan::findOrFail($id);
    
        if ($loan->status !== 'disetujui') {
            return back()->with('error', 'Peminjaman tidak valid atau sudah dikembalikan.');
        }
    
        $tanggalAktual = Carbon::parse($request->tanggal_kembali_aktual);
        
        // Simpan tanggal aktual
        $loan->tanggal_kembali_aktual = $tanggalAktual;
        
        // Hitung denda (method sudah benar)
        $denda = $loan->calculateDenda();
        
        // Update status, tanggal aktual, dan denda
        $loan->status = 'kembali';
        $loan->denda = $denda;
        $loan->save();
    
        // Kembalikan stok alat
        $tool = Tool::find($loan->tool_id);
        if ($tool) {
            $tool->increment('stok');
        }
    
        // Catat aktivitas (opsional)
        if (class_exists(ActivityLog::class)) {
            ActivityLog::record('Pengembalian (Petugas)', 'Memproses pengembalian alat: ' . ($loan->tool->nama_alat ?? '-') . ' dengan denda Rp ' . number_format($denda, 0, ',', '.'));
        }
    
        return back()->with('success', 'Alat telah dikembalikan. Denda: Rp ' . number_format($denda, 0, ',', '.'));
    }

    public function report(Request $request) 
    {
        // Bisa tambahkan filter tanggal jika mau
        $loans = Loan::with(['user', 'tool'])->get();
        return view('petugas.laporan', compact('loans'));
    }
}