<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Tool;
Use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoanController extends Controller
{
    //tampilkan semua data
    public function index()
    {
        $loans = Loan::with(['user', 'tool'])->latest()->paginate(10);
        return view('admin.loans.index', compact('loans'));
    }

    // Form tambah (create)
    public function create()
    {
        //ambil user yang rolenya peminjam saja
        $users = User::where('role', 'peminjam')->get();
        //ambil semua tools
        $tools = Tool::all();

        return view('admin.loans.create', compact('users', 'tools'));
    }

    //simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'tool_id' => 'required',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'status' => 'required'
        ]);
        
        // cek stok jika status langsung disetujui
        $tool = Tool::findOrFail($request->tool_id);
        if($request->status == 'disetujui' && $tool->stok < 1) {
            return back()->with(['error' => 'Stok alat kosong, tidak bisa set status disetujui.']);
        }

        Loan::create([
            'user_id' => $request->user_id,
            'tool_id' => $request->tool_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'status' => $request->status,
            'petugas_id' => Auth::id() //admin yang input dianggap petugas
        ]);

        // kurangi stok jika admin langsung set disetujui
        if ($request->status == 'disetujui') {
            $tool->decrement('stok');
        }

        ActivityLog::record('Create Loan', 'Admin membuat data pinjaman baru');

        return redirect()->route('admin.loans.index')->with('success', 'Data pinjaman berhasil ditambahkan.');
    }

    // form edit
    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        $users = User::where('role', 'peminjam')->get();
        $tools = Tool::all();

        return view('admin.loans.edit', compact('loan', 'users', 'tools'));
    }

    // update data (simpan perubahan)
    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $tool = Tool::findOrFail($request->tool_id);

        // Logika perubahan stok berdasarkan perubahan status
        // 1. jika sebelumnya pending -> diubah menjadi disetujui (stok berkurang)
        if ($loan->status == 'pending' && $request->status == 'disetujui') {
            $tool->decrement('stok');
        }

        //2. jika sebelumnya disetujui -> diubah menjadi kembali (stok bertambah)
        elseif ($loan->status == 'disetujui' && $request->status == 'kembali') {
            $tool->increment('stok');
            $request->merge(['tanggal_kembali_aktual' => now()]);
        }

        //3. jika sebelumnya disetujui -> diubah jadi pending/batal (stok bertambah/koreksi)
        elseif ($loan->status == 'disetujui' && $request->status == 'pending') {
            $tool->increment('stok');
        }

        $loan->update([
            'user_id' => $request->user_id,
            'tool_id' => $request->tool_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'status' => $request->status,
            'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual ?? $loan->tanggal_kembali_aktual
        ]);

        return redirect()->route('admin.loans.index')->with('success', 'Data berhasil diperbarui.');
    }

    //hapus data
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);

        //jika menghapus data yang statusnya masih disetujui (sedang dipinjam), kembalikan stok 
        if ($loan->status == 'disetujui'){
            $loan->tool->increment('stok');
        }

        $loan->delete();
        return redirect()->route('admin.loans.index')->with('success', 'Data berhasil dihapus.');
    }
}