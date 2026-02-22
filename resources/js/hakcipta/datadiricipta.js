document.addEventListener('DOMContentLoaded', () => {
  const jumlahInput = document.getElementById('jumlah_inventor');
  const container = document.getElementById('inventor-container');
  const tpl = document.getElementById('inventor-template');

  const prefillEl = document.getElementById('prefill-inventor-data');
  const prefillCountEl = document.getElementById('prefill-count');

  let prefillInventor = {};
  let prefillCount = 1;

  try {
    prefillInventor = JSON.parse(prefillEl?.textContent || '{}');
  } catch {}

  try {
    prefillCount = parseInt(JSON.parse(prefillCountEl?.textContent || '1'), 10) || 1;
  } catch {}

  if (!jumlahInput || !container || !tpl) {
    console.error('Inventor elements not found', { jumlahInput, container, tpl });
    return;
  }

  const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

  function snapshotValues() {
    const get = sel => [...document.querySelectorAll(sel)].map(e => e.value);
    return {
      nama: get('[name="inventor[nama][]"]'),
      nip_nim: get('[name="inventor[nip_nim][]"]'),
      nidn: get('[name="inventor[nidn][]"]'),
      fakultas: get('[name="inventor[fakultas][]"]'),
      no_hp: get('[name="inventor[no_hp][]"]'),
      email: get('[name="inventor[email][]"]'),
      status: get('[name="inventor[status][]"]'),
    };
  }

  function getSourceData() {
  const snap = snapshotValues();
  const hasAny = snap.nama.some(v => v?.trim());

  if (hasAny) return snap;

  // convert array of object → object of array
  const result = {
    nama: [],
    nip_nim: [],
    nidn: [],
    fakultas: [],
    no_hp: [],
    email: [],
    status: [],
  };

  (prefillInventor || []).forEach((row, i) => {
    result.nama[i] = row.nama || '';
    result.nip_nim[i] = row.nip_nim || '';
    result.nidn[i] = row.nidn || '';
    result.fakultas[i] = row.fakultas || '';
    result.no_hp[i] = row.no_hp || '';
    result.email[i] = row.email || '';
    result.status[i] = row.status || '';
  });

  return result;
}

  function toggleNidn(card, show) {
    const wrap = card.querySelector('.nidn-wrap');
    const input = card.querySelector('[name="inventor[nidn][]"]');
    if (!wrap || !input) return;

    wrap.style.display = show ? 'block' : 'none';
    input.required = show;
    if (!show) input.value = '';
  }

  function renderInventors() {
    const jumlah = clamp(parseInt(jumlahInput.value || '1', 10), 1, 20);
    jumlahInput.value = jumlah;

    const old = getSourceData();
    container.innerHTML = '';

    for (let i = 0; i < jumlah; i++) {
      const frag = tpl.content.cloneNode(true);
      const card = frag.querySelector('.inventor-card');

      frag.querySelector('.inv-no').textContent = i + 1;

      const set = (name, val) => {
        const el = frag.querySelector(`[name="inventor[${name}][]"]`);
        if (el) el.value = val ?? '';
      };

      set('nama', old.nama[i]);
      set('nip_nim', old.nip_nim[i]);
      set('nidn', old.nidn[i]);
      set('fakultas', old.fakultas[i]);
      set('no_hp', old.no_hp[i]);
      set('email', old.email[i]);
      set('status', old.status[i]);

      const statusEl = frag.querySelector('[name="inventor[status][]"]');
      toggleNidn(card, statusEl?.value === 'Dosen');

      statusEl?.addEventListener('change', () =>
        toggleNidn(card, statusEl.value === 'Dosen')
      );

      container.appendChild(frag);
    }
  }

  jumlahInput.value = clamp(prefillCount, 1, 20);
  jumlahInput.addEventListener('input', renderInventors);
  renderInventors();
});