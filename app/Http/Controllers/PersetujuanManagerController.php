<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersetujuanManagerController extends Controller
{
    public function index(Request $request)
    {
        $list_persetujuan = Formulir::query()
            ->where('status', 'proses')
            ->with(['sampel', 'pemeriksa', 'penyetuju'])
            // Logika pencarian berdasarkan kode sampel atau customer
            ->when($request->search, function ($query, $search) {
                $query->whereHas('sampel', function ($q) use ($search) {
                    $q->where('kode_sample', 'like', "%{$search}%")
                      ->orWhere('customer', 'like', "%{$search}%");
                });
            })
            // Filter status agar hanya yang perlu disetujui (opsional)
            // ->where('status', 'Proses')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('PersetujuanManager/Index', [
            'list_persetujuan' => $list_persetujuan,
            'filters' => $request->only(['search'])
        ]);
    }




    public function show(Formulir $formulir)
    {
        $formulir->load([
            'sampel',
            'pemeriksa',
            'penyetuju',
            // Kita gunakan join agar bisa sorting berdasarkan kolom di tabel sub_departemen
            'departemen_terlibat' => function($query) {
                $query->select('departemen_terlibat.*')
                    ->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')
                    ->orderBy('sub_departemen.urutan', 'asc');
            },
            'departemen_terlibat.sub_departemen',
            'departemen_terlibat.penerima',
            'departemen_terlibat.qcUser',
            'departemen_terlibat.spvUser'
        ]);

        return Inertia::render('PersetujuanManager/Show', [
            'sampel' => $formulir->sampel,
            'formulir' => $formulir,
        ]);
    }
}
