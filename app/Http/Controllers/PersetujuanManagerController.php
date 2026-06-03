<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Notifications\SampelSiapDiproses;

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
        // 1. Cek apakah user punya role QC Manager atau Admin
        if (!Auth::user()->hasAnyRole(['QC Manager', 'admin'])) {
            return back()->with('error', 'Akses ditolak.');
        }

        // 2. VALIDASI BARU: Cek apakah ada departemen terlibat yang belum selesai (paraf_spv atau tanggal_selesai masih NULL)
        $adaYangBelumSelesai = $formulir->departemen_terlibat()
            ->where(function ($query) {
                $query->whereNull('paraf_spv')
                    ->orWhereNull('tanggal_selesai');
            })->exists();

        if ($adaYangBelumSelesai) {
            return back()->with('error', 'Gagal! Masih ada sub-departemen yang belum menyelesaikan proses atau belum diparaf oleh SPV.');
        }

        // 3. Jika semua departemen sudah selesai, lanjutkan simpan paraf
        $formulir->update([
            'diperiksa_oleh' => Auth::id(),
        ]);

        // --- Logika kirim WhatsApp & Notifikasi tetap sama seperti sebelumnya ---
        $pakparinton = User::find(3);

        if ($pakparinton) {
            $nomorSampel = $formulir->sampel->kode_sample ?? '-';
            $customer = $formulir->sampel->customer ?? '-';
            $model = $formulir->sampel->model ?? '-';
            $size = $formulir->size ?? '-';
            $running_ke = $formulir->running_ke ?? '-';

            $pesan = "*Notifikasi SISAMSUL*\n\n";
            $pesan .= "Ada sampel baru yang siap untuk disetujui\n";
            $pesan .= "• *Nomor Sampel:* {$nomorSampel}\n";
            $pesan .= "• *Customer:* {$customer}\n";
            $pesan .= "• *Model:* {$model}\n";
            $pesan .= "• *Size:* {$size}\n";
            $pesan .= "• *Running Ke:* {$running_ke}\n\n";
            $pesan .= "_Pesan otomatis dari Sistem Monitoring Sample_";

            $url = route('persetujuan.manager.show', ['formulir' => $formulir->id]);
            $pesan2 = "Sampel baru {$nomorSampel} {$customer} {$model} {$size} run: {$running_ke} siap untuk disetujui";
            $pakparinton->notify(new SampelSiapDiproses($pesan2, $url));

            try {
                $this->kirimWhatsApp($pakparinton->whatsapp, $pesan);
            } catch (\Exception $e) {
                \Log::error("Gagal kirim WA pak parinton: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Dokumen berhasil diperiksa dan diteruskan ke Factory Manager.');
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
