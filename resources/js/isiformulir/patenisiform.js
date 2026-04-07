document.addEventListener('DOMContentLoaded', () => {

  /* =====================================================
   * 1) KONSULTAN PATEN
   * ===================================================== */
  const konsultanSelect = document.getElementById('konsultanpaten');
  const konsultanFollow = document.getElementById('konsultan-followup');
  const konsultanFields = [
    'nama_badan_hukum', 'alamat_badan_hukum', 'nama_konsultan_paten',
    'alamat_konsultan_paten', 'nomor_konsultan_paten', 'telepon_fax',
  ];

  function toggleKonsultan(isInit = false) {
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
        if (!isInit) el.value = ''; 
      }
    });
  }

  konsultanSelect?.addEventListener('change', () => toggleKonsultan(false));
  toggleKonsultan(true); 

  /* =====================================================
   * 2) PCT & PECAHAN & HAK PRIORITAS
   * ===================================================== */
  const setupToggle = (selectId, boxId, inputName, fields = [], isYaValue = 'Ya') => {
    const select = document.getElementById(selectId);
    const box = document.getElementById(boxId);
    
    const action = (isInit = false) => {
      if (!select || !box) return;
      const isYa = select.value === isYaValue;
      box.style.display = isYa ? 'block' : 'none';

      // Untuk single input (PCT/Pecahan)
      const singleInput = box.querySelector(`input[name="${inputName}"]`);
      if (singleInput) {
        if (isYa) singleInput.setAttribute('required', 'required');
        else {
          singleInput.removeAttribute('required');
          if (!isInit) singleInput.value = '';
        }
      }

      // Untuk banyak fields (Hak Prioritas)
      fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (isYa) el.setAttribute('required', 'required');
        else {
          el.removeAttribute('required');
          if (!isInit) el.value = '';
        }
      });
    };

    select?.addEventListener('change', () => action(false));
    action(true);
  };

  setupToggle('is_pct', 'pct-followup', 'nomor_permohonan');
  setupToggle('is_pecahan', 'pecahan-followup', 'pecahan_paten');
  setupToggle('hak_prioritas', 'hak-prioritas-followup', '', ['negara', 'nomor_prioritas', 'tgl_penerimaan']);

  /* =====================================================
   * 3) JUMLAH INVENTOR + RENDER LENGKAP
   * ===================================================== */
  const jumlahEl = document.getElementById('jumlah_inventor_verif') || document.getElementById('jumlah_inventor');
  const container = document.getElementById('inventor-container-verif') || document.getElementById('inventor-container');
  const tplFirst = document.getElementById('inventor-template-first-verif') || document.getElementById('inventor-template');
  const tplOther = document.getElementById('inventor-template-verif') || document.getElementById('inventor-template');

  // Ambil data prefill dari session yang ditaruh di JSON script
  let oldInventors = {};
  const oldJsonEl = document.getElementById('old-inventor-data');
  if (oldJsonEl) {
    try {
      oldInventors = JSON.parse(oldJsonEl.textContent || '{}');
    } catch (e) { oldInventors = {}; }
  }

  function renderInventors() {
    if (!jumlahEl || !container) return;
    const raw = (jumlahEl.value || '').trim();
    if (raw === '') return;

    let jumlah = parseInt(raw, 10);
    if (isNaN(jumlah)) return;
    jumlah = Math.max(1, Math.min(20, jumlah));

    container.innerHTML = '';

    for (let i = 0; i < jumlah; i++) {
      const isFirst = (i === 0);
      const targetTpl = (isFirst && tplFirst) ? tplFirst : tplOther;
      const node = targetTpl.content.cloneNode(true);
      
      const no = node.querySelector('.inv-no');
      if (no) no.textContent = i + 1;

      // FILL SEMUA FIELD (Nama, NIP, Alamat, Kode Pos, Pekerjaan, dll)
      const fields = [
        "nama", "kewarganegaraan", "nip_nim", "alamat", "fakultas",
        "no_hp", "email", "nidn", "status", "kode_pos", "pekerjaan"
      ];

      fields.forEach(key => {
        const input = node.querySelector(`[name="inventor[${key}][]"]`);
        if (input && oldInventors[key] && oldInventors[key][i]) {
          input.value = oldInventors[key][i];
        }
      });

      // Status Dosen/Mahasiswa Logic
      const statusSelect = node.querySelector('.status-select');
      const nidnField = node.querySelector('.nidn-field');
      if (statusSelect && nidnField) {
        const updateNidn = () => {
          nidnField.style.display = statusSelect.value === 'Dosen' ? 'block' : 'none';
        };
        statusSelect.addEventListener('change', updateNidn);
        updateNidn();
      }

      container.appendChild(node);
    }
  }

  // Event Listeners untuk Jumlah Inventor
  if (jumlahEl) {
    jumlahEl.addEventListener('input', renderInventors);
    
    // Support Tombol Plus Minus (jika ada)
    document.getElementById('invPlus')?.addEventListener('click', () => {
      jumlahEl.value = Math.min(20, (parseInt(jumlahEl.value) || 0) + 1);
      renderInventors();
    });
    document.getElementById('invMinus')?.addEventListener('click', () => {
      jumlahEl.value = Math.max(1, (parseInt(jumlahEl.value) || 2) - 1);
      renderInventors();
    });

    renderInventors(); 
  }

  /* =====================================================
   * 4) DOWNLOAD ACTION SWITCHER
   * ===================================================== */
  const form = document.querySelector("form");
  const docType = document.getElementById("doc_type");
  const btnDownload = document.getElementById("btnDownload");

  if (docType && btnDownload && form) {
    const originalAction = form.action;

    btnDownload.addEventListener('click', (e) => {
      if (!docType.value) {
        e.preventDefault();
        alert("Pilih jenis dokumen terlebih dahulu!");
        return;
      }
      form.action = docType.value;
      // Kembalikan ke original action setelah download dipicu
      setTimeout(() => { form.action = originalAction; }, 1000);
    });
  }
});