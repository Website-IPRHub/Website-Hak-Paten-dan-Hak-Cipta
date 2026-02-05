// resources/js/admin/paten-detail.js
document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ paten-detail loaded");

  // =========================
  // 1) ACCORDION (docs & inv)
  // =========================
  document.addEventListener("click", (e) => {
    const head = e.target.closest("[data-acc-toggle]");
    if (!head) return;

    const wrap = document.querySelector("[data-paten-detail]");
    if (!wrap) return;

    // cegah event lain ganggu
    e.preventDefault();
    e.stopPropagation();

    const key  = head.getAttribute("data-acc-toggle"); // docs / inv
    const body = wrap.querySelector(`[data-acc-body="${key}"]`);
    const card = wrap.querySelector(`[data-acc-card="${key}"]`);

    console.log("CLICK ACC:", key, { head, body, card });

    if (!body) return;

    const isOpen = !body.hasAttribute("hidden");
    if (isOpen) {
      body.setAttribute("hidden", "");
      card?.classList.remove("is-open");
      head.setAttribute("aria-expanded", "false");
    } else {
      body.removeAttribute("hidden");
      card?.classList.add("is-open");
      head.setAttribute("aria-expanded", "true");
    }
  }, true); // ✅ CAPTURE MODE

  // =========================
  // 2) REVISI POPUP (data-rev)
  // =========================
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-rev-btn]");
    if (btn) {
      e.preventDefault();
      e.stopPropagation();

      const wrapRev = btn.closest("[data-rev]");
      const pop = wrapRev?.querySelector("[data-rev-pop]");
      if (!pop) return;

      document.querySelectorAll("[data-rev-pop]").forEach(p => {
        if (p !== pop) p.hidden = true;
      });

      pop.hidden = !pop.hidden;
      return;
    }

    // klik di luar -> tutup semua
    if (!e.target.closest("[data-rev]")) {
      document.querySelectorAll("[data-rev-pop]").forEach(p => p.hidden = true);
    }
  }, true);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      document.querySelectorAll("[data-rev-pop]").forEach(p => p.hidden = true);
    }
  });

  // optional: buka via hash
  if (location.hash === "#docs") document.querySelector('[data-acc-toggle="docs"]')?.click();
  if (location.hash === "#inv")  document.querySelector('[data-acc-toggle="inv"]')?.click();
});

// =========================
// AJAX SAVE VERIF DOKUMEN (DETAIL PATEN/CIPTA)
// =========================
function csrfToken() {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
}

async function postFormJson(url, formData) {
  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken(),
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: formData,
  });

  let json = null;
  try { json = await res.json(); } catch(e) {}

  if (!res.ok) {
    const msg = json?.message || `Request gagal (${res.status})`;
    throw new Error(msg);
  }
  return json;
}

function setBadge(badgeEl, status) {
  if (!badgeEl) return;

  // buang class status lama (ok/revisi/pending)
  badgeEl.classList.remove('s-ok','s-revisi','s-pending','badge-ok','badge-revisi','badge-pending');

  const st = String(status || 'pending').toLowerCase();

  // support 2 style: status-badge.s-xxx atau badge-xxx
  badgeEl.classList.add('status-badge', `s-${st}`);

  badgeEl.textContent = st.toUpperCase();
}

function setFormLoading(form, loading, label = 'Menyimpan...') {
  const btn = form.querySelector('button[type="submit"]');
  if (!btn) return;

  if (!btn.dataset.originalText) btn.dataset.originalText = btn.textContent.trim();

  btn.disabled = loading;
  btn.textContent = loading ? label : (btn.dataset.originalText || 'Simpan');
}

// listen submit untuk semua form doc
document.addEventListener('submit', async (e) => {
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;
  if (!form.classList.contains('js-doc-form')) return;

  e.preventDefault();

  const actionUrl = form.getAttribute('action');
  const fd = new FormData(form);

  setFormLoading(form, true);

  try {
    const json = await postFormJson(actionUrl, fd);
    if (!json?.ok) throw new Error(json?.message || 'Gagal simpan');

    const docKey = json.doc?.doc_key;
    const status = json.doc?.status || 'pending';

    // cari badge untuk doc ini (di card dokumen yang sama)
    // pastikan badge kamu punya: data-doc-badge data-doc-key="draft_paten" dll
    let badge = null;

    // 1) cari di wrapper dokumen terdekat
    const docWrap = form.closest('[data-doc-wrap]') || form.closest('.doc-item') || form.closest('[data-doc-item]');
    if (docWrap && docKey) {
      badge = docWrap.querySelector(`[data-doc-badge][data-doc-key="${docKey}"]`);
    }

    // 2) fallback cari global
    if (!badge && docKey) {
      badge = document.querySelector(`[data-doc-badge][data-doc-key="${docKey}"]`);
    }

    setBadge(badge, status);

    // tutup popup revisi kalau ada
    const pop = form.closest('[data-rev-pop]');
    if (pop) pop.hidden = true;

    console.log('✅ saved:', docKey, status, json);

  } catch (err) {
    alert(err.message || 'Terjadi error');
    console.error(err);
  } finally {
    setFormLoading(form, false);
  }
});

const docKey = new URLSearchParams(location.search).get('doc_key');
if (docKey) {
  // buka accordion docs
  document.querySelector('[data-acc-toggle="docs"]')?.click();
  // optional scroll ke doc item yg sesuai (kalau kamu kasih data attr)
}
