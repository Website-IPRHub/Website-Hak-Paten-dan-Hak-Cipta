// resources/js/pemohon/dashboardpemohon.js

function onReady(fn) {
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
  else fn();
}

function initAccountModal() {
  const openBtn = document.getElementById('openAccount');
  const modal = document.getElementById('paModal');
  const backdrop = document.getElementById('paBackdrop');
  const closeBtn = document.getElementById('closeAccount');
  if (!openBtn || !modal || !backdrop) return;

  const open = () => {
    modal.hidden = false;
    backdrop.hidden = false;
    document.body.style.overflow = 'hidden';
  };

  const close = () => {
    modal.hidden = true;
    backdrop.hidden = true;
    document.body.style.overflow = '';
  };

  openBtn.addEventListener('click', (e) => {
    e.preventDefault();
    open();
  });

  backdrop.addEventListener('click', close);
  closeBtn?.addEventListener('click', close);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.hidden) close();
  });
}

function initTrackerHighlight() {
  const tracker = document.querySelector('.pd-tracker');
  if (!tracker) return;

  const active = tracker.getAttribute('data-active');
  const order = ['terkirim', 'proses', 'revisi', 'approve'];
  const activeIndex = order.indexOf(active);

  document.querySelectorAll('.pd-step').forEach((el) => {
    const key = el.getAttribute('data-step');
    const idx = order.indexOf(key);

    el.classList.remove('is-done', 'is-run', 'is-todo');

    if (activeIndex === -1) return el.classList.add('is-todo');

    if (idx < activeIndex) el.classList.add('is-done');
    else if (idx === activeIndex) el.classList.add(active === 'approve' ? 'is-done' : 'is-run');
    else el.classList.add('is-todo');
  });
}

/**
 * ✅ cek overflow clamp, munculkan tombol Selengkapnya
 * NOTE: hanya valid kalau elemnya sedang terlihat (display != none)
 */
function refreshSelengkapnya(root = document) {
  root.querySelectorAll('.pd-note-cell').forEach((cell) => {
    const clamp = cell.querySelector('.pd-note-clamp');
    const full  = cell.querySelector('.pd-note-full');
    const btn   = cell.querySelector('.jsNoteToggle');
    if (!clamp || !full || !btn) return;

    // kalau lagi mode full, jangan diubah
    const isOpen = !full.hasAttribute('hidden');
    if (isOpen) return;

    // kalau parent display none, ukurannya 0 -> skip dulu
    const rect = clamp.getBoundingClientRect();
    if (rect.width === 0 || rect.height === 0) {
      // tetap hidden, nanti pas visible kita refresh lagi
      btn.hidden = true;
      return;
    }

    const overflow = clamp.scrollHeight > clamp.clientHeight + 1;
    btn.hidden = !overflow;

    // safety: kalau ternyata gak overflow, pastiin full hidden
    if (!overflow) {
      full.setAttribute('hidden', 'hidden');
      clamp.removeAttribute('hidden');
      btn.textContent = 'Selengkapnya';
    }
  });
}

function initPemohonDashboardUI() {
  // ===== modal note elements
  const modal = document.getElementById('pdNoteModal');
  const backdrop = document.getElementById('pdNoteBackdrop');
  const btnClose = document.getElementById('pdNoteClose');
  const titleEl = document.getElementById('pdNoteTitle');
  const bodyEl = document.getElementById('pdNoteBody');

  const openNote = (title, note) => {
    if (!modal || !backdrop || !titleEl || !bodyEl) return;
    titleEl.textContent = title || 'Detail';
    bodyEl.textContent = note || '-';
    modal.hidden = false;
    backdrop.hidden = false;
  };

  const closeNote = () => {
    if (modal) modal.hidden = true;
    if (backdrop) backdrop.hidden = true;
  };

  btnClose?.addEventListener('click', closeNote);
  backdrop?.addEventListener('click', closeNote);

  document.addEventListener(
    'click',
    (e) => {
      // ===== 1) Toggle Detail Dokumen (TERKIRIM)
      const btnDok = e.target.closest('.btnDetailDok');
      if (btnDok) {
        e.preventDefault();
        const wrap = btnDok.closest('.pd-step-body') || btnDok.parentElement;
        const box = wrap?.querySelector('.boxDetailDok');
        if (box) {
          const willOpen = (box.style.display === 'none' || box.style.display === '');
          box.style.display = willOpen ? 'block' : 'none';

          // ✅ setelah terbuka, refresh
          if (willOpen) {
            requestAnimationFrame(() => refreshSelengkapnya(box));
          }
        }
        return;
      }

      // ===== 2) Toggle Detail Revisi
      const btnRev = e.target.closest('.btnRevisi');
      if (btnRev) {
        e.preventDefault();
        const box = document.getElementById('boxRevisi');
        if (box) {
          const willOpen = (box.style.display === 'none' || box.style.display === '');
          box.style.display = willOpen ? 'block' : 'none';

          // ✅ KUNCI: refresh setelah box jadi visible
          if (willOpen) {
            requestAnimationFrame(() => refreshSelengkapnya(box));
          }
        }
        return;
      }

      // ===== 3) Klik "Lihat detail" → buka modal (CATATAN ADMIN)
      const btnOpenNote = e.target.closest('.jsOpenNote');
      if (btnOpenNote) {
        e.preventDefault();
        openNote(btnOpenNote.dataset.title, btnOpenNote.dataset.note);
        return;
      }

      // ===== 4) Toggle Selengkapnya / Tutup (INLINE)
      const btnToggle = e.target.closest('.jsNoteToggle');
      if (btnToggle) {
        e.preventDefault();

        const cell = btnToggle.closest('.pd-note-cell');
        if (!cell) return;

        const clamp = cell.querySelector('.pd-note-clamp');
        const full  = cell.querySelector('.pd-note-full');
        if (!clamp || !full) return;

        const isOpen = !full.hasAttribute('hidden');

        if (isOpen) {
          full.setAttribute('hidden', 'hidden');
          clamp.removeAttribute('hidden');
          btnToggle.textContent = 'Selengkapnya';
       } else {
  full.removeAttribute('hidden');
  clamp.setAttribute('hidden', 'hidden');
  btnToggle.textContent = 'Tutup';
}
        return;
      }
    },
    true
  );

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeNote();
  });

  // ✅ initial refresh setelah semua handler siap
  requestAnimationFrame(() => refreshSelengkapnya(document));
}

function boot() {
  if (document.documentElement.dataset.pemohonDashInit === '1') return;
  document.documentElement.dataset.pemohonDashInit = '1';

  initAccountModal();
  initTrackerHighlight();
  initPemohonDashboardUI();

  // ✅ resize refresh
  window.addEventListener('resize', () => refreshSelengkapnya(document));
}

onReady(boot);