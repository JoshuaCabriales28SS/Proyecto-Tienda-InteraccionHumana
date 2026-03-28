const eliminaciones = document.querySelectorAll(".eliminacion");

eliminaciones.forEach(eliminacion => {
    const btnAbrir = eliminacion.querySelector("#mostrarEliminar");
    const ventana = eliminacion.querySelector("#ventanaEliminar");
    const btnCerrar = eliminacion.querySelector("#cerrarEliminar");

    btnAbrir.addEventListener("click", () => {
        ventana.style.display = "flex";
    });

    btnCerrar.addEventListener("click", () => {
        ventana.style.display = "none";
    });

    ventana.addEventListener("click", (e) => {
        if (e.target === ventana) {
            ventana.style.display = "none";
        }
    });
});


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
