<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepartemenTerlibat;
use Inertia\Inertia;

class TugasProduksiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tugas_list = DepartemenTerlibat::query()
            // Filter berdasarkan departemen user login
            ->whereHas('sub_departemen', function ($query) use ($user) {
                $query->where('departemen_id', $user->departemen_id);
            })
            ->with([
                'formulir.sampel',
                'sub_departemen',
                'penerima',
                'qcUser',
                'spvUser'
            ])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('formulir.sampel', function ($q) use ($search) {
                    $q->where('kode_sample', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('TugasProduksi/Index', [
            'tugas_list' => $tugas_list,
            'filters' => $request->only(['search'])
        ]);
    }
}
