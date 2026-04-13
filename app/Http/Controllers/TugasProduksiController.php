<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepartemenTerlibat;
use App\Models\SubDepartemen;
use App\Models\Sample;
use App\Models\Formulir;
use Inertia\Inertia;

class TugasProduksiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tugas_list = DepartemenTerlibat::query()
            ->whereHas('formulir', function($query) {
                $query->where('status', 'Proses');

            })
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


    public function edit(DepartemenTerlibat $departemen_terlibat)
    {
        // Cukup load 'sampel' saja karena 'customer' adalah kolom di dalam tabel samples
        $formulir = $departemen_terlibat->formulir->load('sampel');

        $departemen_terlibat->load(['sub_departemen', 'penerima', 'qcUser', 'spvUser']);

        return Inertia::render('TugasProduksi/Edit', [
            'formulir' => $formulir,
            'departemen_terlibat' => array_merge($departemen_terlibat->toArray(), [
                'nama_departemen' => $departemen_terlibat->sub_departemen?->nama ?? 'N/A',
                'is_qc' => !is_null($departemen_terlibat->paraf_qc),
                'is_spv' => !is_null($departemen_terlibat->paraf_spv),
                'qc_user' => $departemen_terlibat->qcUser,
                'spv_user' => $departemen_terlibat->spvUser,
            ]),
        ]);
    }

    public function terima(DepartemenTerlibat $departemen_terlibat)
    {
        if (!$departemen_terlibat->tanggal_diterima) {
            $departemen_terlibat->update([
                'tanggal_diterima' => now(),
                'diterima_oleh' => auth()->id(),
            ]);
        }

        // Redirect back akan me-refresh props di halaman Edit.vue secara otomatis
        return back()->with('success', 'Tugas diterima.');
    }



    public function parafSpv(Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        // Pastikan user memiliki hak akses (opsional: bisa tambah role check di sini)

        // Update data paraf supervisor
        $departemen_terlibat->update([
            'paraf_spv' => auth()->id(),
            'tanggal_selesai' => now(), // Mencatat kapan proses di unit ini benar-benar selesai
        ]);

        return back()->with('success', 'Paraf Supervisor berhasil disimpan.');
    }

    public function update(Request $request, DepartemenTerlibat $departemen_terlibat)
    {
        $validated = $request->validate([
            'sub_departemen_id' => 'required|exists:sub_departemen,id',
            'qty'               => 'required|integer',
            'item_pemeriksaan'  => 'nullable|array',
            'data_tambahan'     => 'nullable|array',
        ]);

        // Update data (Laravel otomatis menghandle JSON casting karena sudah ada di Model)
        $departemen_terlibat->update($validated);

        return redirect()->route('tugas.produksi.edit', $departemen_terlibat)
            ->with('success', 'Data berhasil disimpan');
    }
}
