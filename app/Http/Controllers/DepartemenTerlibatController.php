<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formulir;
use App\Models\SubDepartemen;
use App\Models\DepartemenTerlibat;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;

class DepartemenTerlibatController extends Controller
{

    public function create(Formulir $formulir)
    {
        return Inertia::render('Formulir/Departemen/Create', [
            'formulir' => $formulir->load('sampel'),
            // Gunakan has('departemen') untuk memastikan hanya sub yang punya induk yang muncul
            'list_sub_departemen' => SubDepartemen::has('departemen')
                ->with('departemen')
                ->orderBy('urutan')
                ->get(),
            'list_users' => User::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request, Formulir $formulir)
    {
        $validated = $request->validate([
            'sub_departemen_id' => 'required|exists:sub_departemen,id',
            'qty' => 'required|integer',
            'item_pemeriksaan' => 'nullable|array',
            'data_tambahan' => 'nullable|array',
        ]);

        $formulir->departemen_terlibat()->create($validated);

        return redirect()->route('formulirs.show', $formulir->id)
            ->with('success', 'Departemen berhasil ditambahkan ke alur proses.');
    }

    public function edit(Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        // Load relasi agar nama muncul di frontend
        $departemen_terlibat->load('sub_departemen');

        return Inertia::render('Formulir/Departemen/Edit', [
            'formulir' => $formulir->load('sampel'),
            // Tambahkan atribut nama_departemen secara manual agar sesuai dengan pemanggilan di Vue
            'departemen_terlibat' => array_merge($departemen_terlibat->toArray(), [
                'nama_departemen' => $departemen_terlibat->sub_departemen?->nama ?? 'N/A'
            ]),
            'list_sub_departemen' => SubDepartemen::with('departemen')->orderBy('urutan')->get(),
            'list_users' => User::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        $validated = $request->validate([
            'sub_departemen_id' => 'required|exists:sub_departemen,id',
            'qty'               => 'required|integer',
            'item_pemeriksaan'  => 'nullable|array',
            'data_tambahan'     => 'nullable|array',
        ]);

        // Update data (Laravel otomatis menghandle JSON casting karena sudah ada di Model)
        $departemen_terlibat->update($validated);

        return redirect()->route('formulirs.show', $formulir->id)
            ->with('success', 'Alur departemen berhasil diperbarui.');
    }

    public function destroy(Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        // 1. Hapus data dari database
        $departemen_terlibat->delete();

        // 2. Kembalikan ke halaman show formulir dengan pesan sukses
        return redirect()->route('formulirs.show', $formulir->id)
            ->with('success', 'Alur proses departemen berhasil dihapus.');
    }


    public function parafQc(Formulir $formulir, DepartemenTerlibat $departemen_terlibat)
    {
        // 1. Update status paraf QC terlebih dahulu
        $departemen_terlibat->update([
            'paraf_qc' => auth()->id(),
        ]);

        // 2. Cari departemen berikutnya dalam formulir ini berdasarkan urutan sub_departemen
        $nextDeptTerlibat = DepartemenTerlibat::query()
            ->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')
            ->where('departemen_terlibat.formulir_id', $formulir->id)
            ->where('sub_departemen.urutan', '>', $departemen_terlibat->sub_departemen->urutan)
            ->orderBy('sub_departemen.urutan', 'asc')
            ->select('departemen_terlibat.*')
            ->first(); // Ambil 1 yang urutannya paling dekat setelah departemen ini

        if ($nextDeptTerlibat) {
            // --- JIKA ADA DEPARTEMEN LANJUTAN ---

            // Ambil Manager dari departemen tersebut
            $manager = User::role('Manager')
                ->where('departemen_id', $nextDeptTerlibat->sub_departemen->departemen_id)
                ->first();

            if ($manager && $manager->whatsapp) {
                try {
                    $nomorSampel = $formulir->sampel->kode_sample ?? 'N/A';
                    $namaSubDeptNext = $nextDeptTerlibat->sub_departemen->nama;

                    $pesan = "*Notifikasi SISAMSUL*\n\n";
                    $pesan .= "Ada sampel baru dengan nomor: *{$nomorSampel}* yang siap untuk diproses di bagian *{$namaSubDeptNext}*.\n";
                    $pesan .= "Mohon segera dicek dan diterima melalui sistem.\n\n";
                    $pesan .= "_Pesan otomatis dari Sistem Monitoring Sample_";

                    $this->kirimWhatsApp($manager->whatsapp, $pesan);
                } catch (\Exception $e) {
                    \Log::error("Gagal kirim WA Manager: " . $e->getMessage());
                }
            }
        } else {
            // --- JIKA INI ADALAH DEPARTEMEN TERAKHIR (Contoh: FQC) ---
            // Kirim ke Bu Afrida (Penyetuju Akhir)

            try {
                $nomorSampel = $formulir->sampel->kode_sample ?? 'N/A';
                $pesan = "*Notifikasi SISAMSUL*\n\n";
                $pesan .= "Izin Bu Afrida, sampel dengan nomor: *{$nomorSampel}* telah selesai diproses di semua departemen.\n";
                $pesan .= "Mohon kesediaannya untuk melakukan pengecekan akhir dan paraf persetujuan pada sistem.\n\n";
                $pesan .= "_Terima kasih_";

                $this->kirimWhatsApp('6282379728828', $pesan);
            } catch (\Exception $e) {
                \Log::error("Gagal kirim WA Bu Afrida: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Berhasil melakukan Paraf QC dan mengirim notifikasi.');
    }

    /**
    * Helper function agar kode tidak duplikat
    */
    private function kirimWhatsApp($target, $pesan)
    {
        return Http::withoutVerifying()
            ->withBasicAuth('root', 'Sukses1234')
            ->withHeaders(['X-Device-Id' => 'c6d70742-0f1b-414c-b367-0ec156007663'])
            ->post('https://whatsapp.gotechdynamics.com/send/message', [
                'phone'   => $target,
                'message' => $pesan,
            ]);
    }

}
