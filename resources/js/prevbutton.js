document.addEventListener("DOMContentLoaded", () => {
  const btnPrev = document.getElementById("btn-prev");
  if (!btnPrev) return;

  btnPrev.addEventListener("click", (e) => {
    e.preventDefault();

    const fallback = btnPrev.getAttribute("data-fallback") || "/";

    // kalau ada history, back
    if (window.history.length > 1) {
      window.history.back();
      return;
    }

    // kalau nggak ada history, lempar ke fallback route
    window.location.href = fallback;
  });
});
