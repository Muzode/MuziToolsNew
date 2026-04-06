<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Tool;
use App\Models\ActivityLog;

class AdminReturnController extends Controller
{
    /**
     * READ: Menampilkan Riwayat Pengembalian (History)
     */
    public function index()
    {
        // Ambil hanya yang statusnya 'kembali'
        $returns = Loan::with(['user', 'tool'])
            ->where('status', 'kembali')
            ->latest('tanggal_kembali_aktual')
            ->paginate(10);
        return view('admin.returns.index', compact('returns'));
    }
    /**
     * CREATE (Form): Menampilkan daftar alat yang SEDANG DIPINJAM
     * Admin memilih dari sini untuk dikembalikan.
     */
    public function create()
    {
        // Ambil data yang statusnya 'disetujui' (Sedang di luar)
        $activeLoans = Loan::with(['user', 'tool'])
            ->where('status', 'disetujui')
            ->latest()
            ->get();
        return view('admin.returns.create', compact('activeLoans'));
    }
    /**

     * STORE: Proses Simpan Pengembalian (Action)
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'denda' => 'nullable|integer' // Opsional jika mau ada denda
        ]);
        $loan = Loan::findOrFail($request->loan_id);
        if ($loan->status != 'disetujui') {
            return back()->with('error', 'Data tidak valid atau sudah dikembalikan.');
        }
        // 1. Update Status & Tanggal
        $loan->update([
            'status' => 'kembali',
            'tanggal_kembali_aktual' => now(),
            // 'denda' => $request->denda // Jika tabel loans punya kolom denda
        ]);
        // 2. Kembalikan Stok Alat
        $tool = Tool::findOrFail($loan->tool_id);
        $tool->increment('stok');
        ActivityLog::record('Pengembalian (Admin)', 'Memproses pengembalian alat: ' . $tool->nama_alat);
        return redirect()->route('admin.returns.index')->with('success', 'Alat berhasil dikembalikan.');
    }
    /**
     * EDIT: Edit data pengembalian (Misal salah tanggal)
     */
    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        // Pastikan hanya bisa edit yang statusnya sudah kembali
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
        // Jika data dihapus, apakah stok mau dikurangi lagi? 
        // Biasanya hapus riwayat tidak mempengaruhi stok fisik saat ini, tapi tergantung kebijakan.
        // Di sini kita asumsikan hanya hapus arsip.
        $loan->delete();
        return redirect()->route('admin.returns.index')->with('success', 'Riwayat dihapus.');
    }
}
