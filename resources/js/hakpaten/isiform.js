document.addEventListener('DOMContentLoaded', () => {
  /**
   * =========================
   * 1) Toggle Konsultan Paten
   * =========================
   */
  function toggleKonsultanFollowup() {
    const select = document.getElementById('konsultanpaten');
    const followup = document.getElementById('konsultan-followup');
    if (!select || !followup) return;

    const isYa = select.value === 'Melalui';
    followup.style.display = isYa ? 'block' : 'none';

    // Field-field yang ikut aturan Ya/Tidak
    const ids = [
      'nama_badan_hukum',
      'alamat_badan_hukum',
      'nama_konsultan_paten',
      'alamat_konsultan_paten',
      'nomor_konsultan_paten',
      'telepon/fax', // ganti id html dari telepon/fax -> telepon_fax
    ];

    ids.forEach((id) => {
      const el = document.getElementById(id);
      if (!el) return;

      if (isYa) {
        el.setAttribute('required', 'required');
      } else {
        el.removeAttribute('required');
        // bersihin isi biar gak ke-submit
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        else el.value = '';
      }
    });
  }

  const konsultanSelect = document.getElementById('konsultanpaten');
  if (konsultanSelect) {
    konsultanSelect.addEventListener('change', toggleKonsultanFollowup);
    toggleKonsultanFollowup(); // biar kondisi awal/old() sinkron
  }

  /**
   * =========================
   * 2) Generate Inventor Fields
   * =========================
   * Butuh elemen:
   * - input#jumlah_inventor
   * - div#inventor-container
   * - template#inventor-template
   * - script#old-inventor-data (JSON array) (opsional)
   */
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

  /**
   * =========================
   * 3) Toggle hak prioritas
   * =========================
   */
  function toggleHakPrioritas() {
    const select = document.getElementById('hak_prioritas');
    const followup = document.getElementById('hak-prioritas-followup');
    if (!select || !followup) return;

    const isYa = select.value === 'Ya';
    followup.style.display = isYa ? 'block' : 'none';

    // Field-field yang ikut aturan Ya/Tidak
    const ids = [
      'negara',
      'tgl_penerimaan',
      'nomor_prioritas',
    ];

    ids.forEach((id) => {
      const el = document.getElementById(id);
      if (!el) return;

      if (isYa) {
        el.setAttribute('required', 'required');
      } else {
        el.removeAttribute('required');
        // bersihin isi biar gak ke-submit
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        else el.value = '';
      }
    });
  }

  const hakPrioritasSelect = document.getElementById('hak_prioritas');
  if (hakPrioritasSelect) {
    hakPrioritasSelect.addEventListener('change', toggleHakPrioritas);
    toggleHakPrioritas(); // biar kondisi awal/old() sinkron
  }

});
