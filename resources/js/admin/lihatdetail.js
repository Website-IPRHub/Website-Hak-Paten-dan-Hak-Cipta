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

  // ✅ KUNCI: Biar popup ini cuma muncul sekali per TAB (ga balik2 muncul lagi)
  const storageKey =
    "waPayloadShown::" + (waEl.dataset.key || (location.pathname + location.search));

  if (sessionStorage.getItem(storageKey) === "1") {
    // udah pernah tampil di tab ini -> buang triggernya biar gak muncul lagi
    waEl.remove();
  } else {
    // support: data-was (json array) / fallback data-wa (single)
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
      waEl.remove(); // matiin pemicu swal lama
    };

    if (window.Swal) {
      if (waLinks.length <= 1) {
        const waLink = waLinks[0] || null;

        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Revisi berhasil dikirim.",
          showCancelButton: true,
          confirmButtonText: waLabel,
          cancelButtonText: "Tutup",
        }).then((r) => {
          // ✅ setelah popup selesai, tandain sudah tampil
          markAsShown();

          if (r.isConfirmed && waLink) window.open(waLink, "_blank");
        });
      } else {
        const listHtml = waLinks
          .map((l, i) =>
            `<div style="margin:6px 0;">
              <a href="${l}" target="_blank">Kirim ke nomor #${i + 1}</a>
            </div>`
          )
          .join("");

        Swal.fire({
          icon: "success",
          title: "Berhasil",
          html: `Pilih nomor tujuan WhatsApp:<br><br>${listHtml}`,
          confirmButtonText: "OK",
        }).then(() => {
          // ✅ setelah popup selesai, tandain sudah tampil
          markAsShown();
        });
      }
    } else {
      // fallback tanpa Swal
      if (waLinks.length === 1 && confirm("Kirim WhatsApp sekarang?")) {
        markAsShown();
        window.open(waLinks[0], "_blank");
      } else if (waLinks.length > 1) {
        markAsShown();
        waLinks.forEach((l) => window.open(l, "_blank"));
      } else {
        markAsShown();
      }
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
      return st === "ok" || st === "revisi";
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

const updateRevisiUI = (docKey, doc) => {
  const docWrap = document.querySelector(`[data-doc-wrap][data-doc-key="${docKey}"]`);
  if (!docWrap) return;

  const incomingWrap  = docWrap.querySelector("[data-incoming-wrap]");
  const adminNoteWrap = docWrap.querySelector("[data-admin-note-wrap]");
  const adminDateEl   = docWrap.querySelector("[data-admin-note-date]");
  const adminTextEl   = docWrap.querySelector("[data-admin-note-text]");
  const pemohonEmpty  = docWrap.querySelector("[data-pemohon-empty]");

  const status = String(doc?.status || "pending").toLowerCase();
  const note   = String(doc?.note || "").trim();

  // box revisi muncul kalau status revisi / note ada
  const showRevisi = status === "revisi" || note.length > 0;

  // tampilkan wrap revisi
  if (incomingWrap) incomingWrap.hidden = !showRevisi;

  // tampilkan admin box kalau note ada
  if (adminNoteWrap) adminNoteWrap.hidden = note.length === 0;

  // isi catatan admin text (biar langsung berubah)
  if (adminTextEl) {
    adminTextEl.textContent = note;
    adminTextEl.title = note;
  }

  // update tanggal admin -> format TANGGAL doang (bukan "baru saja")
  if (adminDateEl) {
    const raw = doc?.updated_at || doc?.created_at || new Date().toISOString();
    adminDateEl.textContent = formatDateID(raw);
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

  // summary harus jadi "Detail Revisi (0)"
  const summary = docWrap.querySelector(".incoming-summary");
  if (summary) summary.textContent = "Detail Revisi (0)";

  // =========================
  // KUNCI: update ROW ADMIN di incoming-table (div-based)
  // (bukan bikin <tr>)
  // =========================
  const table = docWrap.querySelector(".incoming-table");
  if (table) {
    // hapus row admin lama dari JS (kalau ada)
    table.querySelectorAll("[data-js-admin-row]").forEach((el) => el.remove());

    if (showRevisi) {
      const adminRow = document.createElement("div");
      adminRow.className = "incoming-row";
      adminRow.setAttribute("data-js-admin-row", "1");
      adminRow.innerHTML = `
        <div class="incoming-cell">
          <div class="truncate-1" title="${note !== "" ? note.replaceAll('"', "&quot;") : "-"}">
            ${note !== "" ? note : "-"}
          </div>
        </div>

        <div class="incoming-cell muted">
          Pemohon belum upload file revisi.
        </div>

        <div class="incoming-cell muted">-</div>
      `;

      const head = table.querySelector(".incoming-head");
      if (head) head.insertAdjacentElement("afterend", adminRow);
      else table.prepend(adminRow);
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
  document.addEventListener(
    "click",
    (e) => {
      const btn = e.target.closest("[data-rev-btn]");
      const popInside = e.target.closest("[data-rev-pop]");
      const wrapRev = e.target.closest("[data-rev]");

      // klik tombol revisi
      if (btn && wrapRev) {
        e.preventDefault();
        e.stopPropagation();

        const myPop = wrapRev.querySelector("[data-rev-pop]");
        if (!myPop) return;

        // tutup popup lain
        document.querySelectorAll("[data-rev-pop]").forEach((p) => {
          if (p !== myPop) p.hidden = true;
        });

        myPop.hidden = !myPop.hidden;
        return;
      }

      // klik di dalam pop -> jangan nutup
      if (popInside) return;

      // klik di luar -> tutup semua
      document
        .querySelectorAll("[data-rev-pop]")
        .forEach((p) => (p.hidden = true));
    },
    true
  );

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
        credentials: "same-origin",
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

        // ✅ TAMBAHAN: langsung update box revisi tanpa reload
        updateRevisiUI(docKey, data?.doc);
      }

      // tutup popover revisi
      const pop = form.closest("[data-rev-pop]");
      if (pop) pop.hidden = true;

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
      const url = btnApprove.dataset.url;
      if (!url) return;

      const oldText = btnApprove.textContent;
      btnApprove.disabled = true;
      btnApprove.textContent = "Menyimpan...";

      try {
        const res = await fetch(url, {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "X-CSRF-TOKEN": csrf,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
          },
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok)
          throw new Error(data?.message || `Gagal approve (${res.status})`);

        const statusEl = document.getElementById("statusPengajuanBadge");
        const newStatus = data?.status || "approve";
        if (statusEl) {
          statusEl.textContent = String(newStatus).toUpperCase();
          statusEl.className = `status-badge s-${String(newStatus).toLowerCase()}`;
        }

        const waLinks = data?.wa_links || (data?.wa_link ? [data.wa_link] : []);
        document.getElementById("waPayload")?.remove();


        if (window.Swal) {
          if (waLinks.length) {
            const listHtml = waLinks
              .map(
                (l, i) =>
                  `<div style="margin:6px 0;"><a href="${l}" target="_blank">Kirim ke nomor #${i + 1}</a></div>`
              )
              .join("");

            await Swal.fire({
              icon: "success",
              title: "Approved",
              html: `Status berhasil diubah.<br><br>${listHtml}`,
              confirmButtonText: "OK",
            });
          } else {
            await Swal.fire({
              icon: "success",
              title: "Approved",
              text: data?.message || "Status berhasil diubah.",
            });
          }
        } else {
          alert(data?.message || "Approved");
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
        btnApprove.disabled = false;
        btnApprove.textContent = oldText;
      }
    });
  }

  // =========================
// ✅ SIMPAN & KIRIM (AJAX) - BIAR WA LIST MULTI & TANPA POPUP GLOBAL
// =========================
// =========================
// ✅ SIMPAN & KIRIM (AJAX) - BIAR WA LIST MULTI & TANPA POPUP GLOBAL
// =========================
const sendRevisiForm = document.getElementById("sendRevisiForm");
const btnSendRevisi = document.getElementById("btnSendRevisi");

if (sendRevisiForm && btnSendRevisi) {
  sendRevisiForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    // ✅ WAJIB: stop submit ini biar gak kena handler lain (biar gak dobel Swal)
    e.stopPropagation();


    const url = sendRevisiForm.getAttribute("action");
    if (!url) return;

    const oldText = btnSendRevisi.textContent;
    btnSendRevisi.disabled = true;
    btnSendRevisi.textContent = "Menyimpan...";

    try {
      const fd = new FormData(sendRevisiForm);

      const res = await fetch(url, {
        method: "POST",
        credentials: "same-origin",
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

      const waLinks = data?.wa_links || (data?.wa_link ? [data.wa_link] : []);
      const waLabel = data?.wa_label || "Kirim WA";

      // ✅ BONUS: bunuh trigger swal lama dari awal
      document.getElementById("waPayload")?.remove();

      if (window.Swal) {
        if (waLinks.length <= 1) {
          const waLink = waLinks[0] || null;

          await Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: data?.message || "Revisi berhasil dikirim.",
            showCancelButton: true,
            confirmButtonText: waLabel,
            cancelButtonText: "Tutup",
          }).then((r) => {
            if (r.isConfirmed && waLink) window.open(waLink, "_blank");
          });
        } else {
          const listHtml = waLinks
            .map(
              (l, i) =>
                `<div style="margin:6px 0;">
                  <a href="${l}" target="_blank">
                    Kirim ke nomor #${i + 1}
                  </a>
                </div>`
            )
            .join("");

          await Swal.fire({
            icon: "success",
            title: "Berhasil",
            html: `
              ${data?.message || "Revisi berhasil dikirim."}
              <br><br>
              Pilih nomor tujuan WhatsApp:
              <br><br>
              ${listHtml}
            `,
            confirmButtonText: "OK",
          });
        }
      } else {
        alert(data?.message || "Revisi berhasil dikirim.");
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
      btnSendRevisi.disabled = false;
      btnSendRevisi.textContent = oldText;
    }
  });
}

  refreshCanSend();
  
});

