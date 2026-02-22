document.addEventListener("DOMContentLoaded", () => {
 console.log("prefillEl:", document.getElementById("old-inventor-data"));
console.log("container:", document.getElementById("inventor-container"));
console.log("template:", document.getElementById("inventor-template"));
console.log("jumlah:", document.getElementById("jumlah_inventor"));
const btnMinus = document.getElementById("invMinus");
const btnPlus  = document.getElementById("invPlus");
  /* =====================================================
     1️⃣ TOGGLE KUASA
  ===================================================== */

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

    ids.forEach(id => {
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
    toggleKuasaFollowup();
  }


  /* =====================================================
     2️⃣ TOGGLE JENIS CIPTA LAINNYA
  ===================================================== */

  const lainnyaWrap = document.getElementById('jenis-lainnya-wrap');
  const jenisRadios = document.querySelectorAll('input[name="jenis_cipta"]');

  function toggleJenisLainnya() {
    if (!lainnyaWrap) return;
    const checked = document.querySelector('input[name="jenis_cipta"]:checked');
    lainnyaWrap.style.display =
      (checked && checked.value === 'Lainnya') ? 'block' : 'none';
  }

  if (jenisRadios.length) {
    jenisRadios.forEach(r => r.addEventListener('change', toggleJenisLainnya));
    toggleJenisLainnya();
  }


  /* =====================================================
   3️⃣ DYNAMIC INVENTOR (FINAL CLEAN)
===================================================== */
  const jumlahInput = document.getElementById("jumlah_inventor");
  const container   = document.getElementById("inventor-container");
  const tpl         = document.getElementById("inventor-template");

  const prefillEl = document.getElementById("old-inventor-data");
let oldInventor = {};

try {
  oldInventor = JSON.parse(prefillEl?.textContent || "{}");
} catch (e) {
  oldInventor = {};
}

const normalizeOldInventor = (raw) => {
  if (!raw || Array.isArray(raw)) return {};

  // sudah format BENAR
  if (raw.nama && Array.isArray(raw.nama)) return raw;

  // format SALAH → ubah ke format benar
  const out = {};
  Object.values(raw).forEach((inv, idx) => {
    Object.keys(inv).forEach(k => {
      if (!out[k]) out[k] = [];
      out[k][idx] = inv[k];
    });
  });
  return out;
};

oldInventor = normalizeOldInventor(oldInventor);

console.log("OLD INVENTOR:", oldInventor);
  /* =====================================================
     HELPERS
  ===================================================== */
  const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

  const getCount = () => {
    const n = parseInt(jumlahInput?.value || "1", 10);
    return clamp(isNaN(n) ? 1 : n, 1, 20);
  };

  const snapshotCurrent = () => {
    const snap = {};
    if (!container) return snap;

    container.querySelectorAll("[name^='inventor[']").forEach(el => {
      const name = el.getAttribute("name");
      if (!snap[name]) snap[name] = [];
      snap[name].push(el.value);
    });

    return snap;
  };

  const fillFromOld = (root, idx) => {
  const keys = [
    "nama",
    "nip_nim",
    "fakultas",
    "status",
    "no_hp",
    "email",
    "nidn",
    "alamat",
    "kode_pos"
  ];

  keys.forEach(k => {
    const el = root.querySelector(`[name="inventor[${k}][]"]`);
    if (!el) return;

    const val = oldInventor?.[k]?.[idx];
    if (val !== undefined) {
      el.value = val;

      if (k === "status") {
        el.dispatchEvent(new Event("change", { bubbles: true }));
      }
    }
  });
};

console.log("OLD INVENTOR:", oldInventor);

  const applyStatusLogic = (card) => {
  const statusSelect = card.querySelector('[name="inventor[status][]"]');
  const nidnField = card.querySelector(".nidn-wrap");
  const nidnInput = card.querySelector('[name="inventor[nidn][]"]');

  const update = () => {
    const isDosen = statusSelect?.value === "Dosen";
    if (nidnField) nidnField.style.display = isDosen ? "" : "none";
    if (nidnInput) {
      if (isDosen) nidnInput.setAttribute("required", "required");
      else {
        nidnInput.removeAttribute("required");
        nidnInput.value = "";
      }
    }
  };

  if (statusSelect) {
    statusSelect.addEventListener("change", update);
    update(); 
  }
};

  /* =====================================================
     RENDER
  ===================================================== */
  function renderInventors(count) {
  if (!container || !tpl) return;

  const snap = snapshotCurrent();
  container.innerHTML = "";

  for (let i = 0; i < count; i++) {
    const node = tpl.content.cloneNode(true);
    const card = node.querySelector(".inventor-card");

    const no = node.querySelector(".inv-no");
    if (no) no.textContent = (i + 1);

    if (card) applyStatusLogic(card);

    node.querySelectorAll("[name^='inventor[']").forEach(el => {
      const name = el.getAttribute("name");
      const arr = snap[name] || [];
      if (arr[i] !== undefined) el.value = arr[i];
    });

    fillFromOld(node, i);

    container.appendChild(node);
  }
}

  const setCount = (n) => {
    const v = clamp(n, 1, 20);
    if (jumlahInput) jumlahInput.value = v;
    renderInventors(v);
  };

  

  /* =====================================================
     INIT (PREFILL)
  ===================================================== */
  const countEl = document.getElementById("prefill-count");
  let prefillCount = 1;
  try {
    prefillCount = parseInt(JSON.parse(countEl?.textContent || "1"), 10) || 1;
  } catch (e) {}
  setCount(prefillCount);

  
      if (btnMinus) btnMinus.addEventListener("click", () => setCount(getCount() - 1));
      if (btnPlus)  btnPlus.addEventListener("click", () => setCount(getCount() + 1));

  /* =====================================================
     EVENTS
  ===================================================== */
  if (jumlahInput) {
    jumlahInput.addEventListener("input", () => {
      setCount(getCount());
    });

    jumlahInput.addEventListener("blur", () => {
      if (!jumlahInput.value) setCount(1);
    });
  }

  /* =====================================================
     FIX SUBMIT (SYNC JUMLAH)
  ===================================================== */
  const form = document.querySelector("form.form");
  if (form) {
    form.addEventListener("submit", () => {
      const cards = container.querySelectorAll(".inventor-card");
      jumlahInput.value = cards.length || 1;
    });
  }

  


});