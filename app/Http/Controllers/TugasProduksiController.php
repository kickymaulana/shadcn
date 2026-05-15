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
            // 1. Join ke sub_departemen untuk kolom 'urutan'
            ->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')

            // 2. Join ke formulirs untuk kolom 'running_ke'
            ->join('formulirs', 'departemen_terlibat.formulir_id', '=', 'formulirs.id')

            // 3. Join ke samples untuk kolom 'model'
            ->join('samples', 'formulirs.sampel_id', '=', 'samples.id')

            // 4. Select hanya kolom dari tabel utama agar ID tidak tertimpa join
            ->select('departemen_terlibat.*')

            // Filter status & departemen user
            ->where('formulirs.status', 'Proses')
            ->where('sub_departemen.departemen_id', $user->departemen_id)

            // Eager Loading relasi untuk kebutuhan UI
            ->with([
                'formulir.sampel',
                'sub_departemen',
                'penerima',
                'qcUser',
                'spvUser'
            ])

            // Filter Search (berdasarkan kode_sample di tabel samples)
            ->when($request->search, function ($query, $search) {
                $query->where('samples.kode_sample', 'like', "%{$search}%");
            })

            // --- LOGIKA PENGURUTAN ---

            // 1. Kelompokkan berdasarkan Formulir ID (atau Sampel)
            // Ini memastikan QS, OVEN, BONGKAR milik formulir A tetap berkumpul
            ->orderBy('departemen_terlibat.formulir_id', 'asc')

            // 2. Di dalam formulir yang sama, urutkan berdasarkan alur departemennya
            // Ini yang membuat tampilannya QS -> OVEN -> BONGKAR
            ->orderBy('sub_departemen.urutan', 'asc')

            // 3. Opsional: urutkan berdasarkan running_ke jika ingin melihat iterasi trialnya
            ->orderBy('formulirs.running_ke', 'asc')

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
        $user = auth()->user();

        $subSekarang = $departemen_terlibat->sub_departemen;
        $urutanSekarang = $subSekarang->urutan;
        $namaSubSekarang = strtoupper($subSekarang->nama);
        $indukSekarang = $subSekarang->departemen_id;

        // JIKA BUKAN GLAZE, TERAPKAN VALIDASI KETAT
        if ($namaSubSekarang !== 'GLAZE') {

            // 1. Validasi Urutan: Cek apakah ada proses sebelumnya yang BELUM DITERIMA
            $belumDiterima = DepartemenTerlibat::where('formulir_id', $departemen_terlibat->formulir_id)
                ->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')
                ->where('sub_departemen.urutan', '<', $urutanSekarang)
                ->whereNull('tanggal_diterima')
                ->exists();

            if ($belumDiterima) {
                return back()->with('error', "Gagal! Ada proses sebelumnya yang belum diterima/diproses.");
            }

            // 2. Validasi Paraf QC: Cek proses dari departemen INDUK LAIN yang belum di-paraf
            $masihAdaYangBelumQC = DepartemenTerlibat::where('formulir_id', $departemen_terlibat->formulir_id)
                ->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')
                ->where('sub_departemen.urutan', '<', $urutanSekarang)
                ->where('sub_departemen.departemen_id', '!=', $indukSekarang) // Pakai != untuk integer ID
                ->whereNull('paraf_qc')
                ->select('sub_departemen.nama')
                ->first();

            if ($masihAdaYangBelumQC) {
                return back()->with('error', "Gagal! Proses {$masihAdaYangBelumQC->nama} belum di-paraf oleh QC.");
            }
        }

        // 3. Update status terima
        // Jika dia GLAZE, dia akan langsung loncat ke sini tanpa peduli urutan atau QC
        if (!$departemen_terlibat->tanggal_diterima) {
            $departemen_terlibat->update([
                'tanggal_diterima' => now(),
                'diterima_oleh' => $user->id,
            ]);
        }

        return back()->with('success', 'Tugas berhasil diterima.');
    }





    public function parafSpv(Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        $user = auth()->user();

        // 1. Validasi khusus jika user yang login berasal dari departemen FQC
        // Kita cek berdasarkan departemen_id user atau departemen_id di sub_departemen terkait
        if ($user->departemen_id == 14) {

            // Cek apakah paraf_qc pada record ini masih NULL
            if (is_null($departemen_terlibat->paraf_qc)) {
                return back()->with('error', 'Gagal! Tugas di FQC harus di-paraf oleh QC (Sarah) terlebih dahulu sebelum Supervisor.');
            }
        }

        // 2. Jika bukan FQC atau jika sudah di-paraf QC, lanjutkan update
        $departemen_terlibat->update([
            'paraf_spv' => $user->id,
            'tanggal_selesai' => now(),
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

    public function show($departemen_terlibat_id)
    {
        $dt = DepartemenTerlibat::findOrFail($departemen_terlibat_id);

        $formulir = Formulir::query()
            ->with([
                'sampel',
                'pemeriksa',
                'penyetuju',
                'departemen_terlibat' => function($query) {
                    // Pastikan select tabel utama dilakukan agar ID tidak tertimpa
                    $query->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')
                        ->orderBy('sub_departemen.urutan', 'asc')
                        ->select('departemen_terlibat.*'); // CRITICAL: ID harus tetap milik departemen_terlibat
                },
                'departemen_terlibat.sub_departemen',
                'departemen_terlibat.penerima',
                'departemen_terlibat.qcUser',
                'departemen_terlibat.spvUser'
            ])
            ->findOrFail($dt->formulir_id);

        return Inertia::render('TugasProduksi/Show', [
            'sampel' => $formulir->sampel,
            'formulir' => $formulir,
            'active_dept_id' => $departemen_terlibat_id
        ]);
    }

}
