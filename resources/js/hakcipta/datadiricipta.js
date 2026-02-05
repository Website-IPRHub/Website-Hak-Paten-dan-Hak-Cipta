document.addEventListener('DOMContentLoaded', () => {

  const jumlahInput = document.getElementById('jumlah_inventor');
  const container   = document.getElementById('inventor-container');
  const template    = document.getElementById('inventor-template');

  if (!jumlahInput || !container || !template) return;

  const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

  function renderInventors() {
    const jumlah = clamp(parseInt(jumlahInput.value || 1, 10), 1, 20);
    jumlahInput.value = jumlah;

    container.innerHTML = '';

    for (let i = 0; i < jumlah; i++) {
      const frag = template.content.cloneNode(true);

      const noEl = frag.querySelector('.inv-no');
      if (noEl) noEl.textContent = i + 1;

      // DEFAULT: NIDN MATI
      const nidnWrap  = frag.querySelector('.nidn-wrap');
      const nidnInput = frag.querySelector('.nidn-input');
      if (nidnWrap && nidnInput) {
        nidnWrap.style.display = 'none';
        nidnInput.required = false;
        nidnInput.disabled = true;
      }

      container.appendChild(frag);
    }
  }

  // 🔥 SATU LISTENER UNTUK SEMUA INVENTOR
  document.addEventListener('change', (e) => {
    if (!e.target.matches('select[name="inventor[status][]"]')) return;

    const card = e.target.closest('.inventor-card');
    if (!card) return;

    const nidnWrap  = card.querySelector('.nidn-wrap');
    const nidnInput = card.querySelector('.nidn-input');
    if (!nidnWrap || !nidnInput) return;

    if (e.target.value === 'Dosen') {
      nidnWrap.style.display = 'block';
      nidnInput.required = true;
      nidnInput.disabled = false;
    } else {
      nidnWrap.style.display = 'none';
      nidnInput.required = false;
      nidnInput.disabled = true;
      nidnInput.value = '';
    }
  });

  jumlahInput.addEventListener('input', renderInventors);
  jumlahInput.addEventListener('change', renderInventors);

  renderInventors();
});
