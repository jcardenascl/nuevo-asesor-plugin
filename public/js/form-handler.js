// public/js/form-handler.js
import { fetchSelectOptions, submitFormData } from './api-service.js';

const FORM_ID = 'mfa-external-api-form';
const LOADING_INDICATOR_ID = 'mfa-loading';
const ERROR_MESSAGE_ID = 'mfa-error';
const SUCCESS_MESSAGE_ID = 'mfa-success-message';
const SUBMIT_BUTTON_ID = 'mfa-submit-button';

// Textos desde mfa_params para internacionalización
const TEXTS = mfa_params.text || {};

function getElement(id) {
    return document.getElementById(id);
}

function displayMessage(elementId, message, isError = false) {
    const el = getElement(elementId);
    if (el) {
        el.textContent = message;
        el.style.display = 'block';
        el.className = isError ? 'mfa-message mfa-error-message' : 'mfa-message mfa-success-message';
    }
}

function hideMessages() {
    [ERROR_MESSAGE_ID, SUCCESS_MESSAGE_ID].forEach(id => {
        const el = getElement(id);
        if (el) el.style.display = 'none';
    });
}

function setLoadingState(isLoading) {
    const loadingEl = getElement(LOADING_INDICATOR_ID);
    const submitButton = getElement(SUBMIT_BUTTON_ID);

    if (loadingEl) loadingEl.style.display = isLoading ? 'block' : 'none';
    if (submitButton) submitButton.disabled = isLoading;
}

/**
 * Carga opciones en un elemento select.
 * @param {string} selectId ID del elemento select.
 * @param {Array} optionsData Array de objetos { value: '', label: '' }.
 */
function populateSelect(selectId, optionsData) {
    const selectElement = getElement(selectId);
    if (!selectElement) {
        console.error(`Select element with ID "${selectId}" not found.`);
        return;
    }

    selectElement.innerHTML = `<option value="">${TEXTS.select_default || 'Seleccione una opción'}</option>`; // Opción por defecto
    if (Array.isArray(optionsData)) {
        optionsData.forEach(option => {
            const optionElement = document.createElement('option');
            // Asegúrate de que tu API externa devuelve 'value' y 'label' o ajusta aquí
            optionElement.value = option.value;
            optionElement.textContent = option.label;
            selectElement.appendChild(optionElement);
        });
    } else {
        console.warn(`Data for select ${selectId} is not an array:`, optionsData);
        displayMessage(ERROR_MESSAGE_ID, `Error: Formato de datos incorrecto para el select ${selectId}.`, true);
    }
}

async function initializeForm() {
    const formElement = getElement(FORM_ID);
    if (!formElement) return; // No hay formulario en esta página

    const loadingIndicator = getElement(LOADING_INDICATOR_ID);
    if (loadingIndicator) loadingIndicator.textContent = TEXTS.loading || 'Cargando...';

    setLoadingState(true);
    hideMessages();

    try {
        console.log('Initializing form, select data will be fetched via WP REST API.');

        // Cargar datos para los selects
        // El 'select_id' (ej: 'opciones_categoria') debe coincidir con lo que espera tu endpoint REST en PHP
        // y cómo tu MFA_API_Handler construye la URL para la API externa.
        const select1Data = await fetchSelectOptions('opciones_tipo_documento'); // Cambia 'opciones_tipo_documento'
        populateSelect('mfa-opcion-api-1', select1Data);

        const select2Data = await fetchSelectOptions('opciones_ciudad'); // Cambia 'opciones_ciudad'
        populateSelect('mfa-opcion-api-2', select2Data);

        // ...cargar más selects si es necesario
        // const select3Data = await fetchSelectOptions('otro_identificador');
        // populateSelect('mfa-opcion-api-3', select3Data);

    } catch (error) {
        console.error('Failed to initialize form with select data:', error.message);
        displayMessage(ERROR_MESSAGE_ID, `Error al inicializar: ${error.message}`, true);
    } finally {
        setLoadingState(false);
    }

    formElement.addEventListener('submit', async (event) => {
        event.preventDefault();
        hideMessages();
        setLoadingState(true);

        const formData = new FormData(formElement);
        const data = Object.fromEntries(formData.entries());

        try {
            const result = await submitFormData(data);
            console.log('Form submitted successfully via WP REST API:', result);
            displayMessage(SUCCESS_MESSAGE_ID, result.message || 'Formulario enviado con éxito.');
            formElement.reset();
        } catch (error) {
            console.error('Failed to submit form via WP REST API:', error.message);
            displayMessage(ERROR_MESSAGE_ID, `Error al enviar: ${error.message}`, true);
        } finally {
            setLoadingState(false);
        }
    });
}

export { initializeForm };