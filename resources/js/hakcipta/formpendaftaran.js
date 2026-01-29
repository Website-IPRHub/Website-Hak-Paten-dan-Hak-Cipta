
document.addEventListener('DOMContentLoaded', () => {

  /**
   * =========================
   * 1) Toggle Kuasa Followup
   * =========================
   */
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

  const kuasaSelect = document.getElementById('kuasa_cipta');
  if (kuasaSelect) {
    kuasaSelect.addEventListener('change', toggleKuasaFollowup);
    toggleKuasaFollowup(); // init
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
    // auto select
    const selectAll = () => setTimeout(() => jumlahEl.select(), 0);
    jumlahEl.addEventListener('focus', selectAll);
    jumlahEl.addEventListener('click', selectAll);

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

    // init
    renderInventors();

    // realtime
    let t;
    jumlahEl.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(() => {
        oldInventors = getCurrentInventorValues();
        renderInventors();
      }, 150);
    });

    // blur fix
    jumlahEl.addEventListener('blur', () => {
      if (((jumlahEl.value || '').trim()) === '') {
        jumlahEl.value = 1;
      }
      oldInventors = getCurrentInventorValues();
      renderInventors();
    });
  }


  /**
   * =========================
   * 3) Toggle Jenis Cipta "Lainnya"
   * =========================
   */
  /**
 * =========================
 * 3) Toggle Jenis Cipta "Lainnya" (RADIO)
 * =========================
 */
const lainnya = document.getElementById('jenis-lainnya-wrap');
const radios = document.querySelectorAll('input[name="jenis_cipta"]');

function toggleLainnyaRadio() {
  if (!lainnya) return;
  const checked = document.querySelector('input[name="jenis_cipta"]:checked');
  const isLainnya = checked && checked.value === 'Lainnya';
  lainnya.style.display = isLainnya ? 'block' : 'none';
}

if (radios.length) {
  radios.forEach(r => r.addEventListener('change', toggleLainnyaRadio));
  toggleLainnyaRadio(); // init (buat old() pas reload)
}


});

