<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SampelSiapDiproses extends Notification
{
    use Queueable;

    protected $formulir;
    protected $deptTerlibatId;
    protected $pesanTeks;

    // Kita passing data formulir, ID departemen_terlibat, dan teks pesannya
    public function __construct($formulir, $deptTerlibatId, $pesanTeks)
    {
        $this->formulir = $formulir;
        $this->deptTerlibatId = $deptTerlibatId;
        $this->pesanTeks = $pesanTeks;
    }

    // Aktifkan channel 'database'
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // Data inilah yang akan disimpan di kolom `data` (format JSON) di tabel notifications
    public function toArray(object $notifiable): array
    {
        return [
            'formulir_id' => $this->formulir->id,
            'departemen_terlibat_id' => $this->deptTerlibatId, // ID ini untuk link detail nanti
            'nomor_sampel' => $this->formulir->sampel->kode_sample ?? '-',
            'pesan' => $this->pesanTeks,
            'url' => route('tugas.produksi.edit', $this->deptTerlibatId), // Sesuaikan dengan nama route detail Anda
        ];
    }
}
