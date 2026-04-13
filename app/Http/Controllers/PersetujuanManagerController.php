<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
            'departemen_terlibat.sub_departemen',
            'departemen_terlibat.penerima',
            'departemen_terlibat.qcUser', // Relasi ke paraf_qc
            'departemen_terlibat.spvUser'  // Relasi ke paraf_spv
        ]);

        // Sorting berdasarkan urutan sub_departemen di level PHP Collection
        $sortedDepts = $formulir->departemen_terlibat->sortBy(function($dept) {
            return $dept->sub_departemen->urutan ?? 999;
        })->values();

        // Timpa relasi departemen_terlibat dengan yang sudah urut
        $formulir->setRelation('departemen_terlibat', $sortedDepts);

        return Inertia::render('PersetujuanManager/Show', [
            'sampel' => $formulir->sampel,
            'formulir' => $formulir,
        ]);
    }


    public function parafPemeriksa(Formulir $formulir)
    {
        // Cek apakah user punya role QC Manager atau Admin
        if (!Auth::user()->hasAnyRole(['QC Manager', 'admin'])) {
            return back()->with('error', 'Akses ditolak.');
        }

        $formulir->update([
            'diperiksa_oleh' => Auth::id(),
        ]);

        return back();
    }

    public function parafPenyetuju(Formulir $formulir)
    {
        // Cek apakah user punya role Factory Manager atau Admin
        if (!Auth::user()->hasAnyRole(['Factory Manager', 'admin'])) {
            return back()->with('error', 'Akses ditolak.');
        }

        // Pastikan QC sudah paraf duluan
        if (!$formulir->diperiksa_oleh) {
            return back()->with('error', 'Dokumen harus diperiksa QC Manager terlebih dahulu.');
        }

        $formulir->update([
            'disetujui_oleh' => Auth::id(),
            'status' => 'Selesai', // Update status formulir jadi Selesai
        ]);

        return back();
    }
}
