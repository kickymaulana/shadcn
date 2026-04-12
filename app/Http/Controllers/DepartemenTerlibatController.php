<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formulir;
use App\Models\SubDepartemen;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DepartemenTerlibatController extends Controller
{
    public function create(Formulir $formulir)
    {
        return Inertia::render('Formulir/Departemen/Create', [
            'formulir' => $formulir->load('sampel'),
            // Kita ambil sub_departemen beserta nama departemen induknya
            'list_sub_departemen' => SubDepartemen::with('departemen')->orderBy('urutan')->get(),
            'list_users' => User::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request, Formulir $formulir)
    {
        $validated = $request->validate([
            'departemen_id' => 'required|exists:departemen,id',
            'sub_departemen_id' => 'required|exists:sub_departemen,id',
            'qty' => 'required|integer',
            'item_pemeriksaan' => 'nullable|array',
            'data_tambahan' => 'nullable|array',
        ]);

        $formulir->departemen_terlibat()->create($validated);

        return redirect()->route('formulirs.show', $formulir->id)
            ->with('success', 'Departemen berhasil ditambahkan ke alur proses.');
    }
}
