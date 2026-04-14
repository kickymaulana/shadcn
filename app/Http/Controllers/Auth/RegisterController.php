<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role; // Tambahkan ini

class RegisterController extends Controller
{

    public function index()
    {
        // Tentukan role yang diizinkan untuk registrasi publik
        $allowedRoles = ['Manager', 'Supervisor', 'Leader', 'Operator'];

        return Inertia::render('Auth/Register', [
            'departemens' => Departemen::all(),
            // Hanya ambil role yang ada di dalam list allowedRoles
            'roles' => Role::whereIn('name', $allowedRoles)->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'departemen_id' => 'required|exists:departemen,id',
            'role' => 'required|exists:roles,name', // Validasi input role
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'whatsapp' => '62821*******',
            'password' => Hash::make($request->password),
            'departemen_id' => $request->departemen_id,
        ]);

        // Berikan role ke user menggunakan trait HasRoles dari Spatie
        $user->assignRole($request->role);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
