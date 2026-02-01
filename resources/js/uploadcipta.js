
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('draftForm');
  const fileInput = document.getElementById('draftFile');
  const btnPick = document.getElementById('uploadButton');
  const fileName = document.getElementById('fileName');
  const btnSubmit = document.getElementById('submitUpload'); // hidden submit

  if (!form || !fileInput || !btnPick || !fileName || !btnSubmit) return;

  btnPick.addEventListener('click', () => fileInput.click());

  fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
      fileName.textContent = fileInput.files[0].name;

      // kalau kamu MAU auto upload setelah pilih file, aktifkan baris ini:
      // form.submit();

      // kalau kamu TIDAK mau auto upload, biarkan user klik tombol upload/submit lain.
      // (tapi kamu sekarang submit button hidden, jadi kalau mau manual, tampilkan tombol submit)
    } else {
      fileName.textContent = 'Belum pilih file';
    }
  });
});
