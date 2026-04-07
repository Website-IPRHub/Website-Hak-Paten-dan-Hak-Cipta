document.addEventListener('DOMContentLoaded', () => {

  document.querySelectorAll('[data-upload-form]').forEach(form => {

    const fileInput = form.querySelector('input[type="file"]');
    const btnPick   = form.querySelector('[data-btn-pick]');
    const fileName  = form.querySelector('[data-file-name]');
    const btnSubmit = document.querySelector('[data-btn-submit]');

    if (!fileInput || !btnPick || !btnSubmit) return;

    // klik pilih file
    btnPick.addEventListener('click', () => fileInput.click());

    // setelah file dipilih
    fileInput.addEventListener('change', () => {
      if (fileInput.files.length > 0) {
        fileName.textContent = fileInput.files[0].name;
        btnSubmit.removeAttribute('disabled'); 
      } else {
        btnSubmit.setAttribute('disabled', true);
        fileName.textContent = 'Belum pilih file';
      }
    });

  });

});
