document.addEventListener('input', function (e) {
  const el = e.target;
  const val = el.value.trim();

  // ================= NIP / NIM =================
  if (el.classList.contains('nip-input')) {
    const warning = el.nextElementSibling;

    if (!warning) return;

    if (val.length === 0) {
      warning.style.display = 'none';
      el.classList.remove('is-invalid');
      return;
    }

    if (val.length !== 14 && val.length !== 18) {
      warning.style.display = 'block';
      el.classList.add('is-invalid');
    } else {
      warning.style.display = 'none';
      el.classList.remove('is-invalid');
    }
  }

  // ================= NIDN =================
  if (el.classList.contains('nidn-input')) {
    const warning = el.nextElementSibling;

    if (!warning) return;

    if (val.length === 0) {
      warning.style.display = 'none';
      el.classList.remove('is-invalid');
      return;
    }

    if (val.length !== 8) {
      warning.style.display = 'block';
      el.classList.add('is-invalid');
    } else {
      warning.style.display = 'none';
      el.classList.remove('is-invalid');
    }
  }

   // ================= NIDN/NIP =================
  if (el.classList.contains('nidn-nip-input')) {
    const warning = el.nextElementSibling;

    if (!warning) return;

    if (val.length === 0) {
      warning.style.display = 'none';
      el.classList.remove('is-invalid');
      return;
    }

    if (val.length !== 8 && val.length !== 18) {
      warning.style.display = 'block';
      el.classList.add('is-invalid');
    } else {
      warning.style.display = 'none';
      el.classList.remove('is-invalid');
    }
  }
});
