<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::query()
            // Kita hitung jumlah user yang punya role ini secara otomatis
            ->withCount('users')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('guard_name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Roles/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Nama role harus unik di tabel roles
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web' // Default guard untuk aplikasi web
        ]);

        return redirect()->route('roles.index')->with('success', 'Jabatan baru berhasil ditambahkan.');
    }
}
