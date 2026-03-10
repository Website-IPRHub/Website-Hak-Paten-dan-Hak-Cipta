document.addEventListener("DOMContentLoaded", () => {
  // ===== MODAL GANTI PASSWORD =====
  const openBtn = document.getElementById("openChangePw");
  const modal = document.getElementById("cpModal");
  const backdrop = document.getElementById("cpBackdrop");
  const closeBtn = document.getElementById("closeChangePw");
  const cancelBtn = document.getElementById("cancelChangePw");

  const open = () => {
    if (!modal || !backdrop) return;
    modal.hidden = false;
    backdrop.hidden = false;
    document.body.style.overflow = "hidden";
    const first = modal.querySelector("input");
    if (first) first.focus();
  };

  const close = () => {
    if (!modal || !backdrop) return;
    modal.hidden = true;
    backdrop.hidden = true;
    document.body.style.overflow = "";
  };

  if (openBtn && modal && backdrop) {
    openBtn.addEventListener("click", (e) => { e.preventDefault(); open(); });
    backdrop.addEventListener("click", close);
    closeBtn?.addEventListener("click", close);
    cancelBtn?.addEventListener("click", close);

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !modal.hidden) close();
    });
  }

  const pwInput = document.getElementById("pwInput");
  const toggleBtn = document.getElementById("pwToggle");

  if (pwInput && toggleBtn) {
    toggleBtn.hidden = false;

    toggleBtn.addEventListener("click", () => {
      const shown = pwInput.type === "text";
      pwInput.type = shown ? "password" : "text";

      // toggle icon state
      toggleBtn.classList.toggle("is-shown", !shown);

      pwInput.focus();
    });
  }
});