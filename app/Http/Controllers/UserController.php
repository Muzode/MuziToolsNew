<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     */
    public function index(Request $request)
    {
        // Fitur pencarian sederhana (opsional)
        $query = User::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%');
        }
        $users = $query->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }
    /**
     * Form tambah user.
     */
    public function create()
    {
        return view('admin.users.create');
    }
    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,petugas,peminjam',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'role' => $request->role,
        ]);
        ActivityLog::record('Tambah User', 'Menambahkan user baru: ' . $user->name . ' (' . $user->role . ')');
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }
    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }
    /**
     * Update data user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Validasi email unique kecuali untuk user ini sendiri
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,petugas,peminjam',
            'password' => 'nullable|min:6', // Password boleh kosong jika tidak ingin diganti
        ]);
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,

        ];
        // Jika password diisi, update password baru
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        ActivityLog::record('Update User', 'Memperbarui data user: ' . $user->name);
        return redirect()->route('users.index')->with('success', 'Data user diperbarui.');
    }
    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        // Mencegah admin menghapus akunnya sendiri yang sedang login
        if ($user->id == Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.']);
        }
        $nama = $user->name;
        $user->delete();
        ActivityLog::record('Hapus User', 'Menghapus user: ' . $nama);
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
