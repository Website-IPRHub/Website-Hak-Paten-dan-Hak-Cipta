<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DiterimaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $kategori;
    public $judul;
    public $noPendaftaran;

    protected $attachmentFullPath;
    protected $attachmentName;

    public function __construct($kategori, $judul, $noPendaftaran, $attachmentFullPath = null, $attachmentName = null)
    {
        $this->kategori = $kategori;
        $this->judul = $judul;
        $this->noPendaftaran = $noPendaftaran;
        $this->attachmentFullPath = $attachmentFullPath;
        $this->attachmentName = $attachmentName;
    }

    public function build()
    {
        $mail = $this->subject("Pengajuan {$this->kategori} Diterima - {$this->noPendaftaran}")
            ->markdown('emails.diterima');

        if ($this->attachmentFullPath && file_exists($this->attachmentFullPath)) {
            $mail->attach($this->attachmentFullPath, [
                'as' => $this->attachmentName ?? 'sertifikat_djki.pdf',
            ]);
        }

        return $mail;
    }
}
