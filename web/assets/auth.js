'use strict';
const elements = {
  login: document.getElementById('tabLogin'), register: document.getElementById('tabRegister'),
  title: document.getElementById('formTitle'), subtitle: document.getElementById('formSubtitle'),
  nameField: document.getElementById('nameField'), confirmField: document.getElementById('confirmField'),
  name: document.getElementById('authName'), email: document.getElementById('authEmail'),
  password: document.getElementById('authPassword'), confirm: document.getElementById('authConfirm'),
  passwordHint: document.getElementById('passwordHint'), form: document.getElementById('authForm'),
  message: document.getElementById('authMessage'), submit: document.getElementById('submitAuth'),
  submitText: document.getElementById('submitText')
};
let csrf = '';
let mode = 'ingresar';

async function getSessionToken() {
  const response = await fetch('api/auth.php', { credentials: 'same-origin', cache: 'no-store' });
  const result = await response.json();
  if (!result.success) throw new Error('No se pudo preparar el formulario.');
  if (result.data.autenticado) { location.replace('index.php'); return; }
  csrf = result.data.csrf;
}
function showMessage(message) {
  elements.message.hidden = false;
  elements.message.textContent = message;
}
function changeMode(next) {
  mode = next;
  const registering = mode === 'registrar';
  elements.login.classList.toggle('active', !registering);
  elements.register.classList.toggle('active', registering);
  elements.login.setAttribute('aria-selected', String(!registering));
  elements.register.setAttribute('aria-selected', String(registering));
  elements.nameField.hidden = !registering;
  elements.confirmField.hidden = !registering;
  elements.passwordHint.hidden = !registering;
  elements.name.required = registering;
  elements.confirm.required = registering;
  elements.password.autocomplete = registering ? 'new-password' : 'current-password';
  elements.title.textContent = registering ? 'Tu mejor versión comienza aquí.' : 'Bienvenido de nuevo.';
  elements.subtitle.textContent = registering ? 'Crea tu espacio personal y empieza a construir tu constancia.' : 'Tu progreso te espera. Ingresa para continuar.';
  elements.submitText.textContent = registering ? 'Crear mi cuenta' : 'Entrar a mi espacio';
  elements.password.value = '';
  elements.confirm.value = '';
  elements.message.hidden = true;
}
elements.login.addEventListener('click', () => changeMode('ingresar'));
elements.register.addEventListener('click', () => changeMode('registrar'));
document.querySelectorAll('[data-toggle-password]').forEach(button => button.addEventListener('click', () => {
  const target = document.getElementById(button.dataset.togglePassword);
  const show = target.type === 'password';
  target.type = show ? 'text' : 'password';
  button.textContent = show ? 'Ocultar' : 'Mostrar';
}));
elements.form.addEventListener('submit', async event => {
  event.preventDefault();
  elements.message.hidden = true;
  if (!csrf) { showMessage('No hay conexión con PHP. Recarga la página.'); return; }
  if (mode === 'registrar' && elements.password.value !== elements.confirm.value) {
    showMessage('Las contraseñas no coinciden.'); return;
  }
  elements.submit.disabled = true;
  elements.submitText.textContent = 'Comprobando...';
  try {
    const response = await fetch('api/auth.php', {
      method: 'POST', credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
      body: JSON.stringify({
        accion: mode,
        nombre: elements.name.value,
        correo: elements.email.value,
        contrasena: elements.password.value,
        confirmar: elements.confirm.value
      })
    });
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error(result.message || 'No se pudo completar la operación.');
    location.replace('index.php');
  } catch (error) {
    showMessage(error.message || 'Error de conexión.');
    elements.submit.disabled = false;
    elements.submitText.textContent = mode === 'registrar' ? 'Crear mi cuenta' : 'Entrar a mi espacio';
  }
});
getSessionToken().catch(() => showMessage('No se pudo contactar a la API PHP. Verifica XAMPP y MariaDB.'));
