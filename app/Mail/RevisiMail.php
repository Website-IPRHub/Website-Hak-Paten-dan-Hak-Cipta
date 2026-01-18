<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RevisiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $kategori,
        public string $judul,
        public string $noPendaftaran,
        public array $items, // list revisi
    ) {}

    public function build()
    {
        $mail = $this->subject("[Sistem HKI] Revisi diperlukan - {$this->noPendaftaran}")
            ->view('emails.revisi');

        // attach file admin per item (opsional)
        foreach ($this->items as $it) {
            if (!empty($it['admin_attachment_fullpath']) && file_exists($it['admin_attachment_fullpath'])) {
                $mail->attach($it['admin_attachment_fullpath'], [
                    'as' => $it['admin_attachment_name'] ?? basename($it['admin_attachment_fullpath']),
                ]);
            }
        }

        return $mail;
    }
}
