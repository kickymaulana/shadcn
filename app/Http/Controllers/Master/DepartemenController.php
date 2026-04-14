<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departemen;
use Inertia\Inertia;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $list_departemen = Departemen::query()
            // Mengambil kolom yang diperlukan
            ->select('id', 'nama', 'created_at')
            // Menghitung jumlah user di setiap departemen secara otomatis
            ->withCount('users')
            // Logika pencarian
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%");
                // Jika ada kolom 'code', bisa ditambah: ->orWhere('code', 'like', "%{$search}%")
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Departemen/Index', [
            'list_departemen' => $list_departemen,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Departemen/Create');
    }


    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nama' => 'required|string|max:255|unique:departemen,nama',
        ], [
            // Custom message agar lebih user-friendly
            'nama.unique' => 'Nama departemen sudah terdaftar.',
            'nama.required' => 'Nama departemen wajib diisi.',
        ]);

        // 2. Simpan data ke database
        // Pastikan nama modelnya 'Departemen' atau 'Department' sesuai yang kamu buat
        Departemen::create([
            'nama' => $request->nama,
        ]);

        // 3. Redirect kembali ke index dengan pesan sukses
        return redirect()
            ->route('departemens.index')
            ->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    public function show(Departemen $departemen)
    {
        // Kita load relasi users agar bisa ditampilkan siapa saja anggotanya
        $departemen->load('users:id,name,username,email,departemen_id');

        return Inertia::render('Master/Departemen/Show', [
            'departemen' => [
                'id' => $departemen->id,
                'nama' => $departemen->nama,
                'created_at' => $departemen->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $departemen->updated_at->format('Y-m-d H:i:s'),
                'users' => $departemen->users, // Daftar user di departemen ini
            ]
        ]);
    }

    public function edit(Departemen $departemen)
    {
        return Inertia::render('Master/Departemen/Edit', [
            'departemen' => $departemen
        ]);
    }


    public function update(Request $request, Departemen $departemen)
    {
        // 1. Validasi
        $request->validate([
            // unique:nama_tabel,nama_kolom,id_yang_dikecualikan
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:departemen,nama,' . $departemen->id
            ],
        ], [
            'nama.unique' => 'Nama departemen ini sudah digunakan.',
            'nama.required' => 'Nama departemen tidak boleh kosong.',
        ]);

        // 2. Update data
        $departemen->update([
            'nama' => $request->nama,
        ]);

        // 3. Redirect ke halaman show atau index
        return redirect()
            ->route('departemens.show', $departemen->id)
            ->with('success', 'Nama departemen berhasil diperbarui.');
    }


    public function destroy(Departemen $departemen)
    {
        // 1. Proteksi: Cek apakah masih ada user di departemen ini (Opsional tapi direkomendasikan)
        // Jika kamu ingin mencegah penghapusan jika masih ada anggota:
        if ($departemen->users()->count() > 0) {


            return back()->with('error', 'Departemen tidak bisa dihapus karena masih memiliki anggota.');
        }

        // 2. Hapus data
        $departemen->delete();

        // 3. Redirect
        return redirect()
            ->route('departemens.index')
            ->with('success', 'Departemen berhasil dihapus dari sistem.');
    }

}
