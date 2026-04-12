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
        return Inertia::render('Formulir/Departemen/Edit', [
            'formulir' => $formulir->load('sampel'),
            'departemen_terlibat' => $departemen_terlibat,
            'list_sub_departemen' => SubDepartemen::with('departemen')->orderBy('urutan')->get(),
            'list_users' => User::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        $validated = $request->validate([
            'sub_departemen_id' => 'required|exists:sub_departemen,id',
            'qty' => 'required|integer',
            'item_pemeriksaan' => 'nullable|array',
            'data_tambahan' => 'nullable|array',
        ]);

        // Ambil departemen_id induk dari sub_departemen yang dipilih
        $sub = SubDepartemen::findOrFail($validated['sub_departemen_id']);
        $validated['departemen_id'] = $sub->departemen_id;

        $departemen_terlibat->update($validated);

        return redirect()->route('formulirs.show', $formulir->id)
            ->with('success', 'Alur departemen berhasil diperbarui.');
    }
}
