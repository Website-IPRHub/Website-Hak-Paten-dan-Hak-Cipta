document.addEventListener('DOMContentLoaded', () => {
  const wrap = document.getElementById('jenis-lainnya-wrap');
  const inputLainnya = wrap.querySelector('input[name="jenis_hak_cipta_lainnya"]');
  const radios = document.querySelectorAll('input[name="jenis_hak_cipta"]');

  function toggleLainnya() {
    const checked = document.querySelector('input[name="jenis_hak_cipta"]:checked');
    const isLainnya = checked && checked.value === 'Lainnya';

    wrap.style.display = isLainnya ? 'block' : 'none';
    inputLainnya.required = !!isLainnya;

    if (!isLainnya) inputLainnya.value = '';
  }

  radios.forEach(r => r.addEventListener('change', toggleLainnya));
  toggleLainnya(); // initial (buat handle old())
});
