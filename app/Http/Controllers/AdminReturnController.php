<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Tool;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
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
            'loan_id' => 'required|exists:loans,id',
            'tanggal_kembali_aktual' => 'required|date',
            'keterangan_kondisi' => 'nullable|string|max:500', // Tambah ini
            'gambar_kondisi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Tambah ini
        ]);

        $loan = Loan::findOrFail($request->loan_id);

        if ($loan->status !== 'disetujui') {
            return back()->with('error', 'Peminjaman tidak valid atau sudah dikembalikan.');
        }

        $tanggalAktual = Carbon::parse($request->tanggal_kembali_aktual);
        $tanggalRencana = Carbon::parse($loan->tanggal_kembali_rencana);

        // Hitung denda (logic sudah ada)
        $denda = 0;
        $hariTelat = 0;

        if ($tanggalAktual->gt($tanggalRencana)) {
            $hariTelat = $tanggalRencana->diffInDays($tanggalAktual);
            $denda = $hariTelat * ($loan->denda_per_hari ?? 5000);
        }

        // Handle file upload
        $gambarKondisi = null;
        // 2. Handle Upload Gambar (Jika ada)
        $gambarKondisi = null;
        if ($request->hasFile('gambar_kondisi')) {
            // Simpan di folder: storage/app/public/tools
            $gambarKondisi = $request->file('gambar_kondisi')->store('returns', 'public');

            // Simpan path ke database
            $loan->gambar_kondisi = 'returns/' . $gambarKondisi;
            $loan->save(); 
        }        // Update loan dengan data kondisi
        $loan->update([
            'status' => 'kembali',
            'tanggal_kembali_aktual' => $tanggalAktual,
            'denda' => $denda,
            'keterangan_kondisi' => $request->keterangan_kondisi, // Tambah
            'gambar_kondisi' => $gambarKondisi // Tambah
        ]);

        // Kembalikan stok alat
        $tool = Tool::find($loan->tool_id);
        if ($tool) {
            $tool->increment('stok');
        }

        // Catat aktivitas
        ActivityLog::record(
            'Pengembalian (Admin)',
            'Memproses pengembalian alat: ' . ($loan->tool->nama_alat ?? '-') . ' dengan denda Rp ' . number_format($denda, 0, ',', '.')
        );

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
            'tanggal_kembali_aktual' => 'required|date',
            'gambar_kondisi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Siapkan data untuk update
        $data = [
            'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual
        ];

        // Handle upload gambar baru
        if ($request->hasFile('gambar_kondisi')) {
            // Hapus gambar lama jika ada
            if ($loan->gambar_kondisi && Storage::disk('public')->exists($loan->gambar_kondisi)) {
                Storage::disk('public')->delete($loan->gambar_kondisi);
            }

            // Simpan gambar baru
            $data['gambar_kondisi'] = $request->file('gambar_kondisi')->store('returns', 'public');
        }

        // Update data sekaligus (hanya sekali)
        $loan->update($data);

        ActivityLog::record('Update Pengembalian', 'Memperbarui data pengembalian: ' . $loan->tool->nama_alat);

        return redirect()->route('admin.returns.index')->with('success', 'Data pengembalian diperbarui.');
    }
    /**
     * DESTROY: Hapus riwayat pengembalian
     */
    public function destroy($id, Loan $loan)
    {
        if ($loan->gambar_kondisi && Storage::disk('public')->exists($loan->gambar_kondisi)) {
            Storage::disk('public')->delete($loan->gambar_kondisi);
        }

        $loan = Loan::findOrFail($id);
        $loan->delete();
        return redirect()->route('admin.returns.index')->with('success', 'Riwayat dihapus.');
    }
}
