<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SampelSiapDiproses extends Notification
{
    use Queueable;

    protected $pesan;
    protected $url;

    // Kita passing data formulir, ID departemen_terlibat, dan teks pesannya
    public function __construct($pesan, $url)
    {
        $this->pesan = $pesan;
        $this->url = $url;
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
            'pesan' => $this->pesan,
            'url' => $this->url,
        ];
    }
}
