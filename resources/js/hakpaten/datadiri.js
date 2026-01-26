document.addEventListener('DOMContentLoaded', () => {
  const jumlahInput = document.getElementById('jumlah_inventor');
  const container = document.getElementById('inventor-container');
  const template = document.getElementById('inventor-template');

  if (!jumlahInput || !container || !template) {
    console.error('Inventor elements not found:', { jumlahInput, container, template });
    return;
  }

  const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

  // simpan value yang sudah diisi biar gak hilang saat rerender
  function snapshotValues() {
    const getVals = (sel) => Array.from(document.querySelectorAll(sel)).map(el => el.value);
    return {
      nama: getVals('input[name="inventor[nama][]"]'),
      nip: getVals('input[name="inventor[nip_nim][]"]'),
      fakultas: getVals('select[name="inventor[fakultas][]"]'),
      hp: getVals('input[name="inventor[no_hp][]"]'),
      email: getVals('input[name="inventor[email][]"]'),
      status: getVals('select[name="inventor[status][]"]'),
    };
  }

  function renderInventors() {
    const jumlah = clamp(parseInt(jumlahInput.value || '1', 10) || 1, 1, 20);
    jumlahInput.value = jumlah;

    const old = snapshotValues();

    container.innerHTML = '';

    for (let i = 0; i < jumlah; i++) {
      const frag = template.content.cloneNode(true);

      const noEl = frag.querySelector('.inv-no');
      if (noEl) noEl.textContent = (i + 1);

      // restore values kalau ada
      const nama = frag.querySelector('input[name="inventor[nama][]"]');
      const nip  = frag.querySelector('input[name="inventor[nip_nim][]"]');
      const fak  = frag.querySelector('select[name="inventor[fakultas][]"]');
      const hp   = frag.querySelector('input[name="inventor[no_hp][]"]');
      const em   = frag.querySelector('input[name="inventor[email][]"]');
      const st   = frag.querySelector('select[name="inventor[status][]"]');

      if (nama) nama.value = old.nama[i] ?? '';
      if (nip)  nip.value  = old.nip[i] ?? '';
      if (fak)  fak.value  = old.fakultas[i] ?? '';
      if (hp)   hp.value   = old.hp[i] ?? '';
      if (em)   em.value   = old.email[i] ?? '';
      if (st)   st.value   = old.status[i] ?? '';

      container.appendChild(frag);
    }
  }

  // Trigger tiap kali jumlah berubah
  jumlahInput.addEventListener('input', renderInventors);
  jumlahInput.addEventListener('change', renderInventors);

  // Initial render
  renderInventors();
});
