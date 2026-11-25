<?php
require_once __DIR__ . '/api/_session.php';
start_ritmo_session();
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}
header('Cache-Control: no-store');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#101940">
  <meta name="description" content="RITMO: un espacio para construir hábitos, medir avances y seguir creciendo.">
  <title>Iniciar sesión · RITMO</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/auth.css">
</head>
<body>
<main class="auth-layout">
  <section class="showcase" aria-label="Bienvenida a RITMO">
    <div class="showcase-inner">
      <a href="login.php" class="brand" aria-label="Inicio RITMO"><span class="brand-symbol">◈</span><span>RITMO<span class="brand-dot">.</span></span></a>
      <div class="showcase-body">
        <div class="eyebrow"><span class="sparkle">✦</span> TU ESPACIO, TU EVOLUCIÓN</div>
        <h1>Haz que cada día <em>cuente.</em></h1>
        <p>Construye pequeños hábitos, celebra tus logros y descubre cuánto puedes avanzar. Todo tu progreso en un solo lugar.</p>
        <div class="mini-preview" aria-hidden="true">
          <div class="preview-heading"><div class="preview-dot"></div> TU PRÓXIMO GRAN PASO <span>↗</span></div>
          <div class="preview-chart">
            <i style="--h:29%"></i><i style="--h:44%"></i><i style="--h:36%"></i><i style="--h:65%"></i><i style="--h:52%"></i><i style="--h:83%"></i><i style="--h:100%"></i>
          </div>
          <div class="preview-days"><span>L</span><span>M</span><span>M</span><span>J</span><span>V</span><span>S</span><span>D</span></div>
          <div class="preview-caption"><span>Un paso a la vez</span><strong>Tu progreso importa ✨</strong></div>
        </div>
      </div>
      <div class="showcase-footer"><span>✧</span> CONSTANCIA · CRECIMIENTO · BIENESTAR</div>
    </div>
    <div class="glow-one"></div><div class="glow-two"></div><div class="showcase-ring ring-one"></div><div class="showcase-ring ring-two"></div>
  </section>

  <section class="auth-area">
    <div class="auth-panel">
      <div class="badge"><span class="badge-star">✦</span> EMPIEZA UNA NUEVA ETAPA</div>
      <h2 id="formTitle">Bienvenido de nuevo.</h2>
      <p class="subtitle" id="formSubtitle">Tu progreso te espera. Ingresa para continuar.</p>

      <div class="tabbar" role="tablist" aria-label="Acceso a RITMO">
        <button type="button" class="tab active" id="tabLogin" role="tab" aria-selected="true">Iniciar sesión</button>
        <button type="button" class="tab" id="tabRegister" role="tab" aria-selected="false">Crear cuenta</button>
      </div>
      <form id="authForm" autocomplete="on">
        <div class="field" id="nameField" hidden>
          <label for="authName">¿Cómo te llamas?</label>
          <div class="input-wrap"><span aria-hidden="true">✧</span><input type="text" id="authName" name="nombre" placeholder="Tu nombre" maxlength="100" autocomplete="name"></div>
        </div>
        <div class="field">
          <label for="authEmail">Correo electrónico</label>
          <div class="input-wrap"><span aria-hidden="true">✉</span><input type="email" id="authEmail" name="correo" placeholder="nombre@correo.com" autocomplete="email" maxlength="190" required></div>
        </div>
        <div class="field">
          <div class="field-label-row"><label for="authPassword">Contraseña</label><span class="hint" id="passwordHint" hidden>Mínimo 10 caracteres</span></div>
          <div class="input-wrap"><span aria-hidden="true">⌁</span><input type="password" id="authPassword" name="contrasena" placeholder="Tu contraseña" autocomplete="current-password" required><button type="button" class="show-pass" data-toggle-password="authPassword" aria-label="Mostrar contraseña">Mostrar</button></div>
        </div>
        <div class="field" id="confirmField" hidden>
          <label for="authConfirm">Confirmar contraseña</label>
          <div class="input-wrap"><span aria-hidden="true">⌁</span><input type="password" id="authConfirm" name="confirmar" placeholder="Repite tu contraseña" autocomplete="new-password"><button type="button" class="show-pass" data-toggle-password="authConfirm" aria-label="Mostrar contraseña">Mostrar</button></div>
        </div>
        <div class="message" id="authMessage" role="alert" aria-live="polite" hidden></div>
        <button id="submitAuth" class="submit-btn" type="submit"><span id="submitText">Entrar a mi espacio</span><span aria-hidden="true">↗</span></button>
      </form>
      <div class="privacy"><span aria-hidden="true">🔒</span> Tu información y tus avances están asociados a tu cuenta.</div>
      <div class="auth-bottom">Pequeños hábitos. Grandes resultados. <strong>RITMO.</strong></div>
    </div>
  </section>
</main>
<script src="assets/auth.js" defer></script>
</body>
</html>
