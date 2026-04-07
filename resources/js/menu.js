document.addEventListener('DOMContentLoaded', function () {
  const container = document.querySelector('.menu-steps');
  const active = container?.querySelector('.step.active');
  if (!container || !active) return;

  const padding = 24; 

  const cRect = container.getBoundingClientRect();
  const aRect = active.getBoundingClientRect();

  const leftIn = aRect.left - cRect.left;
  const rightIn = aRect.right - cRect.left;

  if (leftIn < padding) {
    container.scrollBy({ left: leftIn - padding, behavior: 'smooth' });
  }

  else if (rightIn > cRect.width - padding) {
    container.scrollBy({ left: rightIn - (cRect.width - padding), behavior: 'smooth' });
  }
});