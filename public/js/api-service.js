// public/js/api-service.js

// mfa_params es global gracias a wp_localize_script en el handle 'mfa-main-js'
// que es dependencia de este. Los módulos pueden acceder a globales, aunque pasar
// config explícitamente es a veces más limpio para módulos muy reutilizables.
const { rest_url, nonce, selectDataEndpointBase, submitEndpointPath } = mfa_params;

/**
 * Obtiene datos para un select desde un endpoint específico de la API REST de WordPress.
 * @param {string} selectIdentifier Identificador único para el select (ej: 'opciones1').
 */
async function fetchSelectOptions(selectIdentifier) {
    console.log(`Workspaceing select options for "${selectIdentifier}" via WP REST API...`);
    const endpoint = rest_url + selectDataEndpointBase + encodeURIComponent(selectIdentifier);

    try {
        const response = await fetch(endpoint, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce, // Nonce de WordPress
            },
        });

        const data = await response.json(); // Intenta parsear JSON siempre

        if (!response.ok) {
            // data.message viene del WP_Error o de la API externa si se propagó
            const errorMessage = data.message || `Error ${response.status}: ${response.statusText}`;
            throw new Error(errorMessage);
        }
        return data; // Datos de la API externa, ya procesados por PHP
    } catch (error) {
        console.error(`API Fetch Select Options Error for ${selectIdentifier}:`, error.message);
        throw error; // Re-lanza para que form-handler.js lo maneje
    }
}

/**
 * Envía los datos del formulario a la API REST de WordPress.
 * @param {object} formDataObject Datos del formulario como objeto.
 */
async function submitFormData(formDataObject) {
    console.log('Submitting form data via WP REST API...');
    const endpoint = rest_url + submitEndpointPath;

    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify(formDataObject),
        });

        const data = await response.json(); // Intenta parsear JSON siempre

        if (!response.ok) {
            const errorMessage = data.message || `Error ${response.status}: ${response.statusText}`;
            throw new Error(errorMessage);
        }
        return data; // Respuesta de la API externa, ya procesada por PHP
    } catch (error) {
        console.error('API Submit Form Error:', error.message);
        throw error;
    }
}

export { fetchSelectOptions, submitFormData };