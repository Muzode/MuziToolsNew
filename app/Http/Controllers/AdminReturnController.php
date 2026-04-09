<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Tool;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

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
            'selected_loans' => 'required|array',
            'selected_loans.*' => 'exists:loans,id',
            'tanggal_kembali' => 'required|array',
            'tanggal_kembali.*' => 'required|date',
            'denda' => 'nullable|array'
        ]);
        
        $successCount = 0;
        $totalDenda = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($request->selected_loans as $loanId) {
                $loan = Loan::findOrFail($loanId);
                
                if ($loan->status != 'disetujui') {
                    $errors[] = "Peminjaman {$loan->tool->nama_alat} oleh {$loan->user->name} sudah tidak aktif.";
                    continue;
                }
                
                $tanggalKembali = $request->tanggal_kembali[$loanId];
                $denda = $request->denda[$loanId] ?? 0;
                
                // Update loan
                $loan->update([
                    'status' => 'kembali',
                    'tanggal_kembali_aktual' => $tanggalKembali,
                    'denda' => $denda
                ]);
                
                // Kembalikan stok
                $tool = Tool::findOrFail($loan->tool_id);
                $tool->increment('stok');
                
                $successCount++;
                $totalDenda += $denda;
                
                ActivityLog::record('Pengembalian (Admin)', 
                    "Proses pengembalian alat: {$tool->nama_alat} oleh {$loan->user->name}" . 
                    ($denda > 0 ? " | Denda: Rp " . number_format($denda, 0, ',', '.') : ""));
            }
            
            DB::commit();
            
            $message = "Berhasil memproses {$successCount} pengembalian alat.";
            if ($totalDenda > 0) {
                $message .= " Total denda: Rp " . number_format($totalDenda, 0, ',', '.');
            }
            
            if (!empty($errors)) {
                return redirect()->route('admin.returns.index')
                    ->with('warning', $message)
                    ->with('errors', $errors);
            }
            
            return redirect()->route('admin.returns.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
