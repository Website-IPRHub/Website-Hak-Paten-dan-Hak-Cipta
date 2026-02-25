document.addEventListener('DOMContentLoaded', () => {

   if (!document.body.classList.contains('paten-page')) return;
  if (!document.getElementById('draftForm')) return;
  if (!document.getElementById('inventor-template-first-verif')) return;

  const jumlahInput = document.getElementById('jumlah_inventor_verif');
  const container = document.getElementById('inventor-container-verif');

  const tplFirst = document.getElementById('inventor-template-first-verif');
  const tplNormal = document.getElementById('inventor-template-verif');

  const prefillEl = document.getElementById('prefill-inventor-data');
  const prefillCountEl = document.getElementById('prefill-count');

  let prefillInventor = {};
  let prefillCount = 1;

  try { prefillInventor = JSON.parse(prefillEl?.textContent || "{}"); } catch(e) { prefillInventor = {}; }
  try { prefillCount = parseInt(JSON.parse(prefillCountEl?.textContent || "1"), 10) || 1; } catch(e) { prefillCount = 1; }


  if (!jumlahInput || !container || !tplFirst || !tplNormal) {
    console.error('Inventor elements not found:', { jumlahInput, container, tplFirst, tplNormal });
    return;
  }

  const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

  function snapshotValues() {
    const getVals = (sel) => Array.from(document.querySelectorAll(sel)).map(el => el.value);

    const statusEls = Array.from(document.querySelectorAll('[name="inventor[status][]"]'));

    return {
      nama: getVals('[name="inventor[nama][]"]'),
      nip_nim: getVals('[name="inventor[nip_nim][]"]'),
      nidn: getVals('[name="inventor[nidn][]"]'),
      fakultas: getVals('[name="inventor[fakultas][]"]'),
      no_hp: getVals('[name="inventor[no_hp][]"]'),
      email: getVals('[name="inventor[email][]"]'),
      status: statusEls.map(el => el.value),
    };
  }


  function toggleNidn(card, show) {
    const field = card.querySelector('.nidn-field');
    const input = card.querySelector('input[name="inventor[nidn][]"]');
    if (!field || !input) return;

    if (show) {
      field.style.display = 'block';
      input.required = true;
    } else {
      field.style.display = 'none';
      input.required = false;
      input.value = '';
    }
  }

    function getSourceData() {
    const snap = snapshotValues();

    // kalau snap kosong (baru pertama kali), pakai prefill session
    const hasAny =
      (snap.nama?.some(v => v && v.trim() !== "")) ||
      (snap.nip_nim?.some(v => v && v.trim() !== "")) ||
      (snap.email?.some(v => v && v.trim() !== ""));

    if (hasAny) return snap;
    return {
      nama: prefillInventor.nama || [],
      nip_nim: prefillInventor.nip_nim || [],
      nidn: prefillInventor.nidn || [],
      fakultas: prefillInventor.fakultas || [],
      no_hp: prefillInventor.no_hp || [],
      email: prefillInventor.email || [],
      status: prefillInventor.status || [],
    };

  }

  if (prefillCount && prefillCount > 0) {
    jumlahInput.value = clamp(prefillCount, 1, 20);
  }


  function renderInventors() {
    const jumlah = clamp(parseInt(jumlahInput.value || '1', 10) || 1, 1, 20);
    jumlahInput.value = jumlah;

    const old = getSourceData();
    container.innerHTML = '';

    for (let i = 0; i < jumlah; i++) {
      const frag = (i === 0 ? tplFirst : tplNormal).content.cloneNode(true);

      const card = frag.querySelector('.inventor-card');
      const noEl = frag.querySelector('.inv-no');
      if (noEl) noEl.textContent = (i + 1);

      // restore common fields
      const nama = frag.querySelector('input[name="inventor[nama][]"]');
      const nip  = frag.querySelector('input[name="inventor[nip_nim][]"]');
      const nidn = frag.querySelector('input[name="inventor[nidn][]"]');
      const fak  = frag.querySelector('select[name="inventor[fakultas][]"]');
      const hp   = frag.querySelector('input[name="inventor[no_hp][]"]');
      const em   = frag.querySelector('input[name="inventor[email][]"]');

      if (nama) nama.value = old.nama[i] ?? '';
      if (nip)  nip.value  = old.nip_nim[i] ?? '';
      if (nidn) nidn.value = old.nidn[i] ?? '';
      if (fak)  fak.value  = old.fakultas[i] ?? '';
      if (hp)   hp.value   = old.no_hp[i] ?? '';
      if (em)   em.value   = old.email[i] ?? '';


      // template normal: status + toggle NIDN
      if (i > 0 && card) {
        const st = frag.querySelector('select[name="inventor[status][]"]');
        if (st) st.value = old.status[i] ?? '';

        toggleNidn(card, st && st.value === 'Dosen');

        if (st) {
          st.addEventListener('change', function () {
            toggleNidn(card, this.value === 'Dosen');
          });
        }
      }

      container.appendChild(frag);
    }
  }

  jumlahInput.addEventListener('input', renderInventors);
  jumlahInput.addEventListener('change', renderInventors);

  renderInventors();
});
