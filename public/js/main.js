// public/js/main.js
import { initializeForm } from './form-handler.js';

function domReady(fn) {
    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

domReady(() => {
    // Solo inicializar si el contenedor del formulario existe
    // Usamos getElementById de form-handler.js indirectamente, pero aquí una comprobación general es buena.

        initializeForm();
    if (document.getElementById('mfa-form-container')) {
        // console.log('domReady');

    }
});