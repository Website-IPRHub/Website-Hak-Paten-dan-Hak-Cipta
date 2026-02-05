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
  setupTableSearch('searchRevisi', 'revisiTable');
  
  setupPager('revisiTable', 'searchRevisi', 'revisi');
  setupPager('patenTable', 'searchPaten', 'paten');
  setupPager('ciptaTable', 'searchCipta', 'cipta');
  setupPager('statusTable', 'searchStatus', 'status'); // status pakai filter + search

  setupRevisiPopup();
  
  setupDetailDrawer();
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
  if (!chartDataEl) return;

  let Chart;
  try {
    const mod = await import('chart.js/auto');
    Chart = mod.default;
  } catch (err) {
    console.error('Chart.js gagal diload:', err);
    return;
  }

  let payload = {};
  try {
    payload = JSON.parse(chartDataEl.textContent || '{}');
  } catch (e) {
    console.error('chart-data JSON invalid', e);
    return;
  }

  const THEME = {
  paten: '#3361AC', // blue utama
  cipta: '#E8AF30', // orange/gold buat series 2
  gold:  '#E8C766', // soft accent
  navy:  '#0F2043', // dark untuk axis/text
  grid:  'rgba(15,32,67,0.10)',
};




const countTooltip = {
  filter(ctx) {
    let v = 0;

    // pie/doughnut
    if (typeof ctx.parsed === 'number') v = ctx.parsed;

    // bar charts
    else if (ctx.parsed && typeof ctx.parsed === 'object') {
      const indexAxis = ctx.chart?.options?.indexAxis || 'x'; // 'x' = vertical bar, 'y' = horizontal bar
      v = (indexAxis === 'y')
        ? (typeof ctx.parsed.x === 'number' ? ctx.parsed.x : 0)   // horizontal: value ada di x
        : (typeof ctx.parsed.y === 'number' ? ctx.parsed.y : 0);  // vertical: value ada di y
    }

    return v !== 0;
  },

  callbacks: {
    label(ctx) {
      let v = 0;

      if (typeof ctx.parsed === 'number') v = ctx.parsed;
      else if (ctx.parsed && typeof ctx.parsed === 'object') {
        const indexAxis = ctx.chart?.options?.indexAxis || 'x';
        v = (indexAxis === 'y')
          ? (typeof ctx.parsed.x === 'number' ? ctx.parsed.x : 0)
          : (typeof ctx.parsed.y === 'number' ? ctx.parsed.y : 0);
      }

      const name = ctx.dataset?.label ? `${ctx.dataset.label}: ` : '';
      return `${name}${v}`;
    }
  }
};

  // helper: destroy chart lama kalau function dipanggil ulang
  const CHARTS = window.__DASH_CHARTS__ || (window.__DASH_CHARTS__ = {});
  const mount = (key, el, config) => {
    if (!el) return;
    if (CHARTS[key]) CHARTS[key].destroy();
    CHARTS[key] = new Chart(el, config);
  };

  /* =========================
     BAR JENIS PATEN
  ========================= */
  const elPaten = document.getElementById('chartPaten');
  if (elPaten && (payload.patenLabels || []).length) {
    mount('paten', elPaten, {
      type: 'bar',
      data: {
        labels: payload.patenLabels || [],
        datasets: [{
          label: 'Jumlah',
          data: payload.patenData || [],
          backgroundColor: THEME.paten,
          borderRadius: 10,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,

       interaction: {
          mode: 'nearest',
          intersect: false,
          axis: 'x', // ✅ karena bar vertikal
        },

        plugins: {
          legend: { display: false },
          tooltip: { ...countTooltip, position: 'nearest' },
        },

        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: THEME.grid } },
          x: { grid: { display: false } }
        },
      }
    });
  }

  /* =========================
     BAR JENIS CIPTA
  ========================= */
  const elCipta = document.getElementById('chartCipta');
  if (elCipta && (payload.ciptaLabels || []).length) {
    mount('cipta', elCipta, {
      type: 'bar',
      data: {
        labels: payload.ciptaLabels || [],
        datasets: [{
          label: 'Jumlah',
          data: payload.ciptaData || [],
          backgroundColor: THEME.cipta,
          borderRadius: 10,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,

        interaction: {
          mode: 'nearest',
          intersect: false,
          axis: 'x', // ✅ karena bar vertikal
        },

        plugins: {
          legend: { display: false },
          tooltip: { ...countTooltip, position: 'nearest' },
        },

        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: THEME.grid } },
          x: { grid: { display: false } }
        },
      }
    });
  }

  /* =========================
     DOUGHNUT: TOTAL HKI (Mahasiswa vs Dosen)
  ========================= */
  const cRoleAll = document.getElementById('chartRoleAll');
  if (cRoleAll && payload.roleAll && (payload.roleAll.data || []).length) {
    mount('roleAll', cRoleAll, {
      type: 'doughnut',
      data: {
        labels: payload.roleAll.labels || ['Mahasiswa', 'Dosen'],
        datasets: [{
          label: 'Total',
          data: payload.roleAll.data || [0, 0],
          backgroundColor: [THEME.paten, THEME.gold], // ✅ beda jelas
          borderColor: '#fff',
          borderWidth: 2,
          hoverOffset: 6,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: countTooltip,
          legend: { position: 'bottom' },
        },
        cutout: '62%',
      },
    });
  }

  /* =========================
     STACKED BAR: PATEN vs CIPTA (Mahasiswa & Dosen)
  ========================= */
  const cRoleByType = document.getElementById('chartRoleByType');
  if (cRoleByType && payload.roleByType) {
    mount('roleByType', cRoleByType, {
      type: 'bar',
      data: {
        labels: payload.roleByType.labels || ['Paten', 'Hak Cipta'],
        datasets: [
          {
            label: 'Mahasiswa',
            data: payload.roleByType.mahasiswa || [0, 0],
            backgroundColor: THEME.paten,
            borderRadius: 10,
          },
          {
            label: 'Dosen',
            data: payload.roleByType.dosen || [0, 0],
            backgroundColor: THEME.gold,
            borderRadius: 10,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,

        interaction: {
          mode: 'nearest',
          intersect: false,
          axis: 'x', // ✅ karena bar vertikal
        },

        scales: {
          x: { stacked: true, grid: { color: THEME.grid } },
          y: { stacked: true, beginAtZero: true, ticks: { precision: 0 }, grid: { color: THEME.grid } },
        },

        plugins: {
          tooltip: { ...countTooltip, position: 'nearest' },
          legend: { position: 'bottom' },
        },
      }
    });
  }

  /* =========================
     HORIZONTAL BAR: TOP FAKULTAS (dibeda-bedain kebawah)
  ========================= */
  /* =========================
   HORIZONTAL BAR: TOP FAKULTAS (3 WARNA SAJA)
========================= */
  const cFak = document.getElementById('chartFakultas');
  if (cFak && payload.fakultas && (payload.fakultas.labels || []).length) {
    mount('fakultas', cFak, {
      type: 'bar',
      data: {
        labels: payload.fakultas.labels || [],
        datasets: [
          {
            label: 'Total HKI',
            data: payload.fakultas.all || [],
            backgroundColor: THEME.gold,     // ✅ 1 warna
            borderRadius: 8,
            barThickness: 10,
          },
          {
            label: 'Paten',
            data: payload.fakultas.paten || [],
            backgroundColor: THEME.paten,    // ✅ 1 warna
            borderRadius: 8,
            barThickness: 10,
          },
          {
            label: 'Hak Cipta',
            data: payload.fakultas.cipta || [],
            backgroundColor: THEME.cipta,    // ✅ 1 warna
            borderRadius: 8,
            barThickness: 10,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',

         interaction: {
            mode: 'nearest',
            intersect: false,
            axis: 'y',
          },

        // ✅ jangan stacked biar 3 bar muncul berdampingan
        scales: {
          x: {
            stacked: false,
            beginAtZero: true,
            ticks: { precision: 0 },
            grid: { color: THEME.grid },
          },
          y: {
            stacked: false,
            ticks: { autoSkip: false },
            grid: { display: false },
          },
        },

        plugins: {
          tooltip: countTooltip,
          legend: { position: 'bottom' },
        },

        // ✅ biar 3 bar per fakultas kebaca jelas
        datasets: {
          bar: {
            categoryPercentage: 0.7,
            barPercentage: 0.9,
          },
        },
      },
    });
  }
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
    const typeVal = (filterType.value || 'all').toLowerCase();
    const q = (searchInput.value || '').trim().toLowerCase();
    const isShortNumber = /^\d{1,2}$/.test(q);

    rows.forEach((tr) => {
      const t = (tr.dataset.type || '').toLowerCase();
      const key = (tr.dataset.key || '').toLowerCase();
      const nop = (tr.dataset.nop || '').toLowerCase();

      const okType = typeVal === 'all' || t === typeVal;
      const okQuery = !q || (isShortNumber ? nop.includes(q) : key.includes(q));

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

    if (!q) {
      rows.forEach(tr => tr.style.display = '');
      return;
    }

    // ✅ aturan global: angka pendek = fokus no pendaftaran
    const isShortNumber = /^\d{1,2}$/.test(q);

    rows.forEach(tr => {
      const key = (tr.dataset.key || '').toLowerCase();
      const nop = (tr.dataset.nop || '').toLowerCase();

      const ok = isShortNumber ? nop.includes(q) : key.includes(q);
      tr.style.display = ok ? '' : 'none';
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

function setupDetailDrawer() {
  const drawer = document.getElementById('detailDrawer');
  const backdrop = document.getElementById('detailBackdrop');
  const closeBtn = document.getElementById('closeDetail');
  const titleEl = document.getElementById('detailTitle');
  const subEl = document.getElementById('detailSub');
  const bodyEl = document.getElementById('detailBody');

  if (!drawer || !backdrop || !closeBtn || !bodyEl) return;

  function safeParseInventors(raw) {
    if (!raw) return [];
    try {
      const val = JSON.parse(raw);
      return Array.isArray(val) ? val : [];
    } catch (e) {
      return [];
    }
  }

  function renderInventors(inventors) {
    if (!Array.isArray(inventors) || inventors.length === 0) return '';

    return `
      <div class="detail-section">
        <div style="font-weight:800; margin:10px 0 6px;">Data Inventor</div>

        ${inventors.map((inv, idx) => `
          <div style="border:1px solid #e6ebf5;border-radius:12px;padding:10px;margin-bottom:10px;background:#fff;">
            <div style="font-weight:800;color:#0b2c5f;">
              ${idx + 1}. ${(inv.nama ?? '-')} <span style="font-weight:600;">(${inv.status ?? '-'})</span>
            </div>

            <div style="margin-top:6px;">
              <div class="detail-row">
                <div class="detail-k">NIP/NIM</div>
                <div class="detail-v">${inv.nip_nim ?? '-'}</div>
              </div>
              <div class="detail-row">
                <div class="detail-k">Fakultas</div>
                <div class="detail-v">${inv.fakultas ?? '-'}</div>
              </div>
              <div class="detail-row">
                <div class="detail-k">Email</div>
                <div class="detail-v">${inv.email ?? '-'}</div>
              </div>
              <div class="detail-row">
                <div class="detail-k">No HP</div>
                <div class="detail-v">${inv.no_hp ?? '-'}</div>
              </div>
            </div>
          </div>
        `).join('')}
      </div>
    `;
  }

  function openDrawer(payload, type) {
    if (titleEl) titleEl.textContent = type === 'paten' ? 'Detail Paten' : 'Detail Hak Cipta';
    if (subEl) subEl.textContent = `${payload.no_pendaftaran || '-'} • ${payload.judul || '-'}`;

    const fields = [
      ['No Pendaftaran', payload.no_pendaftaran],
      ['Judul', payload.judul],
      ['Jenis', payload.jenis],
      ...(type === 'cipta' && payload.jenis_lainnya && String(payload.jenis_lainnya).trim() !== '-'? [['Jenis Lainnya', payload.jenis_lainnya]]: []),

      // ⬇️ ini data umum pengajuan (tetap tampil)
      ...(type === 'paten' ? [['Prototipe', payload.prototipe]] : []),
      ['Nilai Perolehan', payload.nilai_perolehan],
      ['Sumber Dana', payload.sumber_dana],
      ['Skema Penelitian', payload.skema_penelitian],
    ];

    const inventorHtml = renderInventors(payload.inventors || []);

    // fallback kalau paten lama belum punya inventors array
    bodyEl.innerHTML = `
      ${fields.map(([k, v]) => `
        <div class="detail-row">
          <div class="detail-k">${k}</div>
          <div class="detail-v">${(v ?? '-')}</div>
        </div>
      `).join('')}

      ${inventorHtml}
    `;

    backdrop.hidden = false;
    drawer.hidden = false;
    drawer.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    backdrop.hidden = true;
    drawer.hidden = true;
    drawer.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;

    const type = (btn.dataset.detailType || '').toLowerCase();

    let inventors = safeParseInventors(btn.dataset.inventors);

    // ✅ kalau inventors kosong (cipta/paten lama), bikin 1 item inventor dari data single
    if (!inventors || inventors.length === 0) {
      inventors = [{
        nama: btn.dataset.nama || '-',
        status: btn.dataset.statusInventor || btn.dataset.role || '-', // kalau ga ada ya '-'
        nip_nim: btn.dataset.nip || '-',
        fakultas: btn.dataset.fakultas || '-',
        email: btn.dataset.email || '-',
        no_hp: btn.dataset.hp || '-',
      }];
    }

    let judul = btn.dataset.judul;
    try { judul = JSON.parse(judul); } catch(e) {}

    const payload = {
      no_pendaftaran: btn.dataset.no,
      judul: judul,
      jenis: btn.dataset.jenis,
      jenis_lainnya: btn.dataset.jenisLainnya,

      prototipe: btn.dataset.prototipe,
      nilai_perolehan: btn.dataset.nilai,
      sumber_dana: btn.dataset.sumber,
      skema_penelitian: btn.dataset.skema,

      // ✅ sekarang selalu ada inventors (minimal 1)
      inventors: inventors,
    };


    openDrawer(payload, type);
  });

  closeBtn.addEventListener('click', closeDrawer);
  backdrop.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !drawer.hidden) closeDrawer();
  });
}



// resources/js/admin/dashboard.js

function csrfToken() {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
}

function toast(msg, type = 'success') {
  // simpel: pakai alert dulu biar cepat jalan
  // kalau kamu punya toast UI sendiri, ganti fungsi ini
  console.log(`[${type}]`, msg);
}

async function postFormJson(actionUrl, formData) {
  const res = await fetch(actionUrl, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken(),
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest', // ✅ penting biar expectsJson() true
    },
    body: formData
  });

  let json = null;
  try { json = await res.json(); } catch (e) {}

  if (!res.ok) {
    const msg = (json && json.message) ? json.message : `Request gagal (${res.status})`;
    throw new Error(msg);
  }
  return json;
}

function setBadge(badgeEl, status) {
  if (!badgeEl) return;
  // hapus class badge-xxx lama
  badgeEl.className = badgeEl.className
    .split(' ')
    .filter(c => !c.startsWith('badge-'))
    .join(' ')
    .trim();

  badgeEl.classList.add(`badge-${status}`);
  badgeEl.textContent = String(status).toUpperCase();
}

function toggleSendRevisiForm(rowEl, hasRevisi) {
  if (!rowEl) return;
  const f = rowEl.querySelector('[data-send-revisi]');
  if (!f) return;
  f.hidden = !hasRevisi;
}


// ============================
// 1) AJAX untuk verifikasi dokumen (OK / REVISI) - paten & cipta
// ============================
document.addEventListener('submit', async (e) => {
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;

  if (form.classList.contains('js-doc-form')) {
    e.preventDefault();

    const td = form.closest('td');
    const tr = form.closest('tr');
    const actionUrl = form.getAttribute('action');
    const fd = new FormData(form);

    // 🔥 loading state
    setFormLoading(form, true, 'Menyimpan...');

    try {
      const json = await postFormJson(actionUrl, fd);

      if (!json || !json.ok) throw new Error(json?.message || 'Gagal simpan');

      const docKey = json.doc?.doc_key;
      const status = json.doc?.status || 'pending';

      if (td && docKey) {
        const badge = td.querySelector(`[data-doc-badge][data-doc-key="${docKey}"]`);
        setBadge(badge, status);
      }

      if (tr) toggleSendRevisiForm(tr, !!json.has_revisi);

      toast(json.message || 'Tersimpan');

    } catch (err) {
      toast(err.message || 'Error', 'error');
      alert(err.message || 'Terjadi error');
    } finally {
      // ✅ balikin tombol normal walau error
      setFormLoading(form, false);
    }
  }
});

function setFormLoading(form, loading, labelWhenLoading = 'Menyimpan...') {
  const submitBtn = form.querySelector('button[type="submit"]');
  const ddBtn = form.querySelector('[data-dd-btn]'); // tombol dropdown status

  if (submitBtn && !submitBtn.dataset.originalText) {
    submitBtn.dataset.originalText = submitBtn.textContent.trim();
  }

  if (loading) {
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = labelWhenLoading;
    }
    if (ddBtn) ddBtn.disabled = true; // dropdown dimatiin sementara
    form.classList.add('is-loading');  // opsional styling
  } else {
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.textContent = submitBtn.dataset.originalText || 'Simpan';
    }
    if (ddBtn) ddBtn.disabled = false; // dropdown balik normal
    form.classList.remove('is-loading');
  }
}

// ============================
// 2) AJAX untuk "Kirim Permintaan Revisi" (paten & cipta)
// ============================
document.addEventListener('submit', async (e) => {
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;

  if (form.classList.contains('js-send-revisi-form')) {
    e.preventDefault();

    const actionUrl = form.getAttribute('action');
    const fd = new FormData(form);
    const msgEl = form.querySelector('[data-inline-msg]');

    setFormLoading(form, true, 'Mengirim...');

    try {
      const json = await postFormJson(actionUrl, fd);
      if (!json || !json.ok) throw new Error(json?.message || 'Gagal kirim revisi');

      if (msgEl) {
        msgEl.textContent = json.message || 'Terkirim ✅';
        msgEl.style.color = 'green';
        setTimeout(() => { msgEl.textContent = ''; }, 2500);
      }

      if (json.wa_link) {
        if (window.Swal) {
          Swal.fire({
            title: 'Berhasil',
            text: json.message || 'Revisi terkirim.',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Kirim WA',
            cancelButtonText: 'Tutup'
          }).then((r) => {
            if (r.isConfirmed) window.open(json.wa_link, '_blank');
          });
        } else {
          // fallback kalau sweetalert belum di-include
          const ok = confirm('Revisi terkirim. Kirim WA sekarang?');
          if (ok) window.open(json.wa_link, '_blank');
        }
      }

    } catch (err) {
      if (msgEl) {
        msgEl.textContent = err.message || 'Terjadi error';
        msgEl.style.color = 'red';
      }
    } finally {
      setFormLoading(form, false);
    }
  }
});


// ============================
// 3) AJAX untuk update status (TAB STATUS)
// ============================
document.addEventListener('submit', async (e) => {
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;

  if (form.classList.contains('js-status-form')) {
    e.preventDefault();

    const actionUrl = form.getAttribute('action');
    const tr = form.closest('tr');
    const fd = new FormData(form);

    // Laravel spoof PUT
    if (!fd.get('_method')) fd.append('_method', 'PUT');

    // ✅ loading state
    setFormLoading(form, true, 'Menyimpan...');

    const msgEl = form.querySelector('[data-inline-msg]');
    if (msgEl) {
      msgEl.textContent = 'Menyimpan perubahan...';
      msgEl.style.color = '#444';
    }

    try {
      const json = await postFormJson(actionUrl, fd);
      if (!json || !json.ok) throw new Error(json?.message || 'Gagal update status');

      // update label dropdown
      const hiddenInput = form.querySelector('input[name="status"]');
      const ddLabel = form.querySelector('[data-dd-label]');
      const newStatus = json.data?.status || hiddenInput?.value;
      if (ddLabel && newStatus) ddLabel.textContent = newStatus;

      // inject tombol WA kalau ada
      if (tr) {
        const slot = tr.querySelector('[data-wa-slot]');
        if (slot) {
          slot.innerHTML = '';
          if (json.wa_link) {
            const a = document.createElement('a');
            a.href = json.wa_link;
            a.target = '_blank';
            a.className = 'btn-mini';
            a.textContent = 'Kirim WA';
            slot.appendChild(a);
          }
        }
      }

      // ✅ sukses message
      if (msgEl) {
        msgEl.textContent = json.message || 'Tersimpan ✅';
        msgEl.style.color = 'green';

        // auto hilang 2 detik
        setTimeout(() => { msgEl.textContent = ''; }, 2000);
      }

    } catch (err) {
      if (msgEl) {
        msgEl.textContent = err.message || 'Terjadi error';
        msgEl.style.color = 'red';
      }
    } finally {
      setFormLoading(form, false);
    }
  }
});

function setupPager(tableId, searchInputId, pagerKey) {
  const table = document.getElementById(tableId);
  const footer = document.querySelector(`.table-footer[data-pager="${pagerKey}"]`);
  if (!table || !footer) return;

  const tbody = table.querySelector('tbody');
  if (!tbody) return;

  const infoEl = footer.querySelector('[data-info]');
  const entriesEl = footer.querySelector('[data-entries]');
  const paginationEl = footer.querySelector('[data-pagination]');

  const allRows = Array.from(tbody.querySelectorAll('tr'));

  // simpan pilihan entries per tab
  const lsKey = `entriesPerPage:${pagerKey}`;
  let perPage = parseInt(localStorage.getItem(lsKey) || (entriesEl?.value || '20'), 10);
  if (entriesEl) entriesEl.value = String(perPage);

  let currentPage = 1;

  const getVisibleRows = () => {
    // ambil row yang sedang tampil (tidak display:none)
    return allRows.filter(tr => tr.style.display !== 'none');
  };

  const render = () => {
    const visible = getVisibleRows();
    const total = visible.length;

    const totalPages = Math.max(1, Math.ceil(total / perPage));
    if (currentPage > totalPages) currentPage = totalPages;

    // hide semua dulu
    allRows.forEach(r => (r.hidden = true));

    const start = (currentPage - 1) * perPage;
    const end = Math.min(start + perPage, total);

    for (let i = start; i < end; i++) {
      visible[i].hidden = false;
    }

    // info text
    const showingFrom = total === 0 ? 0 : start + 1;
    const showingTo = end;
    if (infoEl) infoEl.textContent = `Showing ${showingFrom} to ${showingTo} of ${total} entries`;

    // pagination buttons
    if (!paginationEl) return;
    paginationEl.innerHTML = '';

    const mkBtn = (label, onClick, opts = {}) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'page-btn' + (opts.active ? ' active' : '');
      b.textContent = label;
      if (opts.disabled) b.disabled = true;
      b.addEventListener('click', onClick);
      return b;
    };

    paginationEl.appendChild(
      mkBtn('Prev', () => { currentPage--; render(); }, { disabled: currentPage === 1 })
    );

    for (let p = 1; p <= totalPages; p++) {
      paginationEl.appendChild(
        mkBtn(String(p), () => { currentPage = p; render(); }, { active: p === currentPage })
      );
    }

    paginationEl.appendChild(
      mkBtn('Next', () => { currentPage++; render(); }, { disabled: currentPage === totalPages })
    );
  };

  // entries change
  if (entriesEl) {
    entriesEl.addEventListener('change', () => {
      perPage = parseInt(entriesEl.value, 10);
      localStorage.setItem(lsKey, String(perPage));
      currentPage = 1;
      render();
    });
  }

  // hook ke search input: tiap search berubah -> reset page dan render ulang
  const searchInput = document.getElementById(searchInputId);
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      currentPage = 1;
      // tunggu filter/search kamu jalan dulu
      requestAnimationFrame(render);
    });
  }

  // hook tambahan untuk status filter dropdown (karena status pakai filterType)
  if (pagerKey === 'status') {
    const filterType = document.getElementById('filterType');
    if (filterType) {
      filterType.addEventListener('change', () => {
        currentPage = 1;
        requestAnimationFrame(render);
      });
    }
  }

  render();
}

function setupRevisiPopup() {
  document.addEventListener('click', (e) => {
    // toggle open
    const btn = e.target.closest('[data-rev-btn]');
    if (btn) {
      e.preventDefault();
      const wrap = btn.closest('[data-rev]');
      const pop = wrap?.querySelector('[data-rev-pop]');
      if (!pop) return;

      // tutup yang lain dulu
      document.querySelectorAll('[data-rev-pop]').forEach(p => {
        if (p !== pop) p.hidden = true;
      });

      pop.hidden = !pop.hidden;
      return;
    }

    // klik di luar -> tutup
    if (!e.target.closest('[data-rev]')) {
      document.querySelectorAll('[data-rev-pop]').forEach(p => p.hidden = true);
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('[data-rev-pop]').forEach(p => p.hidden = true);
    }
  });
}

  function initNotifRevisiButton() {
  const btn = document.getElementById('btnNotifRevisi');
  if (!btn) return;

  btn.addEventListener('click', () => {
    window.location.href = '/admin/dashboard?tab=status';
  });
}