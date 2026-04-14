<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sample;
use Inertia\Inertia;
use Inertia\Response;

class SampleController extends Controller
{
    public function index(Request $request): Response
    {
        $list_samples = Sample::query()
            // Memilih kolom sesuai struktur tabel sample Anda
            ->select('id', 'kode_sample', 'diajukan_oleh', 'customer', 'model', 'created_at')
            // Logika pencarian berdasarkan kode atau customer
            ->when($request->search, function ($query, $search) {
                $query->where('kode_sample', 'like', "%{$search}%")
                      ->orWhere('customer', 'like', "%{$search}%")
                      ->orWhere('diajukan_oleh', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Sample/Index', [
            'list_samples' => $list_samples,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Sample/Create');
    }

    public function store(Request $request)
    {
        // 1. Validasi input sesuai kolom tabel samples
        $request->validate([
            'kode_sample'   => 'required|string|max:255|unique:samples,kode_sample',
            'diajukan_oleh' => 'required|string|max:255',
            'kepada'        => 'required|string|max:255',
            'customer'      => 'required|string|max:255',
            'model'         => 'required|string|max:255',
            'spesifikasi'   => 'required|string|max:255',
        ], [
            'kode_sample.unique' => 'Kode sample sudah digunakan.',
            'kode_sample.required' => 'Kode sample wajib diisi.',
        ]);

        // 2. Simpan data
        Sample::create([
            'kode_sample'   => $request->kode_sample,
            'diajukan_oleh' => $request->diajukan_oleh,
            'kepada'        => $request->kepada,
            'customer'      => $request->customer,
            'model'         => $request->model,
            'spesifikasi'   => $request->spesifikasi,
        ]);

        // 3. Redirect
        return redirect()
            ->route('samples.index')
            ->with('success', 'Data sample berhasil ditambahkan.');
    }

    public function show(Sample $sample): Response
    {
        // Jika Anda ingin melihat formulir apa saja yang terkait dengan sample ini:
        // $sample->load('formulirs');

        return Inertia::render('Sample/Show', [
            'sample' => [
                'id'            => $sample->id,
                'kode_sample'   => $sample->kode_sample,
                'diajukan_oleh' => $sample->diajukan_oleh,
                'kepada'        => $sample->kepada,
                'customer'      => $sample->customer,
                'model'         => $sample->model,
                'spesifikasi'   => $sample->spesifikasi,
                'created_at'    => $sample->created_at->format('Y-m-d H:i:s'),
                'updated_at'    => $sample->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function edit(Sample $sample): Response
    {
        return Inertia::render('Sample/Edit', [
            'sample' => $sample
        ]);
    }

    public function update(Request $request, Sample $sample)
    {
        // 1. Validasi sesuai kolom tabel samples
        $request->validate([
            'kode_sample'   => 'required|string|max:255|unique:samples,kode_sample,' . $sample->id,
            'diajukan_oleh' => 'required|string|max:255',
            'kepada'        => 'required|string|max:255',
            'customer'      => 'required|string|max:255',
            'model'         => 'required|string|max:255',
            'spesifikasi'   => 'required|string|max:255',
        ], [
            'kode_sample.unique' => 'Kode sample ini sudah digunakan.',
        ]);

        // 2. Update data
        $sample->update($request->all());

        // 3. Redirect ke halaman detail
        return redirect()
            ->route('samples.show', $sample->id)
            ->with('success', 'Data sample berhasil diperbarui.');
    }

    public function destroy(Sample $sample)
    {
        // Opsional: Cek apakah sample ini sudah digunakan di tabel formulir
        // if ($sample->formulirs()->count() > 0) {
        //     return back()->with('error', 'Data tidak bisa dihapus karena sudah memiliki riwayat formulir.');
        // }

        $sample->delete();

        return redirect()
            ->route('samples.index')
            ->with('success', 'Data sample berhasil dihapus.');
    }

}
