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
        $activeLoans = Loan::where(function ($query) {
            $query->where('status', 'diajukan')
                  ->orWhere('status', 'disetujui');
        })->with(['user', 'tool'])->get();

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
        if ($tool && $tool->stok > 0) {
            $tool->decrement('stok');
        } else {
            // Jika stok habis, batalkan approval
            $loan->update(['status' => 'pending']);
            return back()->with('error', 'Stok alat habis, tidak dapat menyetujui peminjaman.');
        }
        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function reject($id)
    {
        $loan = Loan::findOrFail($id);

        // Hanya bisa menolak yang statusnya masih 'pending'
        if ($loan->status != 'pending') {
            return back()->with('error', 'Peminjaman sudah diproses, tidak dapat ditolak.');
        }

        $loan->update([
            'status' => 'ditolak',
            'petugas_id' => Auth::id(),
            'tanggal_kembali_aktual' => null // Pastikan tidak terisi
        ]);

        // Catat aktivitas (opsional)
        ActivityLog::record('Tolak Peminjaman', 'Menolak peminjaman alat: ' . $loan->tool->nama_alat . ' oleh ' . $loan->user->name);

        return back()->with('success', 'Peminjaman ditolak.');
    }

    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'keterangan_kondisi' => 'nullable|string|max:500', // Tambah
            'gambar_kondisi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Tambah
        ]);

        $loan = Loan::findOrFail($id);

        if ($loan->status != 'diajukan' && $loan->status != 'disetujui') {
            return back()->with('error', 'Tidak ada pengajuan pengembalian untuk peminjaman ini.');
        }
        $tanggalAktual = Carbon::parse($request->tanggal_kembali_aktual);

        // Simpan tanggal aktual
        $loan->tanggal_kembali_aktual = $tanggalAktual;

        // Hitung denda (method sudah benar)
        $denda = $loan->calculateDenda();

        // Handle file upload
        $gambarKondisi = null;
        if ($request->hasFile('gambar_kondisi')) {
            // Simpan di folder: storage/app/public/returns
            $gambarKondisi = $request->file('gambar_kondisi')->store('returns', 'public');
        }

        // Update status, tanggal aktual, denda, dan kondisi
        $loan->status = 'kembali';
        $loan->denda = $denda;
        $loan->keterangan_kondisi = $request->keterangan_kondisi; // Tambah
        $loan->gambar_kondisi = $gambarKondisi; // Tambah
        $loan->save();
        // Kembalikan stok alat
        $tool = Tool::find($loan->tool_id);
        if ($tool) {
            $tool->increment('stok');
        }

        // Catat aktivitas
        if (class_exists(ActivityLog::class)) {
            ActivityLog::record(
                'Pengembalian (Petugas)',
                'Memproses pengembalian alat: ' . ($loan->tool->nama_alat ?? '-') . ' dengan denda Rp ' . number_format($denda, 0, ',', '.')
            );
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
