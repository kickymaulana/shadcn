<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'username', 'email', 'created_at')
            ->latest()
            ->get();

        return Inertia::render('Master/Users/Index', [
            'users' => $users
        ]);
    }
}
