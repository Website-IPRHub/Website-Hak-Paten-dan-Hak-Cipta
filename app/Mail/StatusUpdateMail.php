<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $kategori;
    public string $judul;
    public string $noPendaftaran;
    public string $status;

    public function __construct(string $kategori, string $judul, string $noPendaftaran, string $status)
    {
        $this->kategori      = $kategori;
        $this->judul         = $judul;
        $this->noPendaftaran = $noPendaftaran;
        $this->status        = $status;
    }

    public function build()
    {
        $subject = "Update Status {$this->kategori} - {$this->noPendaftaran} ({$this->status})";

        return $this->subject($subject)
            ->view('emails.status_update');
    }
}
