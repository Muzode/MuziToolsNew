<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog; // Untuk mencatat log
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori.
     */
    public function index()
    {
        // Ambil data kategori + hitung jumlah alat di dalamnya (tools_count)
        $categories = Category::withCount('tools')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }
    /**
     * Form tambah kategori.
     */
    public function create()
    {
        return view('admin.categories.create');
    }
    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([

            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori'
        ]);
        Category::create([
            'nama_kategori' => $request->nama_kategori
        ]);
        ActivityLog::record('Create Kategori', 'Menambah kategori: ' . $request->nama_kategori);
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }
    /**
     * Form edit kategori.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }
    /**
     * Update kategori.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $category->id
        ]);
        $oldName = $category->nama_kategori;
        $category->update([
            'nama_kategori' => $request->nama_kategori
        ]);
        ActivityLog::record('Update Kategori', "Mengubah kategori $oldName menjadi " . $request->nama_kategori);
        return redirect()->route('categories.index')->with('success', 'Kategori diperbarui.');
    }
    /**
     * Hapus kategori.
     */
    public function destroy(Category $category)
    {
        // Cek apakah kategori ini masih dipakai di tabel tools?
        // Kita menggunakan method tools() dari relasi di Model Category
        if ($category->tools()->count() > 0) {
            return back()->withErrors(['error' => 'Kategori tidak bisa dihapus karena masih memiliki data Alat. Hapus atau pindahkan alatnya terlebih dahulu.']);
        }
        $nama = $category->nama_kategori;
        $category->delete();
        ActivityLog::record('Delete Kategori', 'Menghapus kategori: ' . $nama);
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
