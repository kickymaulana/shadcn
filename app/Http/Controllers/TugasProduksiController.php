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



    // public function terima(DepartemenTerlibat $departemen_terlibat)
    // {
    //     // Ambil urutan master dari departemen yang mau di-klik "Terima" (FQC = 14)
    //     $urutanSekarang = $departemen_terlibat->sub_departemen->urutan;
    //
    //     // Cari departemen sebelumnya yang TERDAFTAR di formulir ini
    //     $sebelumnya = DepartemenTerlibat::where('formulir_id', $departemen_terlibat->formulir_id)
    //         ->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')
    //         ->where('sub_departemen.urutan', '<', $urutanSekarang)
    //         ->select('departemen_terlibat.*', 'sub_departemen.nama', 'sub_departemen.urutan')
    //         ->orderBy('sub_departemen.urutan', 'desc')
    //         ->first();
    //
    //     // Validasi Paraf QC pada departemen sebelumnya
    //     if ($sebelumnya && is_null($sebelumnya->paraf_qc)) {
    //         return back()->with('error', "Gagal! Proses {$sebelumnya->nama} belum di-paraf oleh QC.");
    //     }
    //
    //     // Jika lolos (atau tidak ada departemen sebelumnya), update status terima
    //     if (!$departemen_terlibat->tanggal_diterima) {
    //         $departemen_terlibat->update([
    //             'tanggal_diterima' => now(),
    //             'diterima_oleh' => auth()->id(),
    //         ]);
    //     }
    //
    //     return back()->with('success', 'Tugas berhasil diterima.');
    // }
    //


    public function terima(DepartemenTerlibat $departemen_terlibat)
    {
        // 1. Ambil data user yang sedang login beserta departemennya
        $user = auth()->user();

        // Ambil urutan master dari departemen yang mau di-klik "Terima"
        $urutanSekarang = $departemen_terlibat->sub_departemen->urutan;

        // 2. Cari departemen sebelumnya yang TERDAFTAR di formulir ini
        $sebelumnya = DepartemenTerlibat::where('formulir_id', $departemen_terlibat->formulir_id)
            ->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')
            ->where('sub_departemen.urutan', '<', $urutanSekarang)
            ->select('departemen_terlibat.*', 'sub_departemen.nama', 'sub_departemen.urutan')
            ->orderBy('sub_departemen.urutan', 'desc')
            ->first();

        // 3. Logika Validasi Paraf QC
        // Validasi ini HANYA dijalankan jika user yang login BUKAN dari departemen OVEN
        // Berdasarkan tabelmu, OVEN adalah departemen_id = 7
        if ($user->departemen->nama !== 'OVEN') {
            if ($sebelumnya && is_null($sebelumnya->paraf_qc)) {
                return back()->with('error', "Gagal! Proses {$sebelumnya->nama} belum di-paraf oleh QC.");
            }
        }

        // 4. Jika lolos (karena dia orang OVEN atau karena sudah di-paraf), update status terima
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
