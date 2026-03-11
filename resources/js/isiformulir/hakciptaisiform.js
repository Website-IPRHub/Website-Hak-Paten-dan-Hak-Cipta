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

    // 1. NORMALISASI DATA (Biar format array session konsisten)
    const normalizeOldInventor = (raw) => {
        if (!raw || Array.isArray(raw)) return {};
        if (raw.nama && Array.isArray(raw.nama)) return raw;
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

    // 2. FUNGSI PENGISI DATA (FILL FROM OLD)
    const fillFromOld = (root, idx) => {
        const keys = ["nama", "NIK", "nik", "nip_nim", "fakultas", "status", "no_hp", "tlp_rumah", "email", "nidn", "alamat", "kode_pos"];
        keys.forEach(k => {
            const el = root.querySelector(`[name="inventor[${k}][]"]`);
            if (!el) return;

            let val = "";
            if (k === "NIK" || k === "nik") {
                val = oldInventor["NIK"]?.[idx] || oldInventor["nik"]?.[idx] || "";
            } else {
                val = oldInventor[k]?.[idx] || "";
            }

            if (val !== "") {
                el.value = val;
                // JANGAN jalankan dispatchEvent "change" di sini karena bakal ngetrigger reset value NIDN
            }
        });
    };

    // 3. LOGIC STATUS (DOSEN/MAHASISWA)
    const applyStatusLogic = (card) => {
        const statusSelect = card.querySelector('[name="inventor[status][]"]');
        const nidnField = card.querySelector(".nidn-wrap") || card.querySelector(".nidn-field"); // Sesuaikan class
        const nidnInput = card.querySelector('[name="inventor[nidn][]"]');

        const update = (isInit = false) => {
            const isDosen = statusSelect?.value === "Dosen";
            if (nidnField) nidnField.style.display = isDosen ? "block" : "none";
            if (nidnInput) {
                if (isDosen) nidnInput.setAttribute("required", "required");
                else {
                    nidnInput.removeAttribute("required");
                    // HANYA kosongkan value jika ini bukan saat pertama kali render (Init)
                    if (!isInit) nidnInput.value = ""; 
                }
            }
        };

        if (statusSelect) {
            statusSelect.addEventListener("change", () => update(false));
            update(true); // Jalankan pertama kali (Init) tanpa hapus isian
        }
    };

    // 4. RENDERER UTAMA
    function renderInventors(count) {
        if (!container || !tpl) return;
        const snap = snapshotCurrent();
        container.innerHTML = "";

        for (let i = 0; i < count; i++) {
            const node = tpl.content.cloneNode(true);
            const card = node.querySelector(".inventor-card");
            if (node.querySelector(".inv-no")) node.querySelector(".inv-no").textContent = (i + 1);

            // A. Isi dari Session Dulu
            fillFromOld(node, i);

            // B. Timpa pake isian layar (Snapshot) kalo ada
            node.querySelectorAll("[name^='inventor[']").forEach(el => {
                const name = el.getAttribute("name");
                const arr = snap[name] || [];
                if (arr[i] !== undefined && arr[i] !== "") el.value = arr[i];
            });

            // C. Pasang Logic Show/Hide NIDN
            if (card) applyStatusLogic(card);

            container.appendChild(node);
        }
    }

    const snapshotCurrent = () => {
        const snap = {};
        container?.querySelectorAll("[name^='inventor[']").forEach(el => {
            const name = el.getAttribute("name");
            if (!snap[name]) snap[name] = [];
            snap[name].push(el.value);
        });
        return snap;
    };

    const setCount = (n) => {
        const v = Math.max(1, Math.min(20, n));
        if (jumlahInput) jumlahInput.value = v;
        renderInventors(v);
    };

    // INIT
    const prefillCount = parseInt(JSON.parse(document.getElementById("prefill-count")?.textContent || "1"), 10) || 1;
    setCount(prefillCount);

    if (btnMinus) btnMinus.onclick = () => setCount(parseInt(jumlahInput.value) - 1);
    if (btnPlus) btnPlus.onclick = () => setCount(parseInt(jumlahInput.value) + 1);
});