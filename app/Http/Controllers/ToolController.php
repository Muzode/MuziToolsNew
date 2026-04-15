<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\Category;
use App\Models\ActivityLog; // Menggunakan Log yang kita buat sebelumnya

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ToolController extends Controller
{
    /**
     * Menampilkan daftar alat (Read).
     */
    public function index()
    {
        // Mengambil data alat, diurutkan terbaru, dengan pagination 10 per halaman
        // 'with' digunakan untuk Eager Loading relasi kategori agar query lebih ringan
        $tools = Tool::with('category')->latest()->paginate(10);
        return view('admin.tools.index', compact('tools'));
    }
    /**
     * Menampilkan form tambah alat (Create).
     */
    public function create()
    {
        $categories = Category::all(); // Kita butuh data kategori untuk dropdown
        return view('admin.tools.create', compact('categories'));
    }
    /**
     * Menyimpan data alat baru ke database (Store).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'deskripsi' => 'nullable|string'
        ]);
        // 2. Handle Upload Gambar (Jika ada)
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            // Simpan di folder: storage/app/public/tools
            $gambarPath = $request->file('gambar')->store('tools', 'public');
        }
        // 3. Simpan ke Database
        Tool::create([
            'nama_alat' => $request->nama_alat,
            'category_id' => $request->category_id,
            'stok' => $request->stok,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath
        ]);
        // 4. Catat Log
        ActivityLog::record('Create Alat', 'Menambahkan alat baru: ' . $request->nama_alat);
        return redirect()->route('tools.index')->with('success', 'Alat berhasil ditambahkan.');
    }
    /**
     * Menampilkan form edit alat.
     */
    public function edit(Tool $tool)
    {
        $categories = Category::all();
        return view('admin.tools.edit', compact('tool', 'categories'));
    }
    /**
     * Memperbarui data alat (Update).
     */
    public function update(Request $request, Tool $tool)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

        ]);
        $data = $request->except(['gambar']);
        // Handle Ganti Gambar
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($tool->gambar && Storage::disk('public')->exists($tool->gambar)) {
                Storage::disk('public')->delete($tool->gambar);
            }
            // Simpan gambar baru
            $data['gambar'] = $request->file('gambar')->store('tools', 'public');
        }
        $tool->update($data);
        ActivityLog::record('Update Alat', 'Memperbarui data alat: ' . $tool->nama_alat);
        return redirect()->route('tools.index')->with('success', 'Data alat diperbarui.');
    }
    /**
     * Menghapus alat (Delete).
     */
    public function destroy(Tool $tool)
    {
        // Hapus file gambar dari storage jika ada
        if ($tool->gambar && Storage::disk('public')->exists($tool->gambar)) {
            Storage::disk('public')->delete($tool->gambar);
        }
        $namaAlat = $tool->nama_alat;
        $tool->delete();
        ActivityLog::record('Delete Alat', 'Menghapus alat: ' . $namaAlat);
        return redirect()->route('tools.index')->with('success', 'Alat berhasil dihapus.');
    }
}
