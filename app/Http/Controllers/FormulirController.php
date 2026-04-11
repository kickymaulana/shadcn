<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
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
}
