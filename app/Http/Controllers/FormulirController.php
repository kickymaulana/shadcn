<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\Sample;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;
use App\Notifications\SampelSiapDiproses;


class FormulirController extends Controller
{
    public function index(Request $request): Response
    {
        $list_formulir = Formulir::query()
            ->with('sampel:id,kode_sample,customer,model') // Ambil data sampel terkait
            ->when($request->search, function ($query, $search) {
                $query->whereHas('sampel', function ($q) use ($search) {
                    $q->where('kode_sample', 'like', "%{$search}%")
                      ->orWhere('customer', 'like', "%{$search}%");
                })->orWhere('status', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Formulir/Index', [
            'list_formulir' => $list_formulir,
            'filters' => $request->only(['search'])
        ]);
    }


    public function create()
    {
        return Inertia::render('Formulir/Create', [
            'list_samples' => Sample::select('id', 'kode_sample', 'customer', 'model')->get()
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'sampel_id'          => 'required|exists:samples,id',
            'size'               => 'required|string|max:255',
            'qty_sampel_kirim'   => 'required|integer|min:1',
            'running_ke'         => 'required|integer|min:1',
            'tanggal_permintaan' => 'required|date',
            'status'             => 'required|in:Draft,Proses,Selesai,Ditolak',
        ]);

        Formulir::create([
            'sampel_id'          => $request->sampel_id,
            'size'               => $request->size,
            'qty_sampel_kirim'   => $request->qty_sampel_kirim,
            'running_ke'         => $request->running_ke,
            'tanggal_permintaan' => $request->tanggal_permintaan,
            'status'             => $request->status,
            // diperiksa_oleh dan disetujui_oleh biasanya diisi saat proses update/approval
        ]);

        return redirect()->route('formulirs.index')
            ->with('success', 'Formulir permintaan berhasil dibuat.');
    }




    public function show(Formulir $formulir): Response
    {
        // 1. Eager Load semua relasi yang dibutuhkan agar tidak N+1 query
        // Kita muat sampel, lalu departemen_terlibat beserta sub_departemen dan departemen induknya
        $formulir->load([
            'sampel',
            'departemen_terlibat.sub_departemen.departemen',
            'departemen_terlibat.penerima',
            'departemen_terlibat.qcUser',
            'departemen_terlibat.spvUser'
        ]);

        // 2. Transformasi data departemen_terlibat
        $departemen_terlibat = $formulir->departemen_terlibat
            ->sortBy(function ($dt) {
                // Urutkan berdasarkan kolom 'urutan' yang ada di tabel sub_departemen
                return $dt->sub_departemen?->urutan ?? 999;
            })
            ->values() // Reset index array setelah disort
            ->map(function ($dt) {
                return [
                    'id'               => $dt->id,
                    'urutan'           => $dt->sub_departemen?->urutan ?? '-',
                    // Kita ambil nama dari sub_departemen (misal: WASHING)
                    'nama_departemen'  => $dt->sub_departemen?->nama ?? 'N/A',
                    // Jika ingin menampilkan Nama Departemen Induk juga (misal: WASHING (PRODUKSI))
                    // 'nama_induk'    => $dt->sub_departemen?->departemen?->nama ?? '-',
                    'penerima'         => $dt->penerima?->name ?? '-',
                    'tanggal_terima'  => $dt->tanggal_diterima ? $dt->tanggal_diterima->format('d M Y H:i') : '-',
                    'tanggal_selesai' => $dt->tanggal_selesai ? $dt->tanggal_selesai->format('d M Y H:i') : '-',
                    'qty'              => $dt->qty ?? 0,
                    'is_qc'            => !is_null($dt->paraf_qc),
                    'is_spv'           => !is_null($dt->paraf_spv),
                    'nama_qc'          => $dt->qcUser?->name ?? '-',
                    'nama_spv'         => $dt->spvUser?->name ?? '-',
                    // Kita sertakan data JSON untuk kebutuhan di frontend jika perlu
                    'item_pemeriksaan' => $dt->item_pemeriksaan,
                    'data_tambahan'    => $dt->data_tambahan,
                ];
            });

        // 3. Render ke Inertia
        return Inertia::render('Formulir/Show', [
            'formulir' => [
                'id'                 => $formulir->id,
                'sampel'             => [
                    'id'          => $formulir->sampel?->id,
                    'kode_sample' => $formulir->sampel?->kode_sample ?? 'N/A',
                    'customer'    => $formulir->sampel?->customer ?? 'N/A',
                    'model'       => $formulir->sampel?->model ?? 'N/A',
                ],
                'size'               => $formulir->size,
                'qty_sampel_kirim'   => $formulir->qty_sampel_kirim,
                'running_ke'         => $formulir->running_ke,
                'tanggal_permintaan' => $formulir->tanggal_permintaan,
                'status'             => $formulir->status,
                'pemeriksa'          => $formulir->pemeriksa ?? '-', // Sesuaikan dengan field di tabel formulirs
                'penyetuju'          => $formulir->penyetuju ?? '-', // Sesuaikan dengan field di tabel formulirs
                'departemen_terlibat'=> $departemen_terlibat,
            ]
        ]);
    }



    public function edit(Formulir $formulir): Response
    {
        return Inertia::render('Formulir/Edit', [
            'formulir' => $formulir,
            'list_samples' => Sample::select('id', 'kode_sample', 'customer')->get()
        ]);
    }

    public function update(Request $request, Formulir $formulir)
    {
        // 1. Gunakan hasil validasi ke dalam variabel
        $validatedData = $request->validate([
            'sampel_id'          => 'required|exists:samples,id',
            'size'               => 'required|string|max:255',
            'qty_sampel_kirim'   => 'required|integer|min:1',
            'running_ke'         => 'required|integer|min:1',
            'tanggal_permintaan' => 'required|date',
            'status'             => 'required|in:Draft,Proses,Selesai,Ditolak',
        ]);

        $statusLama = $formulir->status;

        // 2. Update hanya dengan data yang sudah lolos validasi (lebih aman)
        $formulir->update($validatedData);

        // Amankan pengambilan relasi dengan Eager Loading jika memungkinkan, atau minimal cek keberadaannya
        $departemenPertama = $formulir->departemen_terlibat()
            ->with('sub_departemen.departemen.users') // Opsional: menghemat query (Eager Loading)
            ->get()
            ->sortBy(function ($item) {
                return $item->sub_departemen->urutan ?? 0;
            })
            ->first();

        // 3. Trigger Notifikasi hanya jika status berubah jadi 'Proses' DAN departemennya ada
        if ($statusLama !== 'Proses' && $request->status === 'Proses' && $departemenPertama) {

            $subDepartemen = $departemenPertama->sub_departemen;
            $departemen    = $subDepartemen->departemen ?? null;
            $penerimaNotif = $departemen ? $departemen->users : collect();

            if ($penerimaNotif->isNotEmpty()) {

                // Siapkan variabel text (menggunakan data model terbaru yang fresh dari database)
                $nomorSampel = $formulir->sampel->kode_sample ?? '-';
                $customer    = $formulir->sampel->customer ?? '-';
                $model       = $formulir->sampel->model ?? '-';
                $size        = $formulir->size ?? '-';
                $running_ke  = $formulir->running_ke ?? '-';
                $namaSubDept = $subDepartemen->nama ?? '-';

                $pesan = "*Notifikasi SISAMSUL*\n\n";
                $pesan .= "Ada sampel baru yang siap untuk diproses di bagian *{$namaSubDept}*.\n";
                $pesan .= "• *Nomor Sampel:* {$nomorSampel}\n";
                $pesan .= "• *Customer:* {$customer}\n";
                $pesan .= "• *Model:* {$model}\n";
                $pesan .= "• *Size:* {$size}\n";
                $pesan .= "• *Running Ke:* {$running_ke}\n\n";
                $pesan .= "Mohon segera dicek dan diterima melalui sistem.\n\n";
                $pesan .= "_Pesan otomatis dari Sistem Monitoring Sample_";


                $pesan2 = "Sampel baru {$nomorSampel} {$customer} {$model} {$size} run: {$running_ke} siap untuk diproses di {$namaSubDept}";

                $url = route('tugas.produksi.edit', [
                    'departemen_terlibat' => $departemenPertama->id
                ]);

                foreach ($penerimaNotif as $user) {
                    // Bungkus try-catch DI DALAM loop agar jika 1 user gagal (misal nomor WA tidak valid),
                    // user setelahnya tetap kebagian notifikasi.
                    try {
                        if ($user->whatsapp) {
                            $this->kirimWhatsApp($user->whatsapp, $pesan);
                        }
                        $user->notify(new SampelSiapDiproses($pesan2, $url));
                    } catch (\Exception $e) {
                        \Log::error("Gagal kirim notifikasi ke User ID {$user->id}: " . $e->getMessage());
                    }
                }
            }
        }

        return redirect()
            ->route('formulirs.show', $formulir->id)
            ->with('success', 'Data formulir berhasil diperbarui.');
    }


    public function destroy(Formulir $formulir)
    {
        $formulir->delete();

        return redirect()->route('formulirs.index')
            ->with('success', 'Formulir berhasil dihapus.');
    }

    public function preview(Formulir $formulir)
    {
        $formulir->load([
            'sampel',
            'pemeriksa',
            'penyetuju',
            // Join untuk sorting departemen berdasarkan urutan di tabel sub_departemen
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

        return Inertia::render('Formulir/Preview', [
            'sampel' => $formulir->sampel,
            'formulir' => $formulir,
        ]);
    }

    private function kirimWhatsApp($target, $pesan)
    {
        return Http::withoutVerifying()
            ->withBasicAuth(config('services.wa_gateway.username'), config('services.wa_gateway.password'))
            ->withHeaders(['X-Device-Id' => config('services.wa_gateway.device_id')])
            ->post(config('services.wa_gateway.url'), [
                'phone'   => $target,
                'message' => $pesan,
            ]);
    }


}
