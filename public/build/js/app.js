// Tema claro / oscuro.
// El <head> ya aplica el tema guardado antes de pintar; aquí solo se conecta el botón.

const temaActual = () => document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';

function aplicarTema(tema) {
    document.documentElement.setAttribute('data-bs-theme', tema);

    const icono = document.querySelector('#darkmode');
    if (icono) {
        // En modo oscuro el botón ofrece volver a claro (sol) y viceversa
        icono.className = tema === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }
}

function guardarTema(tema) {
    try {
        localStorage.setItem('tema', tema);
    } catch (e) {
        // Navegación privada o almacenamiento bloqueado: el tema simplemente no se recuerda
    }
}

function temaGuardado() {
    try {
        return localStorage.getItem('tema');
    } catch (e) {
        return null;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    aplicarTema(temaActual());

    const boton = document.querySelector('.dark-mode');
    if (boton) {
        boton.addEventListener('click', function () {
            const nuevo = temaActual() === 'dark' ? 'light' : 'dark';
            aplicarTema(nuevo);
            guardarTema(nuevo);
        });
    }

    // Si la persona nunca eligió un tema, se sigue al del sistema operativo
    const sistema = window.matchMedia('(prefers-color-scheme: dark)');
    sistema.addEventListener('change', function (e) {
        if (!temaGuardado()) {
            aplicarTema(e.matches ? 'dark' : 'light');
        }
    });
});


// =========================================================
// Carrito sin recargar la página
// Los formularios funcionan solos sin JavaScript; aquí solo se mejoran.
// =========================================================

document.addEventListener('DOMContentLoaded', function () {

    // Selector de cantidad (− / +) en las tarjetas
    document.addEventListener('click', function (e) {
        const boton = e.target.closest('.js-menos, .js-mas');
        if (!boton) return;

        const input = boton.closest('.selector-cantidad').querySelector('input[name="cantidad"]');
        const max = parseInt(input.max, 10) || 99;
        const actual = parseInt(input.value, 10) || 1;
        const nuevo = boton.classList.contains('js-mas') ? actual + 1 : actual - 1;
        input.value = Math.min(max, Math.max(1, nuevo));
    });

    // Agregar desde tarjetas y acciones del carrito lateral
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('form.js-agregar, form.js-carrito-accion');
        if (!form) return;

        e.preventDefault();
        enviarAlCarrito(form);
    });
});

async function enviarAlCarrito(form) {
    const datos = new FormData(form);
    if (!datos.has('accion')) {
        datos.set('accion', 'agregar');
    }

    form.classList.add('enviando');

    let respuesta;
    try {
        const r = await fetch('/carrito/ajax', {
            method: 'POST',
            body: datos,
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        if (!r.ok) throw new Error('HTTP ' + r.status);
        respuesta = await r.json();
    } catch (error) {
        // Si algo falla, se envía el formulario de forma normal (con recarga)
        form.classList.remove('enviando');
        form.submit();
        return;
    }

    form.classList.remove('enviando');

    // En la página del carrito la tabla y los totales dependen del servidor: se recarga
    const enPaginaCarrito = /\/carrito\/?$/.test(location.pathname);
    if (enPaginaCarrito && form.classList.contains('js-carrito-accion')) {
        location.reload();
        return;
    }

    actualizarContador(respuesta.unidades);

    const cuerpo = document.getElementById('carritoLateralCuerpo');
    if (cuerpo && respuesta.html) {
        cuerpo.innerHTML = respuesta.html;
    }

    if (respuesta.producto) {
        actualizarTarjeta(respuesta.producto);
    }

    if (respuesta.mensaje) {
        mostrarAviso(respuesta.mensaje, respuesta.ok ? 'success' : 'danger', form.classList.contains('js-agregar') && respuesta.ok);
    }

    // Después de agregar, el selector vuelve a 1
    const input = form.querySelector('input[name="cantidad"]');
    if (input && respuesta.ok) input.value = 1;
}

function actualizarContador(unidades) {
    document.querySelectorAll('.js-contador').forEach(function (contador) {
        contador.textContent = unidades;
        contador.classList.toggle('d-none', unidades <= 0);
        // Reinicia la animación aunque ya se haya ejecutado antes
        contador.classList.remove('rebote');
        void contador.offsetWidth;
        contador.classList.add('rebote');
    });
}

function actualizarTarjeta(producto) {
    document.querySelectorAll('form.js-agregar[data-producto="' + producto.id + '"]').forEach(function (form) {
        const zona = form.querySelector('.js-zona-agregar');
        const sinDisponibles = form.querySelector('.js-sin-disponibles');
        const input = form.querySelector('input[name="cantidad"]');
        const nota = form.querySelector('.js-en-carrito');

        if (zona && sinDisponibles) {
            zona.classList.toggle('d-none', producto.disponibles <= 0);
            sinDisponibles.classList.toggle('d-none', producto.disponibles > 0);
        }
        if (input) {
            input.max = Math.max(1, producto.disponibles);
            if (parseInt(input.value, 10) > producto.disponibles) input.value = Math.max(1, producto.disponibles);
        }
        if (nota) {
            nota.textContent = producto.enCarrito > 0 ? producto.enCarrito + ' en tu carrito' : '';
        }
    });
}

function mostrarAviso(texto, tipo, conBotonCarrito) {
    const contenedor = document.querySelector('.contenedor-avisos');
    if (!contenedor || !window.bootstrap) {
        return;
    }

    const aviso = document.createElement('div');
    aviso.className = 'toast align-items-center border-0 text-bg-' + tipo;
    aviso.setAttribute('role', tipo === 'danger' ? 'alert' : 'status');

    const fila = document.createElement('div');
    fila.className = 'd-flex align-items-center';

    const cuerpo = document.createElement('div');
    cuerpo.className = 'toast-body fw-bold';
    cuerpo.textContent = texto; // textContent: el mensaje nunca se interpreta como HTML
    fila.appendChild(cuerpo);

    if (conBotonCarrito) {
        const ver = document.createElement('button');
        ver.type = 'button';
        ver.className = 'btn btn-sm btn-light ms-auto me-2 text-nowrap';
        ver.textContent = 'Ver carrito';
        ver.setAttribute('data-bs-toggle', 'offcanvas');
        ver.setAttribute('data-bs-target', '#carritoLateral');
        fila.appendChild(ver);
    }

    const cerrar = document.createElement('button');
    cerrar.type = 'button';
    cerrar.className = 'btn-close btn-close-white me-2 ' + (conBotonCarrito ? '' : 'ms-auto');
    cerrar.setAttribute('data-bs-dismiss', 'toast');
    cerrar.setAttribute('aria-label', 'Cerrar');
    fila.appendChild(cerrar);

    aviso.appendChild(fila);
    contenedor.appendChild(aviso);

    const toast = new bootstrap.Toast(aviso, { delay: 3500 });
    aviso.addEventListener('hidden.bs.toast', function () { aviso.remove(); });
    toast.show();
}
