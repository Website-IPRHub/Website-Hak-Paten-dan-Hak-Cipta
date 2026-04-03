document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ lihatdetail loaded");

  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "";

  const wrap =
    document.querySelector("[data-paten-detail]") ||
    document.querySelector("[data-cipta-detail]");

  // =========================
  // ACCORDION
  // =========================
  if (wrap) {
    wrap.querySelectorAll("[data-acc-toggle]").forEach((head) => {
      head.addEventListener("click", (e) => {
        e.preventDefault();

        const key = head.getAttribute("data-acc-toggle");
        const body = wrap.querySelector(`[data-acc-body="${key}"]`);
        const card = wrap.querySelector(`[data-acc-card="${key}"]`);
        if (!body) return;

        const isOpen = !body.hasAttribute("hidden");
        if (isOpen) {
          body.setAttribute("hidden", "");
          card?.classList.remove("open");
          head.setAttribute("aria-expanded", "false");
        } else {
          body.removeAttribute("hidden");
          card?.classList.add("open");
          head.setAttribute("aria-expanded", "true");
        }
      });
    });
  }

  // =========================
  // WA PAYLOAD (SETELAH KIRIM REVISI / APPROVE) - SUPPORT MULTI LINK
  // butuh blade: data-was='[...]' (json array)
  // =========================
  // =========================
// WA PAYLOAD (SETELAH KIRIM REVISI / APPROVE) - SUPPORT MULTI LINK
// butuh blade: <div id="waPayload" data-was='[...]' data-label="..."></div>
// =========================
const waEl = document.getElementById("waPayload");
if (waEl) {
  const waLabel = waEl.dataset.label || "Kirim WhatsApp";

  const storageKey =
    "waPayloadShown::" + (waEl.dataset.key || (location.pathname + location.search));

  if (sessionStorage.getItem(storageKey) === "1") {
    waEl.remove();
  } else {
    let waLinks = [];
    try {
      waLinks = JSON.parse(waEl.dataset.was || "[]");
    } catch (e) {
      waLinks = [];
    }
    if ((!waLinks || waLinks.length === 0) && waEl.dataset.wa) {
      waLinks = [waEl.dataset.wa];
    }

    const markAsShown = () => {
      sessionStorage.setItem(storageKey, "1");
      waEl.remove();
    };

    if (window.Swal) {
      Swal.fire({
        icon: "success",
        title: "Berhasil",
        text: 'Revisi berhasil dikirim. Silakan klik tombol "Kirim WA" untuk menghubungi pemohon.',
        confirmButtonText: "OK",
      }).then(() => {
        markAsShown();
      });
    } else {
      alert('Revisi berhasil dikirim. Silakan klik tombol "Kirim WA" untuk menghubungi pemohon.');
      markAsShown();
    }
  }
}
  // =========================
  // HELPER: refresh tombol Simpan & Kirim
  // =========================
const refreshCanSend = () => {
  const btn = document.getElementById("btnSendRevisi");
  if (!btn) return;

  const badges = document.querySelectorAll("[data-doc-badge]");
  const canSend = Array.from(badges).some((b) => {
    const st = (b.textContent || "").trim().toLowerCase();
    return st === "revisi";
  });

  btn.disabled = !canSend;
};

  // =========================
  // HELPER: update badge dokumen
  // =========================
  const applyBadge = (docKey, status) => {
    const badge = document.querySelector(
      `[data-doc-badge][data-doc-key="${docKey}"]`
    );
    if (!badge) return;

    const st = String(status || "pending").toLowerCase();
    badge.className = `badge badge-${st}`;
    badge.textContent = st.toUpperCase();
  };

  const setDocStatus = (docKey, status) => {
  const docWrap = document.querySelector(`[data-doc-wrap][data-doc-key="${docKey}"]`);
  if (!docWrap) return;
  docWrap.dataset.docStatus = String(status || "pending").toLowerCase();
};

const applyDocActionLock = (docKey, status) => {
  const st = String(status || "pending").toLowerCase();

  const okBtn = document.querySelector(`[data-doc-ok-btn][data-doc-key="${docKey}"]`);
  const revisiBtn = document.querySelector(`[data-doc-revisi-btn][data-doc-key="${docKey}"]`);

  if (okBtn) okBtn.disabled = false;

  if (revisiBtn) {
    const canRevisi = revisiBtn.dataset.canRevisi === "1";
    revisiBtn.disabled = (st === "ok") || !canRevisi;
  }

  if (st === "ok") {
    const pop = revisiBtn?.closest("[data-rev]")?.querySelector("[data-rev-pop]");
    if (pop) pop.hidden = true;
  }
};

const getAllDocStatuses = () => {
  return Array.from(document.querySelectorAll("[data-doc-wrap]"))
    .map((el) => (el.dataset.docStatus || "pending").toLowerCase());
};

const refreshFooterButtons = () => {
  const btnSend = document.getElementById("btnSendRevisi");
  const btnApprove = document.getElementById("btnApprove");

  const statuses = getAllDocStatuses();
  const hasRevisi = statuses.some((st) => st === "revisi");
  const allOk = statuses.length > 0 && statuses.every((st) => st === "ok");

  if (btnSend) btnSend.disabled = !hasRevisi;
  if (btnApprove) btnApprove.disabled = !(allOk && !hasRevisi);
};

document.querySelectorAll("[data-doc-badge]").forEach((badge) => {
  const docKey = badge.dataset.docKey;
  const status = (badge.textContent || "").trim().toLowerCase();
  if (!docKey) return;

  setDocStatus(docKey, status);
  applyDocActionLock(docKey, status);
});

refreshFooterButtons();

const showWaLinksModal = async (waLinks, title = "Kirim WhatsApp") => {
  const links = Array.isArray(waLinks) ? waLinks.filter(Boolean) : [];

  if (!links.length) {
    if (window.Swal) {
      await Swal.fire({
        icon: "info",
        title: "Info",
        text: "Nomor WhatsApp belum tersedia untuk status ini.",
      });
    } else {
      alert("Nomor WhatsApp belum tersedia untuk status ini.");
    }
    return;
  }

  if (window.Swal) {
    if (links.length === 1) {
      const waLink = links[0];
      await Swal.fire({
        icon: "success",
        title,
        text: "Klik tombol di bawah untuk membuka WhatsApp.",
        showCancelButton: true,
        confirmButtonText: "Buka WhatsApp",
        cancelButtonText: "Tutup",
      }).then((r) => {
        if (r.isConfirmed && waLink) window.open(waLink, "_blank");
      });
    } else {
      const listHtml = links
        .map(
          (l, i) =>
            `<div style="margin:6px 0;"><a href="${l}" target="_blank">Kirim ke nomor #${i + 1}</a></div>`
        )
        .join("");

      await Swal.fire({
        icon: "success",
        title,
        html: `Pilih nomor tujuan WhatsApp:<br><br>${listHtml}`,
        confirmButtonText: "OK",
      });
    }
  } else {
    if (links.length === 1) {
      window.open(links[0], "_blank");
    } else {
      alert("Ada beberapa nomor WhatsApp. Silakan gunakan versi dengan SweetAlert.");
    }
  }
};

  // =========================
// ✅ TRACK DOKUMEN TERBARU YANG DIUBAH (dirty doc_keys)
// =========================
const DIRTY_KEY = "admin_dirty_doc_keys::" + location.pathname;

const getDirty = () => {
  try { return JSON.parse(sessionStorage.getItem(DIRTY_KEY) || "[]"); }
  catch { return []; }
};

const addDirty = (docKey) => {
  if (!docKey) return;
  const cur = new Set(getDirty());
  cur.add(String(docKey));
  sessionStorage.setItem(DIRTY_KEY, JSON.stringify(Array.from(cur)));
};

const clearDirty = () => {
  sessionStorage.removeItem(DIRTY_KEY);
};


  // =========================
  // ✅ TAMBAHAN: update UI box "Revisi" biar langsung muncul tanpa reload
  // (tidak mengubah logic lama, hanya sinkron UI)
  // =========================
  // ✅ helper: buka semua parent yang ketutup (hidden / display none / d-none)
  const unhideParents = (el, stopAt) => {
    let cur = el;
    while (cur && cur !== stopAt && cur instanceof HTMLElement) {
      if (cur.hidden) cur.hidden = false;
      cur.classList?.remove("hidden", "d-none");
      if (cur.style && cur.style.display === "none") cur.style.display = "";
      cur = cur.parentElement;
    }
  };

 const formatDateID = (dt) => {
  if (!dt) return "-";
  try {
    return new Intl.DateTimeFormat("id-ID", {
      day: "2-digit",
      month: "short",
      year: "numeric",
    }).format(new Date(dt));
  } catch (e) {
    return "-";
  }
};

const formatDateTimeID = (dt) => {
  try {
    return new Date(dt).toLocaleString("id-ID", {
      timeZone: "Asia/Jakarta",
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
    });
  } catch {
    return "-";
  }
};

const updateRevisiUI = (docKey, doc) => {
  const docWrap = document.querySelector(`[data-doc-wrap][data-doc-key="${docKey}"]`);
  if (!docWrap) return;

  const incomingWrap  = docWrap.querySelector("[data-incoming-wrap]");
  const adminNoteWrap = docWrap.querySelector("[data-admin-note-wrap]");
  const adminDateEl   = docWrap.querySelector("[data-admin-note-date]");
  const adminTextEl   = docWrap.querySelector("[data-admin-note-text]");
  const pemohonEmpty  = docWrap.querySelector("[data-pemohon-empty]");
  const dtRaw = doc?.updated_at || doc?.created_at || new Date();
  const nowText = formatDateTimeID(dtRaw);

  const status = String(doc?.status || "pending").toLowerCase();
  const note   = String(doc?.note || "").trim();

  // box revisi muncul kalau status revisi / note ada
  const hasExistingArchive =
  !!docWrap.querySelector(".incoming-table .incoming-row:not(.incoming-head)") ||
  !!docWrap.querySelector("[data-empty-cycle-row]") ||
  !!docWrap.querySelector(".doc-dot");

const showRevisi = status === "revisi" || note.length > 0 || hasExistingArchive;

  // tampilkan wrap revisi
  if (incomingWrap) incomingWrap.hidden = !showRevisi;
  if (showRevisi && incomingWrap) {
  unhideParents(incomingWrap, docWrap);
}

  // tampilkan admin box kalau note ada
  if (adminNoteWrap) adminNoteWrap.hidden = note.length === 0;

  // isi catatan admin text (biar langsung berubah)
  if (adminTextEl) {
    adminTextEl.textContent = note;
    adminTextEl.title = note;
  }

  // update tanggal admin -> format TANGGAL doang (bukan "baru saja")
 if (adminDateEl) {
  adminDateEl.textContent = new Date(dtRaw).toLocaleString("id-ID", { timeZone: "Asia/Jakarta" });
}

  // pemohon empty selalu tampil (karena belum upload)
  if (pemohonEmpty) pemohonEmpty.hidden = false;

  // dot indikator -> merah kalau belum ada upload pemohon
  if (showRevisi) {
    let dot = docWrap.querySelector(".doc-dot");
    if (!dot) {
      const nameEl = docWrap.querySelector(".doc-name");
      if (nameEl) {
        dot = document.createElement("span");
        dot.className = "doc-dot red";
        dot.title = "Menunggu upload revisi dari pemohon";
        nameEl.prepend(dot);
      }
    } else {
      dot.classList.remove("green");
      dot.classList.add("red");
      dot.title = "Menunggu upload revisi dari pemohon";
    }
  } else {
    docWrap.querySelector(".doc-dot")?.remove();
  }

  // =========================
  // KUNCI: buka "Detail Revisi" langsung
  // =========================
  const details = docWrap.querySelector(".incoming-details");
  if (details) {
    details.hidden = !showRevisi;
    details.open = true; // <-- ini yang bikin langsung kebuka
  }


  // =========================
  // KUNCI: update ROW ADMIN di incoming-table (div-based)
  // (bukan bikin <tr>)
  // =========================
const table = docWrap.querySelector(".incoming-table");
if (table) {
  if (showRevisi) {
    const NOTE_LIMIT = 80;
    const oneLine = (s) => String(s || "").replace(/\r\n|\r|\n/g, " ").trim();
    const newlineCount = (s) => (String(s || "").match(/\r\n|\r|\n/g) || []).length;

    const shouldShowMoreByText = (s, limit = NOTE_LIMIT) =>
      oneLine(s).length > limit || newlineCount(s) >= 2;

    const fullNote = (note && note.trim()) ? note.trim() : "";
const previewLine = oneLine(fullNote);
const preview = previewLine.length > NOTE_LIMIT ? (previewLine.slice(0, NOTE_LIMIT) + "…") : previewLine;

const more = fullNote !== "" && shouldShowMoreByText(fullNote, NOTE_LIMIT);

// ✅ hanya bikin row baru kalau memang status revisi + ada note
const shouldInsertAdminRow = status === "revisi" && fullNote !== "";

// ✅ anti dobel: cukup pakai docKey + note, jangan pakai timestamp
const sig = `${docKey}::${fullNote}`;

if (shouldInsertAdminRow && !table.querySelector(`[data-rev-sig="${CSS.escape(sig)}"]`)) {
      const adminRow = document.createElement("div");
      adminRow.className = "incoming-row";
      adminRow.setAttribute("data-js-admin-row", "1");
      adminRow.setAttribute("data-rev-sig", sig);

  adminRow.innerHTML = `
  <div class="incoming-cell">
    <div class="note-cell">
      <div class="note-preview truncate-1" title="${escapeHtml(preview)}">
        ${escapeHtml(preview)}
      </div>

      <div class="note-full" hidden style="white-space:pre-wrap; overflow-wrap:anywhere; word-break:break-word; max-width:100%;">
  ${escapeHtml(fullNote)}
</div>

      ${more ? `<button type="button" class="btn-note-more js-note-more">Selengkapnya</button>` : ``}
    </div>
  </div>

  <div class="incoming-cell muted">Pemohon belum upload file revisi.</div>
  <div class="incoming-cell muted">-</div>
`;

      const head = table.querySelector(".incoming-head");
      if (head) head.insertAdjacentElement("afterend", adminRow);
      else table.prepend(adminRow);
      // ✅ reset state toggle untuk row baru (biar gak kebawa state lama)
  adminRow.querySelectorAll(".note-cell").forEach(c => c.dataset.open = "0");

  // ✅ INI TEMPATNYA: setelah row masuk DOM, refresh tombol "Selengkapnya"
  requestAnimationFrame(() => refreshNoteMoreButtons());
    }
  }

  // ✅ update summary SETELAH row mungkin ditambah
  const summary = docWrap.querySelector(".incoming-summary");
  if (summary) {
    const rows = table.querySelectorAll(".incoming-row:not(.incoming-head)");
    summary.textContent = `Detail Revisi (${rows.length})`;
  }
}

  // optional: kalau backend ngirim url lampiran admin
  const attUrl = doc?.admin_attachment_url;
  if (attUrl) {
    let existing = docWrap.querySelector("[data-admin-attachment-wrap]");
    if (!existing) {
      const pop = docWrap.querySelector("[data-rev-pop]");
      if (pop) {
        existing = document.createElement("div");
        existing.setAttribute("data-admin-attachment-wrap", "1");
        existing.style.marginTop = "6px";
        existing.style.fontSize = "12px";
        pop.appendChild(existing);
      }
    }
    if (existing) {
      existing.innerHTML = `Lampiran admin: <a href="${attUrl}" target="_blank">Lihat file</a>`;
    }
  }
};


// =========================
// TOGGLE POPOVER REVISI (delegation)
// =========================
document.addEventListener("click", (e) => {
  const popInside = e.target.closest("[data-rev-pop]");
  const swalInside = e.target.closest(".swal2-container");
  const revBtnNew = e.target.closest("[data-doc-revisi-btn]");

  if (swalInside) return;
  if (revBtnNew) return;
  if (popInside) return;

  document.querySelectorAll("[data-rev-pop]").forEach((p) => (p.hidden = true));
  document.body.classList.remove("modal-rev-open");
});

  document.addEventListener("click", async (e) => {
  const okBtn = e.target.closest("[data-doc-ok-btn]");
  if (!okBtn) return;

  e.preventDefault();

  const form = okBtn.closest("form");
  if (!form) return;

  const actionInput = form.querySelector('input[name="action"]');
  if (!actionInput) return;

  const docKey = okBtn.dataset.docKey;
  const docWrap = document.querySelector(`[data-doc-wrap][data-doc-key="${docKey}"]`);
  const currentStatus = (docWrap?.dataset.docStatus || "pending").toLowerCase();

  if (window.Swal) {
    const result = await Swal.fire({
      icon: "question",
      title: "Konfirmasi Dokumen",
      text: "Apakah dokumen ini sudah benar?",
      showCancelButton: true,
      confirmButtonText: "Sudah",
      cancelButtonText: "Belum",
      reverseButtons: true,
    });

    if (result.isConfirmed) {
      actionInput.value = "ok";
      form.requestSubmit();
      return;
    }

    if (result.dismiss === Swal.DismissReason.cancel) {
      actionInput.value = currentStatus === "revisi" ? "revisi" : "pending";
      form.requestSubmit();
      return;
    }
  } else {
    const yes = confirm("Apakah dokumen ini sudah benar?");
    actionInput.value = yes ? "ok" : (currentStatus === "revisi" ? "revisi" : "pending");
    form.requestSubmit();
  }
});

 document.addEventListener("click", async (e) => {
  const revBtn = e.target.closest("[data-doc-revisi-btn]");
  if (!revBtn) return;

  if (revBtn.disabled) {
    e.preventDefault();
    return;
  }

  const wrapRev = revBtn.closest("[data-rev]");
  const myPop = wrapRev?.querySelector("[data-rev-pop]");
  if (!myPop) return;

  e.preventDefault();
  e.stopPropagation();

  if (window.Swal) {
    const result = await Swal.fire({
      icon: "warning",
      title: "Konfirmasi Revisi",
      text: "Apakah dokumen ini benar perlu revisi?",
      showCancelButton: true,
      confirmButtonText: "Ya, Revisi",
      cancelButtonText: "Batal",
      reverseButtons: true,
    });

    if (!result.isConfirmed) return;
  } else {
    const yes = confirm("Apakah dokumen ini benar perlu revisi?");
    if (!yes) return;
  }

  setTimeout(() => {
    document.querySelectorAll("[data-rev-pop]").forEach((p) => {
      if (p !== myPop) p.hidden = true;
    });

    myPop.hidden = false;
    document.body.classList.add("modal-rev-open");

    const ta = myPop.querySelector("textarea[name='note']");
    if (ta) ta.focus();
  }, 120);
});

  // =========================
  // SUBMIT OK/REVISI (AJAX) - SINGLE HANDLER
  // =========================
  document.addEventListener("submit", async (e) => {
  const form = e.target;

  // 🔥 KUNCI: form kirim revisi jangan diproses handler global
  if (form.id === "sendRevisiForm") return;

  if (!form.classList.contains("js-doc-form")) return;


    e.preventDefault();

    if (form.dataset.loading === "1") return;
    form.dataset.loading = "1";

    const submitBtn = form.querySelector('button[type="submit"]');

    if (submitBtn && !submitBtn.dataset.originalText) {
      submitBtn.dataset.originalText = submitBtn.textContent || "";
    }

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Menyimpan...";
    }

    try {
      const url = form.getAttribute("action");
      const fd = new FormData(form);

      const res = await fetch(url, {
        method: "POST",
        credentials: "include",
        headers: {
          "X-CSRF-TOKEN": csrf,
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
        body: fd,
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data?.ok) {
        throw new Error(data?.message || `Gagal simpan (${res.status})`);
      }

      // ✅ match controller kamu: data.doc.doc_key & data.doc.status
      const docKey = data?.doc?.doc_key;
      const status = data?.doc?.status;

      if (docKey) {
        applyBadge(docKey, status);
        setDocStatus(docKey, status);
        applyDocActionLock(docKey, status);

        updateRevisiUI(docKey, data?.doc);

        if (String(status).toLowerCase() === "revisi") {
          addDirty(docKey);
        }

        refreshFooterButtons();
      }
      
      
      // tutup popover revisi
      const pop = form.closest("[data-rev-pop]");
      if (pop) pop.hidden = true;
      document.body.classList.remove("modal-rev-open"); // ✅ penting

      refreshCanSend();
    } catch (err) {
      console.error(err);
      if (window.Swal) {
        await Swal.fire({
          icon: "error",
          title: "Gagal",
          text: err.message || "Gagal menyimpan status dokumen.",
        });
      } else {
        alert(err.message || "Gagal menyimpan status dokumen.");
      }
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = submitBtn.dataset.originalText || "OK";
      }
      form.dataset.loading = "0";
    }
  });

  // =========================
// APPROVE (AJAX)
// =========================
const btnApprove = document.getElementById("btnApprove");
if (btnApprove) {
  btnApprove.addEventListener("click", async () => {
    if (btnApprove.dataset.approved === "1") return;

    const statuses = getAllDocStatuses();
    const hasRevisi = statuses.some((st) => st === "revisi");
    const allOk = statuses.length > 0 && statuses.every((st) => st === "ok");

    if (hasRevisi) {
      if (window.Swal) {
        await Swal.fire({
          icon: "warning",
          title: "Approve Ditolak",
          text: "Pengajuan ini masih ada dokumen revisi, jadi belum bisa di-approve.",
        });
      } else {
        alert("Pengajuan ini masih ada dokumen revisi, jadi belum bisa di-approve.");
      }
      return;
    }

    if (!allOk) {
      if (window.Swal) {
        await Swal.fire({
          icon: "warning",
          title: "Dokumen Belum Lengkap Dicek",
          text: "Semua dokumen harus berstatus OK terlebih dahulu sebelum approve.",
        });
      } else {
        alert("Semua dokumen harus berstatus OK terlebih dahulu sebelum approve.");
      }
      return;
    }

    const confirmApprove = window.Swal
      ? await Swal.fire({
          icon: "question",
          title: "Konfirmasi Approve",
          text: "Apakah yakin pengajuan ini siap di-approve?",
          showCancelButton: true,
          confirmButtonText: "Ya, Approve",
          cancelButtonText: "Batal",
          reverseButtons: true,
        })
      : { isConfirmed: confirm("Apakah yakin pengajuan ini siap di-approve?") };

    if (!confirmApprove.isConfirmed) return;

    const url = btnApprove.dataset.url;
    if (!url) return;

    const oldText = btnApprove.textContent;
    btnApprove.disabled = true;
    btnApprove.textContent = "Menyimpan...";

    try {
      const res = await fetch(url, {
        method: "POST",
        credentials: "include",
        headers: {
          "X-CSRF-TOKEN": csrf,
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok || data?.ok === false) {
        throw new Error(data?.message || `Gagal approve (${res.status})`);
      }

      const statusEl = document.getElementById("statusPengajuanBadge");
      const newStatus = data?.status || "approve";

      if (statusEl) {
        statusEl.textContent = String(newStatus).toUpperCase();
        statusEl.className = `status-badge s-${String(newStatus).toLowerCase()}`;
      }

      clearDirty();

      const btnSend = document.getElementById("btnSendRevisi");
      if (btnSend) {
        btnSend.disabled = true;
        btnSend.classList.add("is-disabled-state");
      }

      btnApprove.disabled = true;
      btnApprove.textContent = "Sudah Approve";
      btnApprove.dataset.approved = "1";
      btnApprove.classList.add("is-approved");

      document.getElementById("waPayload")?.remove();

      if (window.Swal) {
        await Swal.fire({
          icon: "success",
          title: "Approved",
          text: 'Pengajuan berhasil di-approve. Silakan klik tombol "Kirim WA" untuk menghubungi pemohon.',
          confirmButtonText: "OK",
        });
      } else {
        alert('Pengajuan berhasil di-approve. Silakan klik tombol "Kirim WA" untuk menghubungi pemohon.');
      }
    } catch (err) {
      console.error(err);
      if (window.Swal) {
        await Swal.fire({
          icon: "error",
          title: "Gagal",
          text: err.message || "Terjadi kesalahan.",
        });
      } else {
        alert(err.message || "Terjadi kesalahan.");
      }
    } finally {
      if (btnApprove.dataset.approved !== "1") {
        btnApprove.disabled = false;
        btnApprove.textContent = oldText;
      }
    }
  });
}


const btnSendWA = document.getElementById("btnSendWA");
if (btnSendWA) {
  btnSendWA.addEventListener("click", async () => {
    const url = btnSendWA.dataset.url;
    if (!url) return;

    const oldText = btnSendWA.textContent;
    btnSendWA.disabled = true;
    btnSendWA.textContent = "Memuat...";

    try {
      const res = await fetch(url, {
        method: "GET",
        credentials: "include",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data?.ok) {
        throw new Error(data?.message || `Gagal memuat WA (${res.status})`);
      }

      await showWaLinksModal(data?.wa_links || [], data?.title || "Kirim WhatsApp");
    } catch (err) {
      console.error(err);
      if (window.Swal) {
        await Swal.fire({
          icon: "error",
          title: "Gagal",
          text: err.message || "Gagal memuat daftar WhatsApp.",
        });
      } else {
        alert(err.message || "Gagal memuat daftar WhatsApp.");
      }
    } finally {
      btnSendWA.disabled = false;
      btnSendWA.textContent = oldText;
    }
  });
}

// =========================
// ✅ SIMPAN & KIRIM (AJAX) - ANTI DOBEL SUBMIT + KIRIM HANYA DIRTY DOC
// =========================
const sendRevisiForm = document.getElementById("sendRevisiForm");
const btnSendRevisi = document.getElementById("btnSendRevisi");

if (sendRevisiForm && btnSendRevisi) {
  // ✅ guard: kalau file ini ke-load 2x, event submit gak kebinding 2x
  if (!window.__SEND_REVISI_BOUND__) {
    window.__SEND_REVISI_BOUND__ = true;

    sendRevisiForm.addEventListener(
      "submit",
      async (e) => {
        e.preventDefault();

        // ✅ bunuh kemungkinan listener lain yang ikut nangkep submit
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === "function") {
          e.stopImmediatePropagation();
        }

        // ✅ anti double click / double submit
        if (sendRevisiForm.dataset.loading === "1") return;
        sendRevisiForm.dataset.loading = "1";

        const url = sendRevisiForm.getAttribute("action");
        if (!url) {
          sendRevisiForm.dataset.loading = "0";
          return;
        }

        const oldText = btnSendRevisi.textContent;
        btnSendRevisi.disabled = true;
        btnSendRevisi.textContent = "Menyimpan...";

        try {
          const dirty = getDirty();

          // ✅ kalau tidak ada perubahan terbaru, jangan kirim
          if (!dirty || dirty.length === 0) {
            if (window.Swal) {
              await Swal.fire({
                icon: "info",
                title: "Info",
                text: "Tidak ada revisi baru untuk dikirim.",
              });
            } else {
              alert("Tidak ada revisi baru untuk dikirim.");
            }
            return;
          }

          const fd = new FormData(sendRevisiForm);
          dirty.forEach((k) => fd.append("doc_keys[]", k));

          const res = await fetch(url, {
            method: "POST",
            credentials: "include",
            headers: {
              "X-CSRF-TOKEN": csrf,
              "X-Requested-With": "XMLHttpRequest",
              Accept: "application/json",
            },
            body: fd,
          });

          const data = await res.json().catch(() => ({}));
          if (!res.ok || !data?.ok) {
            throw new Error(data?.message || `Gagal kirim revisi (${res.status})`);
          }

          // ✅ setelah sukses -> bersihin dirty biar gak kekirim ulang
          clearDirty();

          // ✅ bunuh trigger swal lama dari session (kalau ada)
          document.getElementById("waPayload")?.remove();

          if (window.Swal) {
            await Swal.fire({
              icon: "success",
              title: "Berhasil",
              text: 'Revisi berhasil dikirim. Silakan klik tombol "Kirim WA" untuk menghubungi pemohon.',
              confirmButtonText: "OK",
            });
          } else {
            alert('Revisi berhasil dikirim. Silakan klik tombol "Kirim WA" untuk menghubungi pemohon.');
          }
        } catch (err) {
          console.error(err);

          if (window.Swal) {
            await Swal.fire({
              icon: "error",
              title: "Gagal",
              text: err?.message || "Terjadi kesalahan.",
            });
          } else {
            alert(err?.message || "Terjadi kesalahan.");
          }
        } finally {
          sendRevisiForm.dataset.loading = "0";
          btnSendRevisi.disabled = false;
          btnSendRevisi.textContent = oldText;
        }
      },
      true
    );
  }
}

// helper kecil biar aman
function escapeHtml(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
  // =========================
  // NOTE: tampilkan "Selengkapnya" hanya jika kepotong
  // =========================
const refreshNoteMoreButtons = () => {
  const NOTE_LIMIT = 80;

  const oneLine = (s) => String(s || "").replace(/\r\n|\r|\n/g, " ").trim();
  const nlCount = (s) => (String(s || "").match(/\r\n|\r|\n/g) || []).length;

  document.querySelectorAll(".note-cell").forEach((cell) => {
    const prevEl = cell.querySelector(".note-preview");
    const fullEl = cell.querySelector(".note-full");
    const btn = cell.querySelector(".js-note-more");
    if (!prevEl || !fullEl || !btn) return;

    if (cell.dataset.open == null) {
      cell.dataset.open = cell.getAttribute("data-open") || "0";
    }

    const fullTextRaw = (fullEl.textContent || "").trim();
    const fullOneLine = oneLine(fullTextRaw);

    // ✅ KUNCI: tombol muncul kalau FULL-nya memang panjang / multiline
    const shouldShow =
      fullOneLine !== "-" &&
      (fullOneLine.length > NOTE_LIMIT || nlCount(fullTextRaw) >= 2);

    btn.hidden = !shouldShow;

    // kalau gak perlu, paksa state closed
    if (!shouldShow) {
      fullEl.hidden = true;
      prevEl.hidden = false;
      btn.textContent = "Selengkapnya";
      cell.dataset.open = "0";
      return;
    }

    const isOpen = cell.dataset.open === "1";
    btn.textContent = isOpen ? "Tutup" : "Selengkapnya";

    if (isOpen) {
      prevEl.hidden = true;
      fullEl.hidden = false;
    } else {
      fullEl.hidden = true;
      prevEl.hidden = false;
    }
  });
};

refreshNoteMoreButtons();
window.addEventListener("resize", refreshNoteMoreButtons);
  refreshCanSend();
  
});

document.addEventListener("click", (e) => {
  const btn = e.target.closest(".js-note-more");
  if (!btn) return;

  const cell = btn.closest(".note-cell");
  if (!cell) return;

  const preview = cell.querySelector(".note-preview");
  const full = cell.querySelector(".note-full");
  if (!preview || !full) return;

  const isOpen = cell.dataset.open === "1";

  if (!isOpen) {
    preview.setAttribute("hidden", "");
    full.removeAttribute("hidden");
    btn.textContent = "Tutup";
    cell.dataset.open = "1";
  } else {
    full.setAttribute("hidden", "");
    preview.removeAttribute("hidden");
    btn.textContent = "Selengkapnya";
    cell.dataset.open = "0";
  }
});

document.addEventListener("click", (e) => {
  const btn = e.target.closest(".note-close");
  if (!btn) return;

  const details = btn.closest("details.note-detail");
  if (details) details.open = false;
});

