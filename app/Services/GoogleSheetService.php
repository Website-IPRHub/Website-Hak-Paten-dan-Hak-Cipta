<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\DB;

class GoogleSheetService
{
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $client = new Client();

        $client->setApplicationName('Laravel Google Sheets');
        $client->setScopes([Sheets::SPREADSHEETS]);

        $credentialPath = base_path(env('GOOGLE_SHEETS_CREDENTIALS'));


        if (!file_exists($credentialPath)) {
            throw new \Exception("File credentials Google Sheets tidak ditemukan: " . $credentialPath);
        }

        $client->setAuthConfig($credentialPath);

        $this->service = new Sheets($client);
        $this->spreadsheetId = env('GOOGLE_SHEET_ID');
    }

    public function kirim($verif)
    {
        $revisiLatest = DB::table('revisions')
            ->where('type', 'cipta')
            ->where('ref_id', $verif->id)
            ->where('from_role', 'pemohon')
            ->where('state', 'submitted')
            ->orderByDesc('id')
            ->get()
            ->groupBy('doc_key');

        $getRev = function ($key) use ($revisiLatest) {
            return ($revisiLatest[$key] ?? collect())->first();
        };

        $suratPermohonan = $getRev('surat_permohonan');
        $suratPernyataan = $getRev('surat_pernyataan');
        $suratPengalihan = $getRev('surat_pengalihan');
        $tandaTerima     = $getRev('tanda_terima');
        $scanKtp         = $getRev('scan_ktp');
        $hasilCiptaan    = $getRev('hasil_ciptaan');
        $linkCiptaan     = $getRev('link_ciptaan');

        $suratPermohonanPath = $suratPermohonan?->pemohon_file_path ?: $verif->surat_permohonan;
        $suratPernyataanPath = $suratPernyataan?->pemohon_file_path ?: $verif->surat_pernyataan;
        $suratPengalihanPath = $suratPengalihan?->pemohon_file_path ?: $verif->surat_pengalihan;
        $tandaTerimaPath     = $tandaTerima?->pemohon_file_path ?: $verif->tanda_terima;
        $scanKtpPath         = $scanKtp?->pemohon_file_path ?: $verif->scan_ktp;
        $hasilCiptaanPath    = $hasilCiptaan?->pemohon_file_path ?: $verif->hasil_ciptaan;
        $linkCiptaanValue    = $linkCiptaan?->pemohon_text ?: $verif->link_ciptaan;

        $toPublicUrl = function ($path) {
            if (!$path) return '';
            $path = ltrim((string) $path, '/');
            $path = preg_replace('#^storage/#', '', $path);
            return asset('storage/' . $path);
        };

        $inventors = is_string($verif->inventors)
            ? json_decode($verif->inventors, true) ?? []
            : ($verif->inventors ?? []);

        $nama     = collect($inventors)->pluck('nama')->join(', ');
        $nipNim   = collect($inventors)->pluck('nip_nim')->join(', ');
        $fakultas = collect($inventors)->pluck('fakultas')->join(', ');
        $email    = collect($inventors)->pluck('email')->join(', ');
        $noHp     = collect($inventors)->pluck('no_hp')->join(', ');

        $values = [[
            now()->timezone('Asia/Jakarta')->format('n/j/Y H:i:s'),
            (string) $verif->jenis_cipta,
            (string) $verif->judul_cipta,
            (string) $nama,
            (string) $nipNim,
            (string) $fakultas,
            (string) $noHp,
            (string) $email,
            (string) $verif->nilai_perolehan,
            (string) $verif->sumber_dana,
            (string) $verif->skema_penelitian,
            $toPublicUrl($suratPermohonanPath),
            $toPublicUrl($suratPernyataanPath),
            $toPublicUrl($suratPengalihanPath),
            $toPublicUrl($tandaTerimaPath),
            $toPublicUrl($scanKtpPath),
            $toPublicUrl($hasilCiptaanPath),
            (string) $linkCiptaanValue,
        ]];

        $body = new ValueRange([
            'majorDimension' => 'ROWS',
            'values' => $values,
        ]);

        $sheetName = "Form Responses 1";
        $range = "'$sheetName'!A:R";

        return $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            [
                'valueInputOption' => 'USER_ENTERED',
                'insertDataOption' => 'INSERT_ROWS'
            ]
        );
    }
}