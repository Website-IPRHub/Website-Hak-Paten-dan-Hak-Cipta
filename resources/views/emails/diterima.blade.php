@component('mail::message')
# 🎉 Pengajuan Anda Diterima

Pengajuan **{{ $kategori }}** Anda telah berstatus **DITERIMA**.

@component('mail::panel')
**No Pendaftaran:** {{ $noPendaftaran }}  
**Judul:** {{ $judul }}
@endcomponent

Sertifikat/surat resmi dari DJKI terlampir pada email ini.

@component('mail::button', ['url' => url('/')])
Buka Portal
@endcomponent

Terima kasih,  
**Tim Admin**
@endcomponent
