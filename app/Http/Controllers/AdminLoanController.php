<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Tool;
use App\Models\ActivityLog;
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
            'quantity' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'status' => 'required'
        ]);

        // cek stok jika status langsung disetujui
        $tool = Tool::findOrFail($request->tool_id);
        if ($request->status == 'disetujui' && $tool->stok < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi! Sisa stok: ' . $tool->stok . ', Diminta: ' . $request->quantity);
        }

        $loan = Loan::create([
            'user_id' => $request->user_id,
            'tool_id' => $request->tool_id,
            'quantity' => $request->quantity,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'status' => $request->status,
            'petugas_id' => Auth::id() //admin yang input dianggap petugas
        ]);

        // kurangi stok jika admin langsung set disetujui
        if ($request->status == 'disetujui') {
            $tool->decrement('stok', $request->quantity);
        }
        
        ActivityLog::record('Create Loan', 'Admin membuat data pinjaman baru untuk alat: ' . $tool->nama_alat . ' (' . $request->quantity . ' pcs)');

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
        $request->validate([
            'user_id' => 'required',
            'tool_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'status' => 'required'
        ]);

        $loan = Loan::findOrFail($id);
        $tool = Tool::findOrFail($request->tool_id);
        
        // Simpan nilai lama untuk perbandingan
        $statusLama = $loan->status;
        $quantityLama = $loan->quantity;
        $toolIdLama = $loan->tool_id;
        
        // Jika alat berubah, handle stok alat lama
        if ($toolIdLama != $request->tool_id) {
            $toolLama = Tool::find($toolIdLama);
            
            // Kembalikan stok alat lama jika statusnya disetujui
            if ($statusLama == 'disetujui') {
                $toolLama->increment('stok', $quantityLama);
            }
            
            // Cek stok alat baru
            if ($request->status == 'disetujui' && $tool->stok < $request->quantity) {
                return back()->with('error', 'Stok alat baru tidak mencukupi! Sisa stok: ' . $tool->stok . ', Diminta: ' . $request->quantity);
            }
            
            // Kurangi stok alat baru jika status disetujui
            if ($request->status == 'disetujui') {
                $tool->decrement('stok', $request->quantity);
            }
        } 
        else {
            // Alat sama, handle perubahan status dan quantity
            
            // 1. Jika dari disetujui menjadi status lain, kembalikan stok sesuai quantity lama
            if ($statusLama == 'disetujui' && $request->status != 'disetujui') {
                $tool->increment('stok', $quantityLama);
            }
            
            // 2. Jika dari status lain menjadi disetujui, kurangi stok sesuai quantity baru
            elseif ($statusLama != 'disetujui' && $request->status == 'disetujui') {
                // Cek stok mencukupi
                if ($tool->stok < $request->quantity) {
                    return back()->with('error', 'Stok tidak mencukupi! Sisa stok: ' . $tool->stok . ', Diminta: ' . $request->quantity);
                }
                $tool->decrement('stok', $request->quantity);
            }
            
            // 3. Jika tetap disetujui tapi quantity berubah
            elseif ($statusLama == 'disetujui' && $request->status == 'disetujui') {
                if ($quantityLama != $request->quantity) {
                    // Hitung selisih quantity
                    $selisih = $request->quantity - $quantityLama;
                    
                    if ($selisih > 0) {
                        // Quantity bertambah, cek stok dan kurangi
                        if ($tool->stok < $selisih) {
                            return back()->with('error', 'Stok tidak mencukupi untuk penambahan quantity! Sisa stok: ' . $tool->stok . ', Butuh tambahan: ' . $selisih);
                        }
                        $tool->decrement('stok', $selisih);
                    } elseif ($selisih < 0) {
                        // Quantity berkurang, kembalikan stok
                        $tool->increment('stok', abs($selisih));
                    }
                }
            }
            
            // 4. Jika dari kembali/ditolak/pending menjadi kembali/ditolak/pending (tidak ada perubahan stok)
        }
        
        // Update data loan
        $loan->update([
            'user_id' => $request->user_id,
            'tool_id' => $request->tool_id,
            'quantity' => $request->quantity,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'status' => $request->status,
            'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual ?? $loan->tanggal_kembali_aktual
        ]);

        // Catat aktivitas dengan detail
        $activityDesc = "Memperbarui data pinjaman ID: {$loan->id}. ";
        $activityDesc .= "Status: {$statusLama} → {$request->status}, ";
        $activityDesc .= "Quantity: {$quantityLama} → {$request->quantity}, ";
        $activityDesc .= "Alat: " . ($toolIdLama != $request->tool_id ? "berubah" : "tetap");
        
        ActivityLog::record('Update Loan', $activityDesc);
        
        return redirect()->route('admin.loans.index')->with('success', 'Data berhasil diperbarui.');
    }

    //hapus data
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);

        // Jika menghapus data yang statusnya masih disetujui (sedang dipinjam), kembalikan stok sesuai quantity
        if ($loan->status == 'disetujui') {
            $loan->tool->increment('stok', $loan->quantity);
        }

        $loan->delete();
        ActivityLog::record('Delete Loan', 'Admin menghapus data pinjaman ID: ' . $loan->id . ' (' . $loan->quantity . ' pcs)');
        
        return redirect()->route('admin.loans.index')->with('success', 'Data berhasil dihapus.');
    }
}