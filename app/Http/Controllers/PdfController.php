<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formulir;

class PdfController extends Controller
{
    public function pdf(Formulir $formulir)
    {
        // Eager loading semua relasi yang dibutuhkan template blade
        $formulir->load([
            'sampel',
            'pemeriksa',
            'penyetuju',
            'departemen_terlibat' => function($query) {
                // Pastikan select tabel utama dilakukan agar ID tidak tertimpa
                $query->join('sub_departemen', 'departemen_terlibat.sub_departemen_id', '=', 'sub_departemen.id')
                    ->orderBy('sub_departemen.urutan', 'asc')
                    ->select('departemen_terlibat.*'); // CRITICAL: ID harus tetap milik departemen_terlibat
            },
            'departemen_terlibat.sub_departemen',
            'departemen_terlibat.penerima',
            'departemen_terlibat.qcUser',
            'departemen_terlibat.spvUser'
        ]);

        $data = [
            'sampel' => $formulir->sampel,
            'formulir' => $formulir,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf', $data)
                ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-'.$formulir->sampel->kode_sample.'.pdf');
    }
}
