// resources/js/pemohon/dashboardpemohon.js

function initPemohonDashboard() {
  const openBtn = document.getElementById('openAccount');
  const modal = document.getElementById('paModal');
  const backdrop = document.getElementById('paBackdrop');
  const closeBtn = document.getElementById('closeAccount');
  const okBtn = document.getElementById('okAccount');

  // kalau bukan halaman dashboard, stop biar ga ganggu halaman lain
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

  openBtn.addEventListener('click', (e) => { e.preventDefault(); open(); });
  backdrop.addEventListener('click', close);
  closeBtn?.addEventListener('click', close);
  okBtn?.addEventListener('click', close);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.hidden) close();
  });

  // ===== tracker highlight =====
  const tracker = document.querySelector('.pd-tracker');
  if (!tracker) return;

  const active = tracker.getAttribute('data-active');
  const order = ['terkirim','diproses','revisi','diterima','ditolak'];
  const activeIndex = order.indexOf(active);

  document.querySelectorAll('.pd-step').forEach((el) => {
    const key = el.getAttribute('data-step');
    const idx = order.indexOf(key);

    el.classList.remove('is-done','is-run','is-todo');

    if (activeIndex === -1) { el.classList.add('is-todo'); return; }

    if (active === 'diterima') {
      if (key === 'ditolak') el.classList.add('is-todo');
      else if (idx < activeIndex) el.classList.add('is-done');
      else if (idx === activeIndex) el.classList.add('is-run');
      else el.classList.add('is-todo');
      return;
    }

    if (idx < activeIndex) el.classList.add('is-done');
    else if (idx === activeIndex) el.classList.add('is-run');
    else el.classList.add('is-todo');
  });
}

// penting: jalankan setelah DOM siap
document.addEventListener('DOMContentLoaded', initPemohonDashboard);
