<?php
require_once __DIR__ . '/api/_session.php';
require_login_page();
header('Cache-Control: no-store');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="theme-color" content="#11163a" />
  <title>RITMO — Gestor de hábitos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/style.css" />
  <link rel="stylesheet" href="assets/tag-picker.css" />
</head>
<body>
  <div class="app">
    <div class="mobile-shade" id="mobileShade"></div>
    <aside class="sidebar" id="sidebar">
      <div class="brand">
        <div class="brand-mark" data-icon="layers"></div>
        <div><div class="brand-name">RITMO.</div><div class="brand-sub">HABIT STUDIO</div></div>
      </div>
      <div class="nav-caption">TU ESPACIO</div>
      <nav class="nav" aria-label="Navegación principal">
        <button class="nav-btn active" data-view="dashboard"><span data-icon="grid"></span><span class="nav-text">Vista general</span></button>
        <button class="nav-btn" data-view="habitos"><span data-icon="checklist"></span><span class="nav-text">Mis hábitos</span></button>
        <button class="nav-btn" data-view="estadisticas"><span data-icon="chart"></span><span class="nav-text">Estadísticas</span></button>
        <button class="nav-btn" data-view="calendario"><span data-icon="calendar"></span><span class="nav-text">Calendario</span></button>
      </nav>
      <div class="sidebar-bottom">
        <div class="tip-card">
          <div class="tip-icon" data-icon="sparkles"></div>
          <strong>Tu mejor versión<br>se construye hoy.</strong>
          <p>Los cambios más grandes empiezan con un pequeño hábito.</p>
        </div>
        <div class="side-footer">
          <div class="side-avatar" id="sideAvatar">U</div>
          <div><strong id="sideUserName">Tu espacio personal</strong><small>Mi progreso privado</small></div>
        </div>
      </div>
    </aside>

    <main class="main">
      <header class="topbar">
        <div style="display:flex;align-items:center;gap:12px">
          <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú" data-icon="menu"></button>
          <div><div class="breadcrumb">MI ESPACIO <span style="margin:0 6px">/</span> <b id="breadcrumbView">VISTA GENERAL</b></div><h1 class="top-title" id="topTitle">¡Qué gusto verte!</h1></div>
        </div>
        <div class="top-actions">
          <span id="dbStatus" class="date-chip" title="Conexión del sistema">Conectando...</span>
          <span class="date-chip"><span data-icon="calendar"></span><span id="dateText"></span></span>
          <div class="top-avatar" id="topAvatar" title="Tu perfil">U</div><button type="button" class="logout-button" id="logoutButton" title="Cerrar sesión">Salir <span aria-hidden="true">↗</span></button>
        </div>
      </header>

      <!-- DASHBOARD -->
      <section class="view active" id="view-dashboard">
        <div id="connectionNotice" class="connection-notice" hidden></div>
        <div class="hero">
          <div class="hero-copy">
            <div class="hero-kicker">TU PROGRESO EN MOVIMIENTO</div>
            <h2>Construye días que<br><em>valgan la pena.</em></h2>
            <p>Un pequeño paso todos los días puede llevarte muy lejos. Hoy es otra oportunidad para avanzar.</p>
            <button class="hero-button" data-open-modal><span data-icon="plus"></span> Nuevo hábito</button>
          </div>
          <div class="hero-progress">
            <div style="text-align:center"><div class="progress-ring" id="progressRing"><div class="progress-center"><strong id="progressPercent">0%</strong><small>COMPLETADO</small></div></div><div class="progress-caption" id="progressCaption">¡Vas por buen camino!</div></div>
          </div>
        </div>

        <div class="section-header"><div><h2 class="section-title">Tu día, en números</h2><p class="section-sub">Cada avance cuenta. Mira todo lo que estás logrando.</p></div></div>
        <div class="kpis">
          <div class="kpi-card"><div class="kpi-top"><div class="kpi-icon blue" data-icon="layers"></div><div class="kpi-trend">EN MARCHA</div></div><div class="kpi-number" id="kpiHabits">0</div><div class="kpi-label">Hábitos activos</div></div>
          <div class="kpi-card"><div class="kpi-top"><div class="kpi-icon purple" data-icon="checkCircle"></div><div class="kpi-trend">HOY</div></div><div class="kpi-number" id="kpiDone">0</div><div class="kpi-label">Completados hoy</div></div>
          <div class="kpi-card"><div class="kpi-top"><div class="kpi-icon cyan" data-icon="flame"></div><div class="kpi-trend">RÉCORD</div></div><div class="kpi-number" id="kpiStreak">0</div><div class="kpi-label">Mejor racha actual</div></div>
        </div>

        <div class="dashboard-grid">
          <div class="panel">
            <div class="panel-head"><div><h3 class="panel-title">Tu rutina de hoy</h3><p class="panel-sub">Lo que hagas hoy cuenta para tu futuro.</p></div><span class="tiny-badge" id="remainingBadge">0 pendientes</span></div>
            <div class="habits-list" id="todayHabits"></div>
            <div style="text-align:center;margin-top:17px"><button class="text-action" data-goto="habitos">Ver todos los hábitos <span data-icon="arrowRight"></span></button></div>
          </div>
          <div class="panel">
            <div class="panel-head"><div><h3 class="panel-title">Tu semana</h3><p class="panel-sub">Así va tu constancia en los últimos 7 días.</p></div><span class="tiny-badge">7 DÍAS</span></div>
            <div class="week-chart" id="weekBars"></div>
            <div class="chart-footer"><div><strong id="weeklyAverage">0%</strong><br><span>cumplimiento semanal</span></div><span class="chart-pill">Sigue sumando</span></div>
          </div>
        </div>

        <div class="bottom-grid">
          <div class="panel"><div class="panel-head"><div><h3 class="panel-title">Pequeñas victorias</h3><p class="panel-sub">Una vista rápida de tus últimos siete días.</p></div><button class="text-action" data-goto="calendario">Ver calendario <span data-icon="arrowRight"></span></button></div><div class="week-dots" id="weekDots"></div></div>
          <div class="panel quote-card"><div class="quote-kicker">UN RECORDATORIO PARA TI</div><h3>La constancia siempre gana.</h3><p>No necesitas hacerlo perfecto. Solo necesitas seguir avanzando.</p></div>
        </div>
      </section>

      <!-- HÁBITOS -->
      <section class="view" id="view-habitos">
        <div class="heading-row"><div class="page-heading"><div class="eyebrow">TUS PEQUEÑOS GRANDES PASOS</div><h2>Mis hábitos</h2><p>Organiza lo que importa y conviértelo en parte de tu día.</p></div><button class="primary-button" data-open-modal><span data-icon="plus"></span> Crear hábito</button></div>
        <div class="toolbar"><div class="searchbox"><span data-icon="search"></span><input id="habitSearch" type="search" placeholder="Buscar entre tus hábitos..." aria-label="Buscar hábitos" /></div><div class="filter-chips" id="categoryFilters"></div></div>
        <div class="big-habit-list" id="allHabits"></div>
      </section>

      <!-- ESTADÍSTICAS -->
      <section class="view" id="view-estadisticas">
        <div class="page-heading"><div class="eyebrow">CADA DÍA SUMA</div><h2>Tu progreso en perspectiva</h2><p>Visualiza lo que has construido con dedicación y pequeños pasos.</p></div>
        <div class="stats-kpis"><div class="kpi-card"><div class="kpi-top"><div class="kpi-icon blue" data-icon="checkCircle"></div></div><div class="kpi-number" id="statTotal">0</div><div class="kpi-label">Registros completados</div></div><div class="kpi-card"><div class="kpi-top"><div class="kpi-icon purple" data-icon="chart"></div></div><div class="kpi-number" id="statConsistency">0%</div><div class="kpi-label">Constancia semanal</div></div><div class="kpi-card"><div class="kpi-top"><div class="kpi-icon cyan" data-icon="flame"></div></div><div class="kpi-number" id="statStreak">0</div><div class="kpi-label">Mejor racha actual</div></div></div>
        <div class="stats-grid"><div class="panel"><div class="panel-head"><div><h3 class="panel-title">Actividad semanal</h3><p class="panel-sub">Hábitos completados por día.</p></div><span class="tiny-badge">ÚLTIMOS 7 DÍAS</span></div><div class="week-chart" id="statsWeekBars" style="height:250px"></div></div><div class="panel"><div class="panel-head"><div><h3 class="panel-title">Tus categorías</h3><p class="panel-sub">Cómo distribuyes tus objetivos.</p></div></div><div class="category-list" id="categoryStats"></div></div></div>
      </section>

      <!-- CALENDARIO -->
      <section class="view" id="view-calendario">
        <div class="page-heading"><div class="eyebrow">DÍAS QUE DEJAN HUELLA</div><h2>Calendario de constancia</h2><p>Cada punto representa un logro: aquí puedes ver tu recorrido.</p></div>
        <div class="panel"><div class="month-toolbar"><div class="month-title" id="monthTitle"></div><div class="month-buttons"><button class="round-btn" id="prevMonth" aria-label="Mes anterior" data-icon="chevronLeft"></button><button class="round-btn" id="nextMonth" aria-label="Mes siguiente" data-icon="chevronRight"></button></div></div><div class="calendar-grid" id="calendarGrid"></div><p class="calendar-note">Selecciona una fecha para ver cuántos hábitos completaste ese día.</p></div>
      </section>

      <div class="footer-note">RITMO. · Gestor y Visualizador de Hábitos Personales · Conectado a MariaDB</div>
    </main>
  </div>

  <!-- Modal de nuevo hábito -->
  <div class="overlay" id="modalOverlay" role="presentation">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <div class="modal-head"><div><div class="modal-kicker">TU RUTINA</div><h3 id="modalTitle">Crea tu próximo hábito</h3></div><button class="round-btn" id="closeModal" aria-label="Cerrar" data-icon="x"></button></div>
      <form id="habitForm">
        <label for="habitName">¿Cómo se llama tu hábito?</label>
        <input id="habitName" name="nombre" type="text" maxlength="150" placeholder="Ej. Leer 15 minutos" required />

        <label for="habitCategory">Categoría</label>
        <select id="habitCategory" name="categoria"><option value="">Sin categoría</option></select>

        <label>Elige un color</label>
        <div class="color-picks" id="colorPicks" role="group" aria-label="Color del hábito"></div>

        <fieldset class="tag-fieldset">
          <legend>Etiquetas</legend>
          <p class="tag-help">Selecciona una o varias opciones para organizar tu hábito.</p>
          <div class="tag-options" id="habitTags" role="group" aria-label="Etiquetas disponibles">
            <span class="tag-loading">Cargando etiquetas…</span>
          </div>
        </fieldset>

        <div class="modal-actions" style="margin-bottom:12px"><button class="secondary-button" type="button" id="newCategory">+ Nueva categoría</button></div>
        <div class="modal-actions"><button class="secondary-button" type="button" id="cancelModal">Cancelar</button><button class="primary-button" type="submit"><span data-icon="plus"></span> Guardar hábito</button></div>
      </form>
    </div>
  </div>
  <div class="toast" id="toast" role="status" aria-live="polite"></div>

  <script src="assets/app.js" defer></script>
</body>
</html>
