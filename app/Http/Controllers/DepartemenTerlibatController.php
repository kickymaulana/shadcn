<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formulir;
use App\Models\SubDepartemen;
use App\Models\DepartemenTerlibat;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DepartemenTerlibatController extends Controller
{

    public function create(Formulir $formulir)
    {
        return Inertia::render('Formulir/Departemen/Create', [
            'formulir' => $formulir->load('sampel'),
            // Gunakan has('departemen') untuk memastikan hanya sub yang punya induk yang muncul
            'list_sub_departemen' => SubDepartemen::has('departemen')
                ->with('departemen')
                ->orderBy('urutan')
                ->get(),
            'list_users' => User::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request, Formulir $formulir)
    {
        $validated = $request->validate([
            'sub_departemen_id' => 'required|exists:sub_departemen,id',
            'qty' => 'required|integer',
            'item_pemeriksaan' => 'nullable|array',
            'data_tambahan' => 'nullable|array',
        ]);

        $formulir->departemen_terlibat()->create($validated);

        return redirect()->route('formulirs.show', $formulir->id)
            ->with('success', 'Departemen berhasil ditambahkan ke alur proses.');
    }

    public function edit(Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        // Load relasi agar nama muncul di frontend
        $departemen_terlibat->load('sub_departemen');

        return Inertia::render('Formulir/Departemen/Edit', [
            'formulir' => $formulir->load('sampel'),
            // Tambahkan atribut nama_departemen secara manual agar sesuai dengan pemanggilan di Vue
            'departemen_terlibat' => array_merge($departemen_terlibat->toArray(), [
                'nama_departemen' => $departemen_terlibat->sub_departemen?->nama ?? 'N/A'
            ]),
            'list_sub_departemen' => SubDepartemen::with('departemen')->orderBy('urutan')->get(),
            'list_users' => User::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        $validated = $request->validate([
            'sub_departemen_id' => 'required|exists:sub_departemen,id',
            'qty'               => 'required|integer',
            'item_pemeriksaan'  => 'nullable|array',
            'data_tambahan'     => 'nullable|array',
        ]);

        // Update data (Laravel otomatis menghandle JSON casting karena sudah ada di Model)
        $departemen_terlibat->update($validated);

        return redirect()->route('formulirs.show', $formulir->id)
            ->with('success', 'Alur departemen berhasil diperbarui.');
    }

    public function destroy(Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        // 1. Hapus data dari database
        $departemen_terlibat->delete();

        // 2. Kembalikan ke halaman show formulir dengan pesan sukses
        return redirect()->route('formulirs.show', $formulir->id)
            ->with('success', 'Alur proses departemen berhasil dihapus.');
    }
}
