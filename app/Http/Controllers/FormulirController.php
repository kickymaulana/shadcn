<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\Sample;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormulirController extends Controller
{
    public function index(Request $request): Response
    {
        $list_formulir = Formulir::query()
            ->with('sampel:id,kode_sample,customer') // Ambil data sampel terkait
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
            'list_samples' => Sample::select('id', 'kode_sample', 'customer')->get()
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
        // Kita muat data sampel terkait agar bisa ditampilkan detailnya
        $formulir->load(['sampel', 'pemeriksa', 'penyetuju']);

        return Inertia::render('Formulir/Show', [
            'formulir' => [
                'id'                 => $formulir->id,
                'sampel'             => $formulir->sampel, // Data Master Sample
                'size'               => $formulir->size,
                'qty_sampel_kirim'   => $formulir->qty_sampel_kirim,
                'running_ke'         => $formulir->running_ke,
                'tanggal_permintaan' => $formulir->tanggal_permintaan,
                'status'             => $formulir->status,
                'pemeriksa'          => $formulir->pemeriksa?->name ?? '-',
                'penyetuju'          => $formulir->penyetuju?->name ?? '-',
                'created_at'         => $formulir->created_at->format('Y-m-d H:i:s'),
                'updated_at'         => $formulir->updated_at->format('Y-m-d H:i:s'),
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
        // 1. Validasi sesuai struktur tabel formulirs
        $request->validate([
            'sampel_id'          => 'required|exists:samples,id',
            'size'               => 'required|string|max:255',
            'qty_sampel_kirim'   => 'required|integer|min:1',
            'running_ke'         => 'required|integer|min:1',
            'tanggal_permintaan' => 'required|date',
            'status'             => 'required|in:Draft,Proses,Selesai,Ditolak',
        ]);

        // 2. Update data
        $formulir->update($request->all());

        // 3. Redirect ke halaman detail formulir
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

}
