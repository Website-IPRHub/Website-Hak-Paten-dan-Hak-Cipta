// resources/js/admin/dashboard.js
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);


document.addEventListener('DOMContentLoaded', () => {
  setupAllCustomDropdowns();     // buat dropdown status per-row (yang data-dd)
  setupFilterTypeDropdown();     // ✅ khusus dropdown kategori (Semua/Paten/Cipta)
  setupLogoutModal();
  setupChartsIfAny();
  setupStatusFilter();

  setupTableSearch('searchPaten', 'patenTable');
  setupTableSearch('searchCipta', 'ciptaTable');
});

/* =========================
   LOGOUT MODAL
========================= */
function setupLogoutModal() {
  const openBtn = document.getElementById('openLogoutModal');
  const modal = document.getElementById('logoutModal');
  const backdrop = document.getElementById('logoutBackdrop');
  const cancelBtn = document.getElementById('cancelLogout');
  if (!openBtn || !modal || !backdrop || !cancelBtn) return;

  const openModal = () => { modal.hidden = false; backdrop.hidden = false; };
  const closeModal = () => { modal.hidden = true; backdrop.hidden = true; };

  openBtn.addEventListener('click', openModal);
  cancelBtn.addEventListener('click', closeModal);
  backdrop.addEventListener('click', closeModal);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
  });
}

/* =========================
   CHARTS (ONLY ON STATS TAB)
========================= */
function setupChartsIfAny() {
  const chartDataEl = document.getElementById('chart-data');
  if (!chartDataEl) return;

  const canvasPaten = document.getElementById('chartPaten');
  const canvasCipta = document.getElementById('chartCipta');
  if (!canvasPaten || !canvasCipta) return;

  const payload = JSON.parse(chartDataEl.textContent || '{}');

  new Chart(canvasPaten, {
    type: 'bar',
    data: {
      labels: payload.patenLabels || [],
      datasets: [{
        data: payload.patenData || [],
        backgroundColor: '#52a0d8ff',   // warna isi batang
        borderColor: '#0b2c5f',       // warna garis (opsional)
        borderWidth: 0,              // ketebalan garis (opsional)
        borderRadius: 10,
        borderSkipped: false
      }],
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } },
      responsive: true,
      maintainAspectRatio: false,
    },
  });

  new Chart(canvasCipta, {
    type: 'bar',
    data: {
      labels: payload.ciptaLabels || [],
      datasets: [{
        data: payload.ciptaData || [],
        backgroundColor: '#52a0d8ff',
        borderColor: '#0b2c5f',
        borderWidth: 0,
        borderRadius: 10,
        borderSkipped: false
      }],
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } },
      responsive: true,
      maintainAspectRatio: false,
    },
  });
}

/* =========================
   STATUS FILTER (TAB STATUS)
========================= */
function setupStatusFilter() {
  const filterType = document.getElementById('filterType');
  const searchInput = document.getElementById('searchStatus');
  const table = document.getElementById('statusTable');
  if (!filterType || !searchInput || !table) return;

  const rows = Array.from(table.querySelectorAll('tbody tr[data-type]'));

  const apply = () => {
    const typeVal = (filterType.value || 'all').toLowerCase(); // all|paten|cipta
    const q = (searchInput.value || '').trim().toLowerCase();

    rows.forEach((tr) => {
      const t = (tr.dataset.type || '').toLowerCase();
      const key = (tr.dataset.key || '').toLowerCase();

      const okType = typeVal === 'all' || t === typeVal;
      const okQuery = !q || key.includes(q);

      tr.style.display = okType && okQuery ? '' : 'none';
    });
  };

  filterType.addEventListener('change', apply);
  searchInput.addEventListener('input', apply);
  apply();
}

/* =========================
   GENERIC TABLE SEARCH
   (Paten & Cipta)
========================= */
function setupTableSearch(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);
  if (!input || !table) return;

  const rows = Array.from(table.querySelectorAll('tbody tr[data-key]'));

  const apply = () => {
    const q = (input.value || '').trim().toLowerCase();
    rows.forEach((tr) => {
      const key = (tr.dataset.key || '').toLowerCase();
      tr.style.display = (!q || key.includes(q)) ? '' : 'none';
    });
  };

  input.addEventListener('input', apply);
  apply();
}

/* =========================
   CUSTOM DROPDOWNS (data-dd)
   - ini buat dropdown STATUS per row
========================= */
function setupAllCustomDropdowns(){
  const dropdowns = document.querySelectorAll('[data-dd]');
  if (!dropdowns.length) return;

  const closeAll = () => {
    dropdowns.forEach(dd => {
      const menu = dd.querySelector('[data-dd-menu]');
      if (menu) menu.hidden = true;
    });
  };

  dropdowns.forEach(dd => {
    const btn = dd.querySelector('[data-dd-btn]');
    const menu = dd.querySelector('[data-dd-menu]');
    const label = dd.querySelector('[data-dd-label]');
    const input = dd.querySelector('[data-dd-input]');
    if (!btn || !menu || !label || !input) return;

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const willOpen = menu.hidden;
      closeAll();
      menu.hidden = !willOpen;
    });

    menu.addEventListener('click', (e) => {
      const item = e.target.closest('.dd-item');
      if (!item) return;

      const val = item.dataset.value;
      input.value = val;
      label.textContent = item.textContent.trim();

      menu.querySelectorAll('.dd-item').forEach(x => x.classList.remove('active'));
      item.classList.add('active');

      menu.hidden = true;
      input.dispatchEvent(new Event('change')); // biar status filter ke-trigger
    });
  });

  document.addEventListener('click', closeAll);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAll();
  });
}

/* =========================
   FILTER TYPE DROPDOWN
   (Semua / Paten / Hak Cipta)
   - ini yang kamu bilang "ga bisa keluar"
========================= */
function setupFilterTypeDropdown(){
  const dd = document.getElementById('filterTypeDD');
  const btn = document.getElementById('filterTypeBtn');
  const menu = document.getElementById('filterTypeMenu');
  const label = document.getElementById('filterTypeLabel');
  const input = document.getElementById('filterType');

  if (!dd || !btn || !menu || !label || !input) return;

  const close = () => { menu.hidden = true; };

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    menu.hidden = !menu.hidden;
  });

  menu.addEventListener('click', (e) => {
    const item = e.target.closest('.dd-item');
    if (!item) return;

    const val = item.dataset.value;
    input.value = val;
    label.textContent = item.textContent.trim();

    menu.querySelectorAll('.dd-item').forEach(x => x.classList.remove('active'));
    item.classList.add('active');

    close();
    input.dispatchEvent(new Event('change')); // trigger setupStatusFilter()
  });

  document.addEventListener('click', close);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
}
