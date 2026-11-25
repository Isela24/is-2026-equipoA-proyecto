'use strict';

// RITMO: la interfaz obtiene la información desde la API PHP y MariaDB.
const paths = {
  layers: '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
  grid: '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
  checklist: '<rect x="4" y="4" width="16" height="16" rx="3"/><path d="m8 10 2 2 4-4M8 16h8"/>',
  chart: '<path d="M4 20V11m6 9V4m6 16v-7m4 7H2" stroke-linecap="round"/>',
  calendar: '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/>',
  plus: '<path d="M12 5v14M5 12h14" stroke-linecap="round"/>',
  check: '<path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/>',
  checkCircle: '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6" stroke-linecap="round" stroke-linejoin="round"/>',
  flame: '<path d="M12 22c5 0 8-3.6 8-8 0-3.6-2.1-6.3-5-8 .2 3-1 4.2-2 5-1-4-3-6-5-8 0 3-3 6.2-3 11 0 4.4 3 8 7 8z"/>',
  sparkles: '<path d="m12 2 1.6 5.3L19 9l-5.4 1.7L12 16l-1.6-5.3L5 9l5.4-1.7L12 2zM19 16l.7 2.3L22 19l-2.3.7L19 22l-.7-2.3L16 19l2.3-.7L19 16z"/>',
  arrowRight: '<path d="M4 12h16m-6-6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>',
  search: '<circle cx="11" cy="11" r="7"/><path d="m16 16 5 5" stroke-linecap="round"/>',
  x: '<path d="M5 5l14 14M19 5 5 19" stroke-linecap="round"/>',
  menu: '<path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>',
  chevronLeft: '<path d="m14 5-7 7 7 7" stroke-linecap="round" stroke-linejoin="round"/>',
  chevronRight: '<path d="m10 5 7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>',
  activity: '<path d="M2 12h4l3-7 5 14 3-7h5" stroke-linecap="round" stroke-linejoin="round"/>',
  book: '<path d="M12 6c-3-2-6-2-9-1v14c3-1 6-1 9 1 3-2 6-2 9-1V5c-3-1-6-1-9 1zM12 6v14" stroke-linejoin="round"/>',
  droplet: '<path d="M12 2c-3 5-7 9-7 13a7 7 0 0 0 14 0c0-4-4-8-7-13z"/>',
  heart: '<path d="M20.8 8.6c0 4.5-8.8 10.6-8.8 10.6S3.2 13.1 3.2 8.6a4.5 4.5 0 0 1 8.8-1.2 4.5 4.5 0 0 1 8.8 1.2z"/>',
  target: '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
  star: '<path d="m12 2 3.1 6.4 7.1 1-5.1 5 1.2 7-6.3-3.3-6.3 3.3 1.2-7-5.1-5 7.1-1L12 2z"/>'
};

function icon(name) {
  return `<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${paths[name] || paths.star}</svg>`;
}
function paintIcons(scope = document) {
  scope.querySelectorAll('[data-icon]').forEach(el => { el.innerHTML = icon(el.dataset.icon); });
}
const $ = id => document.getElementById(id);
const colors = ['#5d71f5', '#8d63eb', '#31b7d0', '#d777bf', '#f0a05e'];
const categoryDefaults = [
  { icon: 'activity', color: '#536cf3' }, { icon: 'book', color: '#9162e8' },
  { icon: 'heart', color: '#d66faf' }, { icon: 'target', color: '#e7a04e' },
  { icon: 'star', color: '#5aa9d5' }, { icon: 'sparkles', color: '#b86fcf' }
];
const tagSymbols = { 'Mañana': '☀', 'Tarde': '◷', 'Noche': '☾', 'Diario': '▦', 'Prioritario': '★' };

let categories = {};
let categoryCatalog = [];
let tagCatalog = [];
let selectedTags = new Set();
let habits = [];
let csrfToken = '';
let sessionUser = null;
let currentView = 'dashboard';
let filterCategory = 'Todas';
let selectedColor = colors[0];
let editId = null;
let monthCursor = new Date();
let toastTimer;

const pad = value => String(value).padStart(2, '0');
function dateKey(date) { return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`; }
function dayOffset(n) {
  const d = new Date();
  d.setHours(12, 0, 0, 0);
  d.setDate(d.getDate() - n);
  return dateKey(d);
}
function localDate(key) {
  const [y, m, d] = key.split('-').map(Number);
  return new Date(y, m - 1, d, 12);
}
const today = () => dayOffset(0);
function escapeHtml(str) {
  return String(str ?? '').replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[c]));
}

async function request(endpoint, options = {}) {
  const init = {
    credentials: 'same-origin',
    headers: {
      'Accept': 'application/json',
      ...(options.body ? { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken } : {})
    },
    ...options
  };
  let response;
  try {
    response = await fetch('api/' + endpoint, init);
  } catch {
    throw new Error('No se pudo contactar al servidor PHP. Comprueba que Apache o el servidor PHP esté encendido.');
  }
  let payload;
  try {
    payload = await response.json();
  } catch {
    throw new Error('El servidor no devolvió JSON. Abre la página mediante http://localhost, no como archivo local.');
  }
  if (response.status === 401) { window.location.replace('login.php'); throw new Error('Tu sesión ha finalizado.'); }
  if (!response.ok || !payload.success) throw new Error(payload.message || 'Operación no completada.');
  return payload.data;
}

function showToast(message, error = false) {
  $('toast').innerHTML = icon(error ? 'x' : 'checkCircle') + escapeHtml(message);
  $('toast').classList.add('show');
  $('toast').style.background = error ? '#843c62' : '#1c2455';
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => $('toast').classList.remove('show'), 3700);
}

function renderTagOptions() {
  const container = $('habitTags');
  if (!container) return;
  if (!tagCatalog.length) {
    container.innerHTML = '<span class="tag-empty">No hay etiquetas disponibles en la base de datos.</span>';
    return;
  }
  container.innerHTML = tagCatalog.map(tag => {
    const name = String(tag.nombre);
    const checked = selectedTags.has(name);
    const priority = name.toLocaleLowerCase('es') === 'prioritario' ? ' priority' : '';
    const symbol = tagSymbols[name] || '•';
    const safeName = escapeHtml(name);
    return `<label class="tag-option${priority}">
      <input type="checkbox" name="etiquetas[]" value="${safeName}" ${checked ? 'checked' : ''}>
      <span><b aria-hidden="true">${symbol}</b>${safeName}</span>
    </label>`;
  }).join('');
}

async function loadData() {
  try {
    const data = await request('bootstrap.php');
    csrfToken = data.csrf;
    sessionUser = data.usuario || null;
    if (sessionUser) {
      const given = String(sessionUser.nombre || 'Usuario').trim();
      const first = given.split(/\s+/)[0];
      $('sideUserName').textContent = given;
      $('sideAvatar').textContent = first.charAt(0).toUpperCase() || 'U';
      $('topAvatar').textContent = first.charAt(0).toUpperCase() || 'U';
      if (currentView === 'dashboard') $('topTitle').textContent = `¡Qué gusto verte, ${first}!`;
    }
    habits = (data.habitos || []).map(h => ({ ...h, id: Number(h.id), completed: h.completed || [], tags: h.tags || [] }));
    categoryCatalog = data.categorias || [];
    tagCatalog = data.etiquetas || [];

    categories = {};
    categoryCatalog.forEach((c, i) => { categories[c.nombre] = { ...categoryDefaults[i % categoryDefaults.length], id: c.id }; });

    const selectedCategory = $('habitCategory').value;
    $('habitCategory').innerHTML = '<option value="">Sin categoría</option>' + categoryCatalog
      .map(c => `<option value="${Number(c.id)}">${escapeHtml(c.nombre)}</option>`).join('');
    if ([...$('habitCategory').options].some(x => x.value === selectedCategory)) $('habitCategory').value = selectedCategory;

    if (filterCategory !== 'Todas' && !categories[filterCategory]) filterCategory = 'Todas';
    $('dbStatus').textContent = 'MariaDB conectada';
    $('dbStatus').classList.remove('offline');
    $('connectionNotice').hidden = true;
    renderTagOptions();
    refresh();
  } catch (error) {
    $('dbStatus').textContent = 'Sin conexión';
    $('dbStatus').classList.add('offline');
    $('connectionNotice').hidden = false;
    $('connectionNotice').textContent = 'No se pudo cargar la base de datos: ' + error.message + ' Revisa README.md y api/diagnostico.php.';
    habits = [];
    tagCatalog = [];
    renderTagOptions();
    refresh();
    showToast(error.message, true);
  }
}

function completedToday(h) { return h.completed.includes(today()); }
function completedOn(h, key) { return h.completed.includes(key); }
function totalOn(key) { return habits.filter(h => completedOn(h, key)).length; }
function streak(h) {
  const days = new Set(h.completed);
  let d = localDate(today());
  if (!days.has(today())) d.setDate(d.getDate() - 1);
  let count = 0;
  for (let i = 0; i < 3660 && days.has(dateKey(d)); i++) {
    count++;
    d.setDate(d.getDate() - 1);
  }
  return count;
}
function maxStreak() { return habits.length ? Math.max(...habits.map(streak)) : 0; }
function weekDays() { return Array.from({ length: 7 }, (_, i) => dayOffset(6 - i)); }
function weekPercent() {
  const total = habits.length * 7;
  return total ? Math.round(weekDays().reduce((sum, day) => sum + totalOn(day), 0) / total * 100) : 0;
}

function habitMarkup(h) {
  const done = completedToday(h);
  const currentStreak = streak(h);
  const symbol = categories[h.category]?.icon || 'star';
  const col = /^#[0-9a-fA-F]{6}$/.test(h.color) ? h.color : colors[0];
  const tags = (h.tags || []).map(t => `<span class="habit-tag">${tagSymbols[t] || '•'} ${escapeHtml(t)}</span>`).join('');
  return `<div class="habit-row ${done ? 'done' : ''}">
    <div class="habit-symbol" style="background:${col}18;color:${col}">${icon(symbol)}</div>
    <div class="habit-info">
      <div class="habit-name">${escapeHtml(h.name)}</div>
      <div class="habit-meta"><span>${escapeHtml(h.category)}</span><span class="habit-dot"></span><span>${currentStreak} ${currentStreak === 1 ? 'día' : 'días'} de racha</span></div>
      ${tags ? `<div class="habit-tags">${tags}</div>` : ''}
    </div>
    <div class="habit-tools"><button class="habit-tool" data-edit-id="${h.id}" title="Editar hábito">Editar</button>
      <button class="habit-tool delete" data-delete-id="${h.id}" title="Eliminar hábito">×</button></div>
    <button class="habit-check ${done ? 'checked' : ''}" data-toggle-id="${h.id}" aria-label="${done ? 'Marcar como pendiente' : 'Marcar como completado'}: ${escapeHtml(h.name)}" aria-pressed="${done}">${done ? icon('check') : ''}</button>
  </div>`;
}

function renderChart(id) {
  $(id).innerHTML = weekDays().map(day => {
    const amount = totalOn(day);
    const percent = habits.length ? amount / habits.length * 100 : 0;
    const label = new Intl.DateTimeFormat('es-MX', { weekday: 'short' }).format(localDate(day)).replace('.', '');
    return `<div class="bar-group ${day === today() ? 'current' : ''}" title="${amount} de ${habits.length} hábitos completados"><div class="bar-track"><div class="bar-fill" style="height:${percent}%"></div></div><div class="bar-day">${label}</div></div>`;
  }).join('');
}

function renderDashboard() {
  const done = habits.filter(completedToday).length;
  const pct = habits.length ? Math.round(done / habits.length * 100) : 0;
  $('kpiHabits').textContent = habits.length;
  $('kpiDone').textContent = done;
  $('kpiStreak').textContent = maxStreak();
  $('progressPercent').textContent = `${pct}%`;
  $('progressRing').style.setProperty('--pct', `${pct}%`);
  $('progressCaption').textContent = pct === 100 && habits.length ? '¡Hoy lo lograste todo!' : `${done} de ${habits.length} hábitos completados`;
  $('remainingBadge').textContent = `${habits.length - done} pendientes`;
  $('todayHabits').innerHTML = habits.length
    ? [...habits].sort((a, b) => Number(completedToday(a)) - Number(completedToday(b))).map(habitMarkup).join('')
    : `<div class="empty-list">${icon('sparkles')}Aún no tienes hábitos. ¡Crea el primero!</div>`;
  $('weeklyAverage').textContent = `${weekPercent()}%`;
  renderChart('weekBars');
  $('weekDots').innerHTML = weekDays().map(day => {
    const label = new Intl.DateTimeFormat('es-MX', { weekday: 'short' }).format(localDate(day)).replace('.', '');
    return `<div class="week-day-card ${day === today() ? 'current' : ''}"><strong>${label}</strong><div class="weekday-num">${localDate(day).getDate()}</div><span class="activity-dot ${totalOn(day) ? 'lit' : ''}" title="${totalOn(day)} completados"></span></div>`;
  }).join('');
}

function renderHabits() {
  const search = $('habitSearch').value.toLocaleLowerCase('es').trim();
  const filtered = habits.filter(h => (filterCategory === 'Todas' || h.category === filterCategory) && h.name.toLocaleLowerCase('es').includes(search));
  $('allHabits').innerHTML = filtered.length
    ? filtered.map(habitMarkup).join('')
    : `<div class="panel empty-list" style="grid-column:1/-1">${icon('search')}No encontramos hábitos con esos filtros.</div>`;
  $('categoryFilters').innerHTML = ['Todas', ...Object.keys(categories)].map(c => `<button class="filter-chip ${filterCategory === c ? 'active' : ''}" data-filter="${escapeHtml(c)}">${escapeHtml(c)}</button>`).join('');
}

function renderStatistics() {
  $('statTotal').textContent = habits.reduce((n, h) => n + h.completed.length, 0);
  $('statConsistency').textContent = `${weekPercent()}%`;
  $('statStreak').textContent = maxStreak();
  renderChart('statsWeekBars');
  $('categoryStats').innerHTML = Object.entries(categories).map(([name, cat]) => {
    const count = habits.filter(h => h.category === name).length;
    const pc = habits.length ? Math.round(count / habits.length * 100) : 0;
    return `<div><div class="category-top"><strong>${escapeHtml(name)}</strong><span>${count} · ${pc}%</span></div><div class="category-track"><div class="category-fill" style="width:${pc}%;background:${cat.color}"></div></div></div>`;
  }).join('') || '<p class="section-sub">Todavía no tienes categorías.</p>';
}

function renderCalendar() {
  const first = new Date(monthCursor.getFullYear(), monthCursor.getMonth(), 1, 12);
  const days = new Date(first.getFullYear(), first.getMonth() + 1, 0).getDate();
  const shift = (first.getDay() + 6) % 7;
  $('monthTitle').textContent = new Intl.DateTimeFormat('es-MX', { month: 'long', year: 'numeric' }).format(first);
  let html = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'].map(s => `<div class="cal-weekday">${s}</div>`).join('');
  html += Array.from({ length: shift }, () => '<div class="cal-cell blank"></div>').join('');
  for (let n = 1; n <= days; n++) {
    const day = dateKey(new Date(first.getFullYear(), first.getMonth(), n, 12));
    const done = totalOn(day);
    html += `<button class="cal-cell ${day === today() ? 'today' : ''}" data-cal-day="${day}" aria-label="${n}: ${done} hábitos completados"><span class="cal-num">${n}</span><span class="cal-dot-row">${Array.from({ length: Math.min(done, 3) }, () => '<span class="cal-dot"></span>').join('')}</span>${done > 3 ? `<span class="cal-more">+${done - 3}</span>` : ''}</button>`;
  }
  $('calendarGrid').innerHTML = html;
}
function refresh() { renderDashboard(); renderHabits(); renderStatistics(); renderCalendar(); }

const viewTitles = {
  dashboard: ['VISTA GENERAL', '¡Qué gusto verte!'],
  habitos: ['MIS HÁBITOS', 'Tus hábitos'],
  estadisticas: ['ESTADÍSTICAS', 'Tus estadísticas'],
  calendario: ['CALENDARIO', 'Tu calendario']
};
function goTo(view) {
  if (!viewTitles[view]) return;
  currentView = view;
  document.querySelectorAll('.view').forEach(el => el.classList.toggle('active', el.id === `view-${view}`));
  document.querySelectorAll('[data-view]').forEach(el => el.classList.toggle('active', el.dataset.view === view));
  $('breadcrumbView').textContent = viewTitles[view][0];
  $('topTitle').textContent = view === 'dashboard' && sessionUser ? `¡Qué gusto verte, ${String(sessionUser.nombre || 'Usuario').trim().split(/\s+/)[0]}!` : viewTitles[view][1];
  $('sidebar').classList.remove('open');
  $('mobileShade').classList.remove('show');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function drawColors() {
  $('colorPicks').innerHTML = colors.map(c => `<button type="button" class="color-pick ${selectedColor === c ? 'selected' : ''}" data-color="${c}" aria-label="Color ${c}" aria-pressed="${selectedColor === c}" style="background:${c}"></button>`).join('');
}
function openModal(id = null) {
  editId = id;
  selectedTags = new Set();
  $('habitForm').reset();
  selectedColor = colors[0];
  if (id !== null) {
    const h = habits.find(x => x.id === id);
    if (!h) return;
    $('modalTitle').textContent = 'Editar hábito';
    $('habitName').value = h.name;
    $('habitCategory').value = h.categoryId ?? '';
    selectedTags = new Set((h.tags || []).map(String));
    selectedColor = h.color;
  } else {
    $('modalTitle').textContent = 'Crea tu próximo hábito';
  }
  drawColors();
  renderTagOptions();
  $('modalOverlay').classList.add('show');
  $('habitName').focus();
}
function closeModal() {
  $('modalOverlay').classList.remove('show');
  editId = null;
  selectedTags.clear();
  $('habitForm').reset();
  selectedColor = colors[0];
  drawColors();
  renderTagOptions();
}

async function toggleHabit(id) {
  const h = habits.find(x => x.id === id);
  if (!h) return;
  const date = today();
  const completed = h.completed.includes(date);
  try {
    await request('cumplimientos.php', {
      method: completed ? 'DELETE' : 'POST',
      body: JSON.stringify({ habito_id: id, fecha: date })
    });
    await loadData();
    showToast(completed ? 'Marcado como pendiente.' : '¡Un pequeño logro más!');
  } catch (e) { showToast(e.message, true); }
}
async function deleteHabit(id) {
  const h = habits.find(x => x.id === id);
  if (!h) return;
  if (!confirm(`¿Eliminar definitivamente "${h.name}" y sus registros de cumplimiento?`)) return;
  try {
    await request('habitos.php?id=' + id, { method: 'DELETE', body: JSON.stringify({}) });
    await loadData();
    showToast('Hábito eliminado.');
  } catch (e) { showToast(e.message, true); }
}
async function saveHabit(e) {
  e.preventDefault();
  const name = $('habitName').value.trim();
  if (!name) { $('habitName').focus(); return; }
  const btn = e.currentTarget.querySelector('[type="submit"]');
  btn.disabled = true;
  const payload = {
    nombre: name,
    categoria_id: $('habitCategory').value || null,
    color: selectedColor,
    // Enviamos solo nombres que existen en la tabla etiquetas y que el usuario seleccionó.
    etiquetas: [...selectedTags]
  };
  try {
    await request('habitos.php' + (editId !== null ? '?id=' + editId : ''), {
      method: editId !== null ? 'PUT' : 'POST',
      body: JSON.stringify(payload)
    });
    closeModal();
    await loadData();
    goTo('habitos');
    showToast('¡Hábito guardado en MariaDB!');
  } catch (e) {
    showToast(e.message, true);
  } finally {
    btn.disabled = false;
  }
}
async function newCategory() {
  const name = prompt('Nombre de la nueva categoría:');
  if (name === null || !name.trim()) return;
  try {
    const item = await request('categorias.php', { method: 'POST', body: JSON.stringify({ nombre: name.trim() }) });
    await loadData();
    $('habitCategory').value = String(item.id);
    showToast('Categoría disponible.');
  } catch (e) { showToast(e.message, true); }
}

function setup() {
  paintIcons();
  $('dateText').textContent = new Intl.DateTimeFormat('es-MX', { weekday: 'short', day: 'numeric', month: 'short' }).format(new Date()).replace('.', '');
  drawColors();
  renderTagOptions();
  refresh();

  document.addEventListener('click', e => {
    const t = e.target.closest('[data-view],[data-goto],[data-open-modal],[data-toggle-id],[data-edit-id],[data-delete-id],[data-filter],[data-color],[data-cal-day]');
    if (!t) return;
    if (t.dataset.view) goTo(t.dataset.view);
    if (t.dataset.goto) goTo(t.dataset.goto);
    if (t.hasAttribute('data-open-modal')) openModal();
    if (t.dataset.toggleId) toggleHabit(Number(t.dataset.toggleId));
    if (t.dataset.editId) openModal(Number(t.dataset.editId));
    if (t.dataset.deleteId) deleteHabit(Number(t.dataset.deleteId));
    if (t.hasAttribute('data-filter')) { filterCategory = t.dataset.filter; renderHabits(); }
    if (t.dataset.color) { selectedColor = t.dataset.color; drawColors(); }
    if (t.dataset.calDay) showToast(`${totalOn(t.dataset.calDay)} hábitos completados el ${localDate(t.dataset.calDay).toLocaleDateString('es-MX', { day: 'numeric', month: 'long' })}.`);
  });

  $('habitTags').addEventListener('change', e => {
    const checkbox = e.target;
    if (!(checkbox instanceof HTMLInputElement) || checkbox.type !== 'checkbox') return;
    if (checkbox.checked) selectedTags.add(checkbox.value);
    else selectedTags.delete(checkbox.value);
  });
  $('logoutButton').addEventListener('click', async () => {
    $('logoutButton').disabled = true;
    try {
      await request('auth.php', { method: 'POST', body: JSON.stringify({ accion: 'salir' }) });
      window.location.replace('login.php');
    } catch (error) {
      showToast(error.message, true);
      $('logoutButton').disabled = false;
    }
  });
  $('habitSearch').addEventListener('input', renderHabits);
  $('habitForm').addEventListener('submit', saveHabit);
  $('newCategory').addEventListener('click', newCategory);
  $('closeModal').addEventListener('click', closeModal);
  $('cancelModal').addEventListener('click', closeModal);
  $('modalOverlay').addEventListener('click', e => { if (e.target === $('modalOverlay')) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
  $('prevMonth').addEventListener('click', () => { monthCursor.setDate(1); monthCursor.setMonth(monthCursor.getMonth() - 1); renderCalendar(); });
  $('nextMonth').addEventListener('click', () => { monthCursor.setDate(1); monthCursor.setMonth(monthCursor.getMonth() + 1); renderCalendar(); });
  $('menuToggle').addEventListener('click', () => { $('sidebar').classList.add('open'); $('mobileShade').classList.add('show'); });
  $('mobileShade').addEventListener('click', () => { $('sidebar').classList.remove('open'); $('mobileShade').classList.remove('show'); });
  loadData();
}

setup();
