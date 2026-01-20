document.addEventListener('DOMContentLoaded', function () {
  const container = document.querySelector('.menu-steps');
  const active = container?.querySelector('.step.active');
  if (!container || !active) return;

  const padding = 24; // jarak aman dari kiri/kanan

  const cRect = container.getBoundingClientRect();
  const aRect = active.getBoundingClientRect();

  // posisi active relatif terhadap container (bukan window)
  const leftIn = aRect.left - cRect.left;
  const rightIn = aRect.right - cRect.left;

  // kalau kepotong kiri
  if (leftIn < padding) {
    container.scrollBy({ left: leftIn - padding, behavior: 'smooth' });
  }

  // kalau kepotong kanan
  else if (rightIn > cRect.width - padding) {
    container.scrollBy({ left: rightIn - (cRect.width - padding), behavior: 'smooth' });
  }
});