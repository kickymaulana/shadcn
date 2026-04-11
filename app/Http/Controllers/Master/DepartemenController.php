<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departemen;
use Inertia\Inertia;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $list_departemen = Departemen::query()
            // Mengambil kolom yang diperlukan
            ->select('id', 'nama', 'created_at')
            // Menghitung jumlah user di setiap departemen secara otomatis
            ->withCount('users')
            // Logika pencarian
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%");
                // Jika ada kolom 'code', bisa ditambah: ->orWhere('code', 'like', "%{$search}%")
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Departemen/Index', [
            'list_departemen' => $list_departemen,
            'filters' => $request->only(['search'])
        ]);
    }
}
