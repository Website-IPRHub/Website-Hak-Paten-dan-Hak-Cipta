document.addEventListener('DOMContentLoaded', () => {
  function toggleKuasaFollowup() {
    const select = document.getElementById('kuasa_cipta');
    const follow = document.getElementById('kuasa-followup');
    if (!select || !follow) return;

    const isMelalui = select.value === 'Melalui';
    follow.style.display = isMelalui ? 'block' : 'none';

    const ids = [
      'nama_kuasa',
      'kewarganegaraan_kuasa',
      'alamat_kuasa',
      'telp_rumah_kuasa',
      'no_hp_kuasa',
      'email_kuasa',
    ];

    ids.forEach((id) => {
      const el = document.getElementById(id);
      if (!el) return;

      if (isMelalui) {
        el.setAttribute('required', 'required');
      } else {
        el.removeAttribute('required');
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        else el.value = '';
      }
    });
  }

  const select = document.getElementById('kuasa_cipta');
  if (select) {
    select.addEventListener('change', toggleKuasaFollowup);
    toggleKuasaFollowup(); // init
  }
});

  const jumlahEl = document.getElementById('jumlah_inventor');
  const container = document.getElementById('inventor-container');
  const tpl = document.getElementById('inventor-template');

  if (jumlahEl && container && tpl) {
    // ambil old inventor (kalau ada)
    let oldInventors = [];
    const oldJsonEl = document.getElementById('old-inventor-data');
    if (oldJsonEl) {
      try {
        oldInventors = JSON.parse(oldJsonEl.textContent || '[]');
        if (!Array.isArray(oldInventors)) oldInventors = [];
      } catch (e) {
        oldInventors = [];
      }
    }

    function clampJumlah(n) {
      const min = 1, max = 20;
      if (Number.isNaN(n)) return min;
      return Math.max(min, Math.min(max, n));
    }

    function getCurrentInventorValues() {
      return Array.from(container.querySelectorAll('input[name="inventor[]"]'))
        .map((i) => i.value);
    }

    function renderInventors() {
        const raw = (jumlahEl.value || '').trim();

        // ✅ kalau kosong, biarin kosong & kosongin field inventor
        if (raw === '') {
            container.innerHTML = '';
            return;
        }

        let jumlah = parseInt(raw, 10);
        if (Number.isNaN(jumlah)) {
            container.innerHTML = '';
            return;
        }

        // clamp 1..20
        jumlah = Math.max(1, Math.min(20, jumlah));
        jumlahEl.value = jumlah;

        container.innerHTML = '';

        for (let i = 0; i < jumlah; i++) {
            const node = tpl.content.cloneNode(true);
            const label = node.querySelector('.inventor-label');
            const input = node.querySelector('.inventor-input');

            if (label) label.textContent = `Inventor ${i + 1}`;
            if (input) input.value = oldInventors[i] ?? '';

            container.appendChild(node);
        }
     }

     // first render
/**
 * =========================
 * 2) Generate Inventor Fields
 * =========================
 */
const jumlahEl = document.getElementById('jumlah_inventor');
const container = document.getElementById('inventor-container');
const tpl = document.getElementById('inventor-template');

if (jumlahEl && container && tpl) {
  // ✅ auto-select biar dari "1" langsung ketimpa kalau ngetik "2"
  const selectAll = () => {
    // delay dikit biar selection kebaca di semua browser
    setTimeout(() => jumlahEl.select(), 0);
  };
  jumlahEl.addEventListener('focus', selectAll);
  jumlahEl.addEventListener('click', selectAll);

  // ambil old inventor (kalau ada)
  let oldInventors = [];
  const oldJsonEl = document.getElementById('old-inventor-data');
  if (oldJsonEl) {
    try {
      oldInventors = JSON.parse(oldJsonEl.textContent || '[]');
      if (!Array.isArray(oldInventors)) oldInventors = [];
    } catch (e) {
      oldInventors = [];
    }
  }

  function clampJumlah(n) {
    const min = 1, max = 20;
    if (Number.isNaN(n)) return min;
    return Math.max(min, Math.min(max, n));
  }

  function getCurrentInventorValues() {
    return Array.from(container.querySelectorAll('input[name="inventor[]"]'))
      .map((i) => i.value);
  }

  function renderInventorsFrom(jumlah) {
    container.innerHTML = '';
    for (let i = 0; i < jumlah; i++) {
      const node = tpl.content.cloneNode(true);
      const label = node.querySelector('.inventor-label');
      const input = node.querySelector('.inventor-input');

      if (label) label.textContent = `Inventor ${i + 1}`;
      if (input) input.value = oldInventors[i] ?? '';

      container.appendChild(node);
    }
  }

  function renderInventors() {
    const raw = (jumlahEl.value || '').trim();

    // user lagi edit (kosong) → jangan reset, tapi juga jangan render aneh
    if (raw === '') {
      container.innerHTML = '';
      return;
    }

    const parsed = parseInt(raw, 10);
    if (Number.isNaN(parsed)) {
      container.innerHTML = '';
      return;
    }

    const jumlah = clampJumlah(parsed);
    jumlahEl.value = jumlah;
    renderInventorsFrom(jumlah);
  }

  // first render
  renderInventors();

  // realtime pas ngetik (preserve value yang sudah diisi)
  let t;
  jumlahEl.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(() => {
      oldInventors = getCurrentInventorValues();
      renderInventors();
    }, 150);
  });

  // pas blur, kalau kosong → balikin ke 1
  jumlahEl.addEventListener('blur', () => {
    if (((jumlahEl.value || '').trim()) === '') {
      jumlahEl.value = 1;
    }
    oldInventors = getCurrentInventorValues();
    renderInventors();
  });
}

  }

