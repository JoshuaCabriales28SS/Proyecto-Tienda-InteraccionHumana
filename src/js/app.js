// BOTONES PARA CONFIRMAR ELIMINACION
const eliminaciones = document.querySelectorAll(".eliminacion");
eliminaciones.forEach(eliminacion => {
    const btnAbrirEliminar = eliminacion.querySelector("#mostrarEliminar");
    const ventanaEliminar = eliminacion.querySelector("#ventanaEliminar");
    const btnCerrarEliminar = eliminacion.querySelector("#cerrarEliminar");

    btnAbrirEliminar.addEventListener("click", () => {
        ventanaEliminar.style.display = "flex";
    });

    btnCerrarEliminar.addEventListener("click", () => {
        ventanaEliminar.style.display = "none";
    });

    ventanaEliminar.addEventListener("click", (e) => {
        if (e.target === ventanaEliminar) {
            ventanaEliminar.style.display = "none";
        }
    });
});


// BOTONES PARA CARRITO
const carrito = document.getElementById('carrito');
const overlay = document.getElementById('overlay');
const focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
let lastFocus = null;

function mostrarCarrito() {
  if (!carrito || !overlay) {
    return;
  }
  lastFocus = document.activeElement;
  carrito.classList.add('is-visible');
  overlay.classList.add('is-visible');
  carrito.setAttribute('aria-hidden', 'false');
  overlay.setAttribute('aria-hidden', 'false');

  const firstFocusable = carrito.querySelector(focusableSelector);
  if (firstFocusable) {
    firstFocusable.focus();
  }
}

function cerrarCarrito() {
  if (!carrito || !overlay) {
    return;
  }
  carrito.classList.remove('is-visible');
  overlay.classList.remove('is-visible');
  carrito.setAttribute('aria-hidden', 'true');
  overlay.setAttribute('aria-hidden', 'true');

  if (lastFocus instanceof HTMLElement) {
    lastFocus.focus();
  }
}

function animarCarrito() {
  const boton = document.querySelector('.add-to-cart');
  if (!boton) {
    return;
  }
  const badge = boton.querySelector('.carrito-badge');

  boton.classList.remove('is-added');
  void boton.offsetWidth;
  boton.classList.add('is-added');

  if (badge) {
    badge.classList.remove('is-bump');
    void badge.offsetWidth;
    badge.classList.add('is-bump');
  }
}

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    cerrarCarrito();
  }
  if (event.key === 'Tab' && carrito && carrito.classList.contains('is-visible')) {
    const focusable = carrito.querySelectorAll(focusableSelector);
    if (!focusable.length) {
      return;
    }
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const boton = document.querySelector('.add-to-cart');
  if (boton && boton.dataset.cartBump === 'true') {
    animarCarrito();
  }

  const params = new URLSearchParams(window.location.search);
  if (params.get('carrito') === '1') {
    mostrarCarrito();
  }

  document.querySelectorAll('[data-cart-select]').forEach((checkbox) => {
    checkbox.addEventListener('change', () => {
      const form = checkbox.closest('form');
      if (!form) {
        return;
      }
      let actionInput = form.querySelector('input[data-accion]');
      if (!actionInput) {
        actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'accion';
        actionInput.dataset.accion = 'true';
        form.appendChild(actionInput);
      }
      actionInput.value = 'seleccionar';
      form.submit();
    });
  });
});


// BOTONES PARA CONFIRMAR PAGO
const pagar = document.getElementById("pagar");
const btnAbrirPago = document.getElementById("mostrarPagar");
const btnCerrarPago = document.getElementById("cerrarPagar");
const ventanaPago = document.getElementById("ventanaPagar");
btnAbrirPago.addEventListener("click", (e) => {
  e.preventDefault();
  ventanaPago.style.display = "flex";
});
btnCerrarPago.addEventListener("click", (e) => {
  e.preventDefault();
  ventanaPago.style.display = "none";
});


// ACOMODAR NUMEROS DE TARJETA
const tarjeta = document.getElementById("tarjeta");

tarjeta.addEventListener("input", (e) => {
  let target = e.target;
  let value = target.value.replace(/\D/g, '');
  let formattedValue = '';

  value = value.substring(0, 16);

  for (let i = 0; i < value.length; i++) {
      if (i > 0 && i % 4 === 0) {
          formattedValue += ' ';
      }
      formattedValue += value[i];
  }

  target.value = formattedValue;
});
