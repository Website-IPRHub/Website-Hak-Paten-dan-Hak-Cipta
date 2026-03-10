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
  if (!raw) return {};

  if (Array.isArray(raw)) {
    const out = {};
    raw.forEach((inv, idx) => {
      if (!inv || typeof inv !== "object") return;
      Object.keys(inv).forEach((k) => {
        if (!out[k]) out[k] = [];
        out[k][idx] = inv[k];
      });
    });
    return out;
  }

  if (raw.nama && Array.isArray(raw.nama)) return raw;

  const out = {};
  Object.values(raw).forEach((inv, idx) => {
    if (!inv || typeof inv !== "object") return;
    Object.keys(inv).forEach((k) => {
      if (!out[k]) out[k] = [];
      out[k][idx] = inv[k];
    });
  });
  return out;
};

oldInventor = normalizeOldInventor(oldInventor);
console.log("OLD INVENTOR FINAL:", oldInventor);

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

const getOldVal = (key, idx) => {
  const field = oldInventor?.[key];
  if (Array.isArray(field)) return field[idx] ?? "";
  if (field && typeof field === "object") return field[idx] ?? field[String(idx)] ?? "";
  return "";
};

const setFieldValue = (root, selector, value) => {
  const el = root.querySelector(selector);
  if (!el) {
    console.log("FIELD NOT FOUND:", selector);
    return;
  }

  if (el.tagName === "SELECT") {
    el.value = value ?? "";
  } else if (el.tagName === "TEXTAREA") {
    el.value = value ?? "";
    el.textContent = value ?? "";
  } else {
    el.value = value ?? "";
  }
};

const fillInventorCard = (card, idx) => {
  setFieldValue(card, 'input[name="inventor[nama][]"]', getOldVal("nama", idx));
  setFieldValue(card, 'input[name="inventor[nik][]"]', getOldVal("nik", idx));
  setFieldValue(card, 'input[name="inventor[nip_nim][]"]', getOldVal("nip_nim", idx));
  setFieldValue(card, 'select[name="inventor[fakultas][]"]', getOldVal("fakultas", idx));
  setFieldValue(card, 'input[name="inventor[nidn][]"]', getOldVal("nidn", idx));
  setFieldValue(card, 'select[name="inventor[status][]"]', getOldVal("status", idx));
  setFieldValue(card, 'input[name="inventor[no_hp][]"]', getOldVal("no_hp", idx));
  setFieldValue(card, 'input[name="inventor[tlp_rumah][]"]', getOldVal("tlp_rumah", idx));
  setFieldValue(card, 'input[name="inventor[email][]"]', getOldVal("email", idx));
  setFieldValue(card, 'textarea[name="inventor[alamat][]"]', getOldVal("alamat", idx));
  setFieldValue(card, 'input[name="inventor[kode_pos][]"]', getOldVal("kode_pos", idx));

  console.log("POST FILL CHECK", idx, {
    nik: card.querySelector('input[name="inventor[nik][]"]')?.value,
    alamat: card.querySelector('textarea[name="inventor[alamat][]"]')?.value,
    tlp_rumah: card.querySelector('input[name="inventor[tlp_rumah][]"]')?.value,
    kode_pos: card.querySelector('input[name="inventor[kode_pos][]"]')?.value,
  });
};

const applyStatusLogic = (card) => {
  const statusSelect = card.querySelector('[name="inventor[status][]"]');
  const nidnField = card.querySelector(".nidn-wrap");
  const nidnInput = card.querySelector('[name="inventor[nidn][]"]');

  const update = (clearWhenHidden = false) => {
    const isDosen = statusSelect?.value === "Dosen";
    if (nidnField) nidnField.style.display = isDosen ? "" : "none";

    if (nidnInput) {
      if (isDosen) {
        nidnInput.setAttribute("required", "required");
      } else {
        nidnInput.removeAttribute("required");
        if (clearWhenHidden) {
          nidnInput.value = "";
        }
      }
    }
  };

  if (statusSelect) {
    statusSelect.addEventListener("change", () => update(true));
    update(false);
  }
};

function renderInventors(count, useSnapshot = true) {
  if (!container || !tpl) return;

  const snap = useSnapshot ? snapshotCurrent() : {};
  container.innerHTML = "";

  for (let i = 0; i < count; i++) {
    const node = tpl.content.cloneNode(true);
    const no = node.querySelector(".inv-no");
    if (no) no.textContent = i + 1;

    node.querySelectorAll("[name^='inventor[']").forEach(el => {
      const name = el.getAttribute("name");
      const arr = snap[name] || [];
      if (arr[i] !== undefined) {
        el.value = arr[i];
      }
    });

    container.appendChild(node);

    const insertedCard = container.querySelectorAll(".inventor-card")[i];
    applyStatusLogic(insertedCard);
    fillInventorCard(insertedCard, i);
  }
}

const setCount = (n, useSnapshot = true) => {
  const v = clamp(n, 1, 20);
  if (jumlahInput) jumlahInput.value = v;
  renderInventors(v, useSnapshot);
};

const countEl = document.getElementById("prefill-count");
let prefillCount = 1;
try {
  prefillCount = parseInt(JSON.parse(countEl?.textContent || "1"), 10) || 1;
} catch (e) {}
setCount(prefillCount, false);

window.addEventListener("pageshow", (event) => {
  setTimeout(() => {
    setCount(prefillCount, false);
  }, event.persisted ? 50 : 0);
});

if (btnMinus) btnMinus.addEventListener("click", () => setCount(getCount() - 1));
if (btnPlus)  btnPlus.addEventListener("click", () => setCount(getCount() + 1));

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