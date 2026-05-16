<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        // Ambil query builder notifikasi milik user yang sedang login
        $notifikasiQuery = auth()->user()->notifications();

        // Fitur Pencarian berdasarkan pesan teks di dalam data JSON
        $notifikasi = $notifikasiQuery
            ->when($request->search, function ($query, $search) {
                $query->where('data->pesan', 'like', "%{$search}%")
                      ->orWhere('data->nomor_sampel', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Notifikasi/Index', [
            'notifikasi' => $notifikasi,
            'filters' => $request->only(['search'])
        ]);
    }

    public function bacaDanRedirect($id)
    {
        // Cari notifikasi berdasarkan ID milik user saat ini
        $notification = auth()->user()->notifications()->findOrFail($id);

        // Tandai sudah dibaca jika belum dibaca
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        // Ambil URL tujuan yang disimpan saat memicu Notification class
        $urlTujuan = $notification->data['url'] ?? route('dashboard');

        // Redirect langsung ke detail departemen_terlibat terkait
        return redirect($urlTujuan)->with('success', 'Notifikasi diperbarui.');
    }
}
