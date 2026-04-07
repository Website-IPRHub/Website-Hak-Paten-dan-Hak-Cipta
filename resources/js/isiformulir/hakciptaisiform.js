document.addEventListener("DOMContentLoaded", () => {
    const jumlahInput = document.getElementById("jumlah_inventor");
    const container = document.getElementById("inventor-container");
    const tpl = document.getElementById("inventor-template");
    const prefillEl = document.getElementById("old-inventor-data");
    const btnMinus = document.getElementById("invMinus");
    const btnPlus = document.getElementById("invPlus");

    let oldInventor = {};
    try {
        oldInventor = JSON.parse(prefillEl?.textContent || "{}");
    } catch (e) {
        oldInventor = {};
    }

   const normalizeOldInventor = (raw) => {
    if (!raw || Array.isArray(raw)) return {};
    if (raw.nama && Array.isArray(raw.nama)) {
        if (raw.nik && (!raw.NIK || raw.NIK.length === 0)) {
            raw.NIK = raw.nik;
        }
        return raw;
    }
    return raw;
};

   const fillFromOld = (root, idx, snap) => {
    const keys = ["nama", "nik", "nip_nim", "fakultas", "status", "no_hp", "email", "alamat", "kode_pos", "tlp_rumah"];

    keys.forEach((k) => {
        const el = root.querySelector(`[name="inventor[${k}][]"]`);
        if (!el) return;

        const snapVal = snap[`inventor[${k}][]`]?.[idx];
        const sessionVal = oldInventor?.[k]?.[idx];
        
        const val = snapVal || sessionVal || "";
        if (val) el.value = val;
    });
};

    const applyStatusLogic = (card) => {
        const statusSelect = card.querySelector('[name="inventor[status][]"]');
        const nidnField = card.querySelector(".nidn-wrap") || card.querySelector(".nidn-field");
        const nidnInput = card.querySelector('[name="inventor[nidn][]"]');

        const update = (isInit = false) => {
            const isDosen = statusSelect?.value === "Dosen";
            if (nidnField) nidnField.style.display = isDosen ? "block" : "none";
            if (nidnInput) {
                if (isDosen) nidnInput.setAttribute("required", "required");
                else {
                    nidnInput.removeAttribute("required");
                    if (!isInit) nidnInput.value = ""; 
                }
            }
        };

        if (statusSelect) {
            statusSelect.addEventListener("change", () => update(false));
            update(true);
        }
    };

    const snapshotCurrent = () => {
        const snap = {};
        if (!container) return snap;
        // Menangkap input, textarea (alamat), dan select (fakultas/status)
        container.querySelectorAll("input[name^='inventor['], textarea[name^='inventor['], select[name^='inventor[']").forEach((el) => {
            const name = el.getAttribute("name");
            if (!snap[name]) snap[name] = [];
            snap[name].push(el.value);
        });
        return snap;
    };

   function renderInventors(count) {
        if (!container || !tpl) return;

        const snap = snapshotCurrent(); // Ambil ketikan layar sekarang
        container.innerHTML = "";

        for (let i = 0; i < count; i++) {
            const node = tpl.content.cloneNode(true);
            const card = node.querySelector(".inventor-card");
            if (node.querySelector(".inv-no")) node.querySelector(".inv-no").textContent = (i + 1);

            fillFromOld(node, i, snap);

            if (card) applyStatusLogic(card);
            container.appendChild(node);
        }
    }

    const setCount = (n) => {
        const v = Math.max(1, Math.min(20, n));
        if (jumlahInput) jumlahInput.value = v;
        renderInventors(v);
    };

    // INIT
    const prefillCountEl = document.getElementById("prefill-count");
    const prefillCount = parseInt(JSON.parse(prefillCountEl?.textContent || "1"), 10) || 1;
    setCount(prefillCount);

    if (btnMinus) btnMinus.onclick = () => setCount(parseInt(jumlahInput.value) - 1);
    if (btnPlus) btnPlus.onclick = () => setCount(parseInt(jumlahInput.value) + 1);
});