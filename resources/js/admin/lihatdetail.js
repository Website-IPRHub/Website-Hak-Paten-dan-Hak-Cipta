
// resources/js/admin/detail-accordion.js
document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ detail-accordion loaded");

  // wrapper bisa paten atau cipta
  const getWrap = () =>
    document.querySelector("[data-paten-detail]") ||
    document.querySelector("[data-cipta-detail]");

  // =========================
  // ACCORDION
  // =========================
  document.addEventListener(
    "click",
    (e) => {
      const head = e.target.closest("[data-acc-toggle]");
      if (!head) return;

      const wrap = getWrap();
      if (!wrap) {
        console.warn("❌ wrap detail tidak ketemu (data-paten-detail / data-cipta-detail)");
        return;
      }

      e.preventDefault();
      e.stopPropagation();

      const key = head.getAttribute("data-acc-toggle"); // docs/inv
      const body = wrap.querySelector(`[data-acc-body="${key}"]`);
      const card = wrap.querySelector(`[data-acc-card="${key}"]`);

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
    },
    true
  );

  // =========================
  // SWEETALERT WA (SETELAH KIRIM REVISI)
  // =========================
  const waEl = document.getElementById("waPayload");
  if (waEl && window.Swal) {
    const waLink = waEl.dataset.wa;
    const waLabel = waEl.dataset.label || "Kirim WhatsApp";

    Swal.fire({
      icon: "success",
      title: "Revisi terkirim",
      text: "Kirim WhatsApp sekarang?",
      showCancelButton: true,
      confirmButtonText: waLabel,
      cancelButtonText: "Nanti",
    }).then((result) => {
      if (result.isConfirmed && waLink) {
        window.open(waLink, "_blank");
      }
    });
  }
});

  // =========================
  // APPROVE (AJAX + SWEETALERT + LOADING)
  // =========================
  const btnApprove = document.getElementById("btnApprove");
  if (btnApprove && window.Swal) {
    btnApprove.addEventListener("click", async () => {
      const url = btnApprove.dataset.url;
      if (!url) {
        console.warn("❌ Approve URL belum diset di data-url");
        return;
      }

      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

      const confirm = await Swal.fire({
        icon: "question",
        title: "Approve pengajuan?",
        text: "Status akan diubah menjadi APPROVED.",
        showCancelButton: true,
        confirmButtonText: "Ya, Approve",
        cancelButtonText: "Batal",
      });

      if (!confirm.isConfirmed) return;

      // loading state di tombol
      const oldText = btnApprove.textContent;
      btnApprove.disabled = true;
      btnApprove.textContent = "Menyimpan...";

      try {
        const res = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
            "Accept": "application/json",
          },
          body: JSON.stringify({}), // kalau tidak butuh payload, biarkan kosong
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
          // backend bisa kirim {message: "..."} atau {errors: ...}
          throw new Error(data?.message || "Gagal approve.");
        }

        // OPTIONAL: update badge status di UI kalau ada elemen status badge utama
        // misalnya kamu punya:
        // <span id="statusPengajuanBadge" ...>PENDING</span>
        const statusEl = document.getElementById("statusPengajuanBadge");
        if (statusEl && data?.status) {
          statusEl.textContent = String(data.status).toUpperCase();
          // kalau ada class s-pending -> s-approved, sesuaikan:
          statusEl.className = `status-badge s-${String(data.status).toLowerCase()}`;
        }

        // SweetAlert sukses + opsi WA (mirip Simpan & Kirim)
         const waLink = data?.wa_link;
        const waLabel = data?.wa_label || "Kirim WhatsApp";

        if (waLink) {
          const done = await Swal.fire({
            icon: "success",
            title: "Approved",
            text: "Kirim WhatsApp sekarang?",
            showCancelButton: true,
            confirmButtonText: waLabel,
            cancelButtonText: "Nanti",
          });

          if (done.isConfirmed) window.open(waLink, "_blank");
        } else {
          await Swal.fire({
            icon: "success",
            title: "Approved",
            text: data?.message || "Status berhasil diubah.",
          });
        }
      } catch (err) {
        await Swal.fire({
          icon: "error",
          title: "Gagal",
          text: err.message || "Terjadi kesalahan.",
        });
      } finally {
        btnApprove.disabled = false;
        btnApprove.textContent = oldText;
      }
    });
  }
