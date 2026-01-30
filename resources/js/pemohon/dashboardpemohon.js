// resources/js/pemohon/dashboardpemohon.js

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

function initLogoutConfirm() {
  const openBtn = document.getElementById('openLogoutPemohon');
  const form = document.getElementById('logoutFormPemohon');
  const modal = document.getElementById('logoutModalPemohon');
  const backdrop = document.getElementById('logoutBackdropPemohon');
  const cancel = document.getElementById('cancelLogoutPemohon');
  const confirm = document.getElementById('confirmLogoutPemohon');

  if (!openBtn || !form || !modal || !backdrop) return;

  openBtn.addEventListener('click', () => {
    modal.hidden = false;
    backdrop.hidden = false;
    document.body.style.overflow = 'hidden';
  });

  const close = () => {
    modal.hidden = true;
    backdrop.hidden = true;
    document.body.style.overflow = '';
  };

  cancel?.addEventListener('click', close);
  backdrop.addEventListener('click', close);

  confirm?.addEventListener('click', () => {
    form.submit(); // ⬅️ INI YANG PAKE ROUTE POST LOGOUT
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

function initRevisiToggle() {
  const btn = document.getElementById('btnRevisi');
  const box = document.getElementById('boxRevisi');
  if (!btn || !box) return;

  btn.addEventListener('click', () => {
    box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initAccountModal();
  initLogoutConfirm();
  initTrackerHighlight();
  initRevisiToggle();
});
