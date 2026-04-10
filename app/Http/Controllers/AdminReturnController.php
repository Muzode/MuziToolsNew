<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Tool;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReturnController extends Controller
{
    /**
     * READ: Menampilkan Riwayat Pengembalian (History)
     */
    public function index()
    {
        $returns = Loan::with(['user', 'tool'])
            ->where('status', 'kembali')
            ->latest('tanggal_kembali_aktual')
            ->paginate(10);
        return view('admin.returns.index', compact('returns'));
    }
    
    /**
     * CREATE (Form): Menampilkan daftar alat yang SEDANG DIPINJAM
     */
    public function create()
    {
        $activeLoans = Loan::with(['user', 'tool'])
            ->where('status', 'disetujui')
            ->latest()
            ->get();
        return view('admin.returns.create', compact('activeLoans'));
    }
    
    /**
     * STORE: Proses Simpan Pengembalian
     */
public function store(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id', // Tambahkan validasi loan_id
            'tanggal_kembali_aktual' => 'required|date',
        ]);
    
        $loan = Loan::findOrFail($request->loan_id); // Ambil dari request
    
        if ($loan->status !== 'disetujui') {
            return back()->with('error', 'Peminjaman tidak valid atau sudah dikembalikan.');
        }
    
        $tanggalAktual = Carbon::parse($request->tanggal_kembali_aktual);
        $tanggalRencana = Carbon::parse($loan->tanggal_kembali_rencana);
        
        // Hitung denda
        $denda = 0;
        $hariTelat = 0;
        
if ($tanggalAktual->gt($tanggalRencana)) {
    // Cara 1: Gunakan diffInDays dengan urutan yang benar
    $hariTelat = $tanggalRencana->diffInDays($tanggalAktual);
    
    // Atau Cara 2: Gunakan diffInDays dengan parameter absolute (lebih aman)
    // $hariTelat = $tanggalAktual->diffInDays($tanggalRencana, false);
    // if ($hariTelat < 0) $hariTelat = abs($hariTelat);
    
    $denda = $hariTelat * ($loan->denda_per_hari ?? 5000);
}        
        // Update loan
        $loan->update([
            'status' => 'kembali',
            'tanggal_kembali_aktual' => $tanggalAktual,
            'denda' => $denda
        ]);
    
        // Kembalikan stok alat
        $tool = Tool::find($loan->tool_id);
        if ($tool) {
            $tool->increment('stok');
        }
    
        // Catat aktivitas
        ActivityLog::record('Pengembalian (Admin)', 'Memproses pengembalian alat: ' . ($loan->tool->nama_alat ?? '-') . ' dengan denda Rp ' . number_format($denda, 0, ',', '.'));
    
        if ($denda > 0) {
            return redirect()->route('admin.returns.create')->with('success', 'Alat telah dikembalikan. Telat ' . $hariTelat . ' hari, Denda: Rp ' . number_format($denda, 0, ',', '.'));
        }
        
        return redirect()->route('admin.returns.create')->with('success', 'Alat telah dikembalikan tepat waktu.');
    }    
    /**
     * EDIT: Edit data pengembalian
     */
    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->status != 'kembali') {
            return redirect()->route('admin.returns.index');
        }
        return view('admin.returns.edit', compact('loan'));
    }
    
    /**
     * UPDATE: Simpan perubahan data pengembalian
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date'
        ]);
        
        $loan->update([
            'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual
        ]);
        
        return redirect()->route('admin.returns.index')->with('success', 'Data pengembalian diperbarui.');
    }
    
    /**
     * DESTROY: Hapus riwayat pengembalian
     */
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->delete();
        return redirect()->route('admin.returns.index')->with('success', 'Riwayat dihapus.');
    }
}