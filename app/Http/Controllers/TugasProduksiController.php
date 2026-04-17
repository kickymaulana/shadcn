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
            // 1. Lakukan Join dengan sub_departemen agar bisa akses kolom 'urutan'
            ->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')

            // 2. Pastikan select kolom dari departemen_terlibat agar ID tidak tertimpa
            ->select('departemen_terlibat.*')

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

            // 3. Urutkan berdasarkan kolom urutan di tabel sub_departemen
            ->orderBy('sub_departemen.urutan', 'asc')
            // Bisa juga ditambah urutan kedua jika urutan departemen sama (misal berdasarkan yang terbaru)
            ->orderBy('departemen_terlibat.created_at', 'desc')

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
