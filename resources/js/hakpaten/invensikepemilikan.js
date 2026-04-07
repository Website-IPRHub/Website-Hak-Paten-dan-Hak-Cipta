document.addEventListener("DOMContentLoaded", () => {
  const jumlahInput = document.getElementById("jumlah_inventor");
  const container = document.getElementById("inventor-container");
  const tpl = document.getElementById("inventor-template");
  const oldDataEl = document.getElementById("old-inventor-data");

  if (!jumlahInput || !container || !tpl) return;

  let oldData = {};
  try {
    oldData = JSON.parse(oldDataEl?.textContent || "{}");
  } catch (e) {
    oldData = {};
  }

  const getOld = (key, i) => (oldData?.[key]?.[i] ?? "");

  const setFieldValue = (root, selector, value) => {
    const el = root.querySelector(selector);
    if (!el) return;
    el.value = value ?? "";
  };

  const render = () => {
    const n = Math.max(1, Math.min(20, parseInt(jumlahInput.value || "1", 10)));
    container.innerHTML = "";

    for (let i = 0; i < n; i++) {
      const frag = tpl.content.cloneNode(true);

      // nomor inventor
      const no = frag.querySelector(".inv-no");
      if (no) no.textContent = String(i + 1);

      // isi old value (kalau ada)
      setFieldValue(frag, 'input[name="inventor[nama][]"]', getOld("nama", i));
      setFieldValue(frag, 'input[name="inventor[kewarganegaraan][]"]', getOld("kewarganegaraan", i));
      setFieldValue(frag, 'textarea[name="inventor[alamat][]"]', getOld("alamat", i));
      setFieldValue(frag, 'input[name="inventor[kode_pos][]"]', getOld("kode_pos", i));
      setFieldValue(frag, 'input[name="inventor[email][]"]', getOld("email", i));
      setFieldValue(frag, 'input[name="inventor[no_hp][]"]', getOld("no_hp", i));

      container.appendChild(frag);
    }
  };

  jumlahInput.addEventListener("input", render);
  jumlahInput.addEventListener("change", render);

  render();
});
