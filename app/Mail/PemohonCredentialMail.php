<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PemohonCredentialMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $username;
    public string $password;
    public string $kodePengajuan;
    public string $type; // created | changed

    public function __construct(string $username, string $password, string $kodePengajuan, string $type = 'created')
    {
        $this->username = $username;
        $this->password = $password;
        $this->kodePengajuan = $kodePengajuan;
        $this->type = $type;
    }

    public function build()
    {
        $subject = $this->type === 'changed'
            ? "[KIHub] Password Akun Pengajuan Diperbarui - {$this->kodePengajuan}"
            : "[KIHub] Kredensial Login Pemohon - {$this->kodePengajuan}";

        return $this->subject($subject)
            ->view('emails.pemohoncredential');
    }
}