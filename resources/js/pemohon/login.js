document.addEventListener('DOMContentLoaded', () => {
  const openBtn = document.getElementById('openChangePw');
  const modal = document.getElementById('cpModal');
  const backdrop = document.getElementById('cpBackdrop');
  const closeBtn = document.getElementById('closeChangePw');
  const cancelBtn = document.getElementById('cancelChangePw');

  if (!openBtn || !modal || !backdrop) return;

  const open = () => {
    modal.hidden = false;
    backdrop.hidden = false;
    document.body.style.overflow = 'hidden';

    const first = modal.querySelector('input');
    if (first) first.focus();
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
  cancelBtn?.addEventListener('click', close);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.hidden) close();
  });
});
