<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\SubDepartemen;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubDepartemenController extends Controller
{
    public function index(Request $request)
    {
        $list_sub = SubDepartemen::query()
            ->with('departemen:id,nama')
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                      ->orWhereHas('departemen', function($q) use ($search) {
                          $q->where('nama', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/SubDepartemen/Index', [
            'list_sub' => $list_sub,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/SubDepartemen/Create', [
            'departemens' => Departemen::select('id', 'nama')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'departemen_id' => 'required|exists:departemen,id',
            'nama' => 'required|string|max:255',
        ]);

        SubDepartemen::create($request->all());

        return redirect()->route('sub.departemens.index')->with('success', 'Sub Departemen berhasil dibuat.');
    }

    public function show(SubDepartemen $subDepartemen)
    {
        $subDepartemen->load('departemen');
        return Inertia::render('Master/SubDepartemen/Show', ['sub' => $subDepartemen]);
    }

    public function edit(SubDepartemen $subDepartemen)
    {
        return Inertia::render('Master/SubDepartemen/Edit', [
            'sub' => $subDepartemen,
            'departemens' => Departemen::select('id', 'nama')->get()
        ]);
    }

    public function update(Request $request, SubDepartemen $subDepartemen)
    {
        $request->validate([
            'departemen_id' => 'required|exists:departemen,id',
            'nama' => 'required|string|max:255',
        ]);

        $subDepartemen->update($request->all());

        return redirect()->route('sub.departemens.index')->with('success', 'Sub Departemen diperbarui.');
    }

    public function destroy(SubDepartemen $subDepartemen)
    {
        $subDepartemen->delete();
        return redirect()->route('sub.departemens.index')->with('success', 'Sub Departemen dihapus.');
    }
}
