// resources/js/admin/dashboard.js

document.addEventListener('DOMContentLoaded', () => {
  setupAllCustomDropdowns();
  setupStatusFilter();

  setupUserDropdown();
  setupChangePasswordModal();

  setupLogoutModal();
  setupChartsIfAny();
  setupDeleteModal();

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

/* ========================
   CHARTS (ONLY ON STATS TAB)
========================= */
async function setupChartsIfAny() {
  const chartDataEl = document.getElementById('chart-data');
  if (!chartDataEl) return; // kalau bukan tab stats, skip

  const canvasPaten = document.getElementById('chartPaten');
  const canvasCipta = document.getElementById('chartCipta');
  if (!canvasPaten || !canvasCipta) return;

  let Chart;
  try {
    // ✅ load chart.js hanya saat dibutuhkan
    const mod = await import('chart.js/auto');
    Chart = mod.default;
  } catch (err) {
    console.error('Chart.js gagal diload:', err);
    return; // penting: jangan bikin fitur lain mati
  }

  const payload = JSON.parse(chartDataEl.textContent || '{}');

  const commonOptions = {
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
          precision: 0,
          callback: (value) => Math.round(value),
        }
      }
    },
    responsive: true,
    maintainAspectRatio: false,
  };

  new Chart(canvasPaten, {
    type: 'bar',
    data: {
      labels: payload.patenLabels || [],
      datasets: [{
        data: payload.patenData || [],
        backgroundColor: '#52a0d8ff',
        borderColor: '#0b2c5f',
        borderWidth: 0,
        borderRadius: 10,
        borderSkipped: false
      }],
    },
    options: commonOptions,
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
    options: commonOptions,
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
   - dropdown STATUS per-row + dropdown KATEGORI
========================= */
function setupAllCustomDropdowns() {
  const dropdowns = document.querySelectorAll('[data-dd]');
  if (!dropdowns.length) return;

  const closeAll = (except = null) => {
    dropdowns.forEach(dd => {
      if (except && dd === except) return;
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
      e.preventDefault();
      e.stopPropagation();

      const willOpen = menu.hidden;
      closeAll(dd);
      menu.hidden = !willOpen;
    });

    menu.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      const item = e.target.closest('.dd-item');
      if (!item) return;

      const val = item.dataset.value;
      input.value = val;
      label.textContent = item.textContent.trim();

      menu.querySelectorAll('.dd-item').forEach(x => x.classList.remove('active'));
      item.classList.add('active');

      menu.hidden = true;
      input.dispatchEvent(new Event('change'));
    });
  });

  // ✅ FIX: jangan close kalau klik masih di dalam dropdown manapun
  document.addEventListener('click', (e) => {
    if (e.target.closest('[data-dd]')) return;
    closeAll();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAll();
  });
}

/* =========================
   DELETE MODAL
========================= */
function setupDeleteModal() {
  const modal = document.getElementById('deleteModal');
  const backdrop = document.getElementById('deleteBackdrop');
  const cancelBtn = document.getElementById('cancelDelete');
  const deleteForm = document.getElementById('deleteForm');
  const deleteText = document.getElementById('deleteText');

  if (!modal || !backdrop || !cancelBtn || !deleteForm || !deleteText) return;

  const open = (action, type) => {
    deleteForm.action = action;

    const label = (type === 'cipta') ? 'hak cipta' : 'paten';
    deleteText.innerHTML =
      `Apakah yakin ingin menghapus data <b>${label}</b> ini?<br>
       <span class="modal-warning">Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</span>`;

    modal.hidden = false;
    backdrop.hidden = false;
  };

  const close = () => {
    modal.hidden = true;
    backdrop.hidden = true;
    deleteForm.action = '';
  };

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-delete-action]');
    if (!btn) return;

    e.preventDefault();
    open(btn.dataset.deleteAction, (btn.dataset.deleteType || '').toLowerCase());
  });

  cancelBtn.addEventListener('click', close);
  backdrop.addEventListener('click', close);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
}

/* =========================
   USER DROPDOWN (pojok kanan)
========================= */
function setupUserDropdown() {
  const dd = document.getElementById('userDD');
  const btn = document.getElementById('userBtn');
  const menu = document.getElementById('userMenu');
  if (!dd || !btn || !menu) return;

  const close = () => {
    menu.hidden = true;
    btn.setAttribute('aria-expanded', 'false');
  };

  btn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();

    const willOpen = menu.hidden;
    menu.hidden = !willOpen;
    btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
  });

  // ✅ FIX: klik di luar user dropdown baru nutup
  document.addEventListener('click', (e) => {
    if (e.target.closest('#userDD')) return;
    close();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
}

/* =========================
   CHANGE PASSWORD MODAL
========================= */
function setupChangePasswordModal() {
  const openBtn = document.getElementById('openChangePass');
  const modal = document.getElementById('passModal');
  const backdrop = document.getElementById('passBackdrop');
  const cancelBtn = document.getElementById('cancelPass');

  if (!openBtn || !modal || !backdrop || !cancelBtn) return;

  const open = () => { modal.hidden = false; backdrop.hidden = false; };
  const close = () => { modal.hidden = true; backdrop.hidden = true; };

  openBtn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    open();
  });

  cancelBtn.addEventListener('click', close);
  backdrop.addEventListener('click', close);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
}
