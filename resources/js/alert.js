document.addEventListener('DOMContentLoaded', () => {
  // ambil SEMUA input file yang punya data-allowed
  const inputs = document.querySelectorAll('input[type="file"][data-allowed]');

  inputs.forEach((input) => {
    if (input.dataset.alertBound === '1') return;
    input.dataset.alertBound = '1';

    input.addEventListener('change', () => {
      const file = input.files?.[0];
      if (!file) return;

      const allowed = (input.dataset.allowed || '')
        .split(',')
        .map(s => s.trim().toLowerCase())
        .filter(Boolean);

      const maxMB = Number(input.dataset.maxMb || 10);

      const ext = file.name.split('.').pop().toLowerCase();

      if (allowed.length && !allowed.includes(ext)) {
        alert(`Tipe file salah. Harus: ${allowed.join(', ').toUpperCase()}`);
        input.value = '';
        return;
      }

      if (file.size > maxMB * 1024 * 1024) {
        alert(`Ukuran file maksimal ${maxMB}MB.`);
        input.value = '';
        return;
      }
    });
  });
});
