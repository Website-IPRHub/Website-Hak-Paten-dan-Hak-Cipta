document.addEventListener('DOMContentLoaded', () => {

  /* =====================================================
   * 1) KONSULTAN PATEN (Ya / Tidak)
   * ===================================================== */
  const konsultanSelect = document.getElementById('konsultanpaten');
  const konsultanFollow = document.getElementById('konsultan-followup');

  const konsultanFields = [
    'nama_badan_hukum',
    'alamat_badan_hukum',
    'nama_konsultan_paten',
    'alamat_konsultan_paten',
    'nomor_konsultan_paten',
    'telepon_fax', 
  ];

  function toggleKonsultan() {
    if (!konsultanSelect || !konsultanFollow) return;

    const isYa = konsultanSelect.value === 'Melalui';
    konsultanFollow.style.display = isYa ? 'block' : 'none';

    konsultanFields.forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;

      if (isYa) {
        el.setAttribute('required', 'required');
      } else {
        el.removeAttribute('required');
        el.value = '';
      }
    });
  }

  konsultanSelect?.addEventListener('change', toggleKonsultan);
  toggleKonsultan(); // init old()


  /* =====================================================
   * 2) PCT (Ya / Tidak)
   * ===================================================== */
  const pctSelect = document.getElementById('is_pct');
  const pctBox = document.getElementById('pct-followup');
  const pctInput = pctBox?.querySelector('input[name="nomor_permohonan"]');

  function togglePCT() {
    if (!pctSelect || !pctBox || !pctInput) return;

    const isYa = pctSelect.value === 'Ya';
    pctBox.style.display = isYa ? 'block' : 'none';

    if (isYa) pctInput.setAttribute('required', 'required');
    else {
      pctInput.removeAttribute('required');
      pctInput.value = '';
    }
  }

  pctSelect?.addEventListener('change', togglePCT);
  togglePCT();


  /* =====================================================
   * 3) PECAHAN PATEN (Ya / Tidak)
   * ===================================================== */
  const pecahanSelect = document.getElementById('is_pecahan');
  const pecahanBox = document.getElementById('pecahan-followup');
  const pecahanInput = pecahanBox?.querySelector('input[name="pecahan_paten"]');

  function togglePecahan() {
    if (!pecahanSelect || !pecahanBox || !pecahanInput) return;

    const isYa = pecahanSelect.value === 'Ya';
    pecahanBox.style.display = isYa ? 'block' : 'none';

    if (isYa) pecahanInput.setAttribute('required', 'required');
    else {
      pecahanInput.removeAttribute('required');
      pecahanInput.value = '';
    }
  }

  pecahanSelect?.addEventListener('change', togglePecahan);
  togglePecahan();


  /* =====================================================
   * 4) HAK PRIORITAS (Ya / Tidak)
   * ===================================================== */
  const hakSelect = document.getElementById('hak_prioritas');
  const hakBox = document.getElementById('hak-prioritas-followup');

  const hakFields = ['negara', 'nomor_prioritas', 'tgl_penerimaan'];

  function toggleHakPrioritas() {
    if (!hakSelect || !hakBox) return;

    const isYa = hakSelect.value === 'Ya';
    hakBox.style.display = isYa ? 'block' : 'none';

    hakFields.forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;

      if (isYa) el.setAttribute('required', 'required');
      else {
        el.removeAttribute('required');
        el.value = '';
      }
    });
  }

  hakSelect?.addEventListener('change', toggleHakPrioritas);
  toggleHakPrioritas();


  /* =====================================================
   * 5) JUMLAH INVENTOR + GENERATE FIELD
   * ===================================================== */
  
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

/**
 * =========================
 * 2) Generate Inventor Fields
 * =========================
 */
const jumlahEl = document.getElementById('jumlah_inventor');
const container = document.getElementById('inventor-container');
const tpl = document.getElementById('inventor-template');

if (jumlahEl && container && tpl) {
  const selectAll = () => {
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

  let t;
  jumlahEl.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(() => {
      oldInventors = getCurrentInventorValues();
      renderInventors();
    }, 150);
  });

  jumlahEl.addEventListener('blur', () => {
    if (((jumlahEl.value || '').trim()) === '') {
      jumlahEl.value = 1;
    }
    oldInventors = getCurrentInventorValues();
    renderInventors();
  });
}

  }

  const form = document.querySelector("form.form");
  const docType = document.getElementById("doc_type");
  const btnDownload = document.getElementById("btnDownload");

  const setFormAction = () => {
    const url = docType?.value || "";
    if (form && url) form.setAttribute("action", url);

    // disable tombol kalau belum pilih dokumen
    if (btnDownload) btnDownload.disabled = !url;
    if (btnDownload) btnDownload.style.opacity = url ? "1" : ".6";
    if (btnDownload) btnDownload.style.cursor = url ? "pointer" : "not-allowed";
  };

  if (docType) {
    docType.addEventListener("change", setFormAction);
    setFormAction(); // init
  }

});
