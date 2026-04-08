<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    public function index()
    {
        // Gunakan paginate() alih-alih get()
        $users = User::select('id', 'name', 'username', 'email', 'created_at')
            ->latest()
            ->paginate(10) // Tampilkan 10 user per halaman
            ->withQueryString(); // Agar filter/pencarian tetap ada saat pindah halaman

        return Inertia::render('Master/Users/Index', [
            'users' => $users
        ]);
    }




    public function create()
    {
        return Inertia::render('Master/Users/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }
}
