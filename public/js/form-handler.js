// public/js/form-handler.js
import { fetchSelectOptions, submitFormData } from './api-service.js';

const FORM_ID = 'mfa-external-api-form';
const LOADING_INDICATOR_ID = 'mfa-loading';
const ERROR_MESSAGE_ID = 'mfa-error';
const SUCCESS_MESSAGE_ID = 'mfa-success-message';
const SUBMIT_BUTTON_ID = 'mfa-submit-button';
const STEP = 'form-step';
const ACTIVE_STEP = 'form-step.active-step';
const NEXT_BTN = '.js-next-step';
const PREV_BTN = '.js-prev-step';
const TIPO_PERSONA = '#tipoPersona';

// Estado del formulario
let currentStep = 1;

const container = document.querySelector('.sucursales-container');
let counter = 1;
// Funciones para los productos de las transacciones extranjeras
let productoCounter = 0;

// Configuración actualizada
const referenceConfig = {
    financieras: {
        addButton: '.add-ref-financiera',
        template: '.referencia-type[data-tipo="financieras"] .referencia-template',
        campos: ['entidad', 'ciudad', 'cuenta', 'antiguedad', 'producto']
    },
    proveedores: {
        addButton: '.add-ref-proveedor',
        template: '.referencia-type[data-tipo="proveedores"] .referencia-template',
        campos: ['entidad', 'ciudad', 'cuenta', 'antiguedad', 'producto']
    },
    comerciales: {
        addButton: '.add-ref-comercial',
        template: '.referencia-type[data-tipo="comerciales"] .referencia-template',
        campos: ['entidad', 'direccion', 'ciudad', 'persona_contacto', 'telefono']
    }
};




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
        setLoadingState(true);
        hideMessages();

        
        const formElement = getElement(FORM_ID);
        
        if (!formElement) return; // No hay formulario en esta página
        
        document.querySelectorAll('.js-next-step').forEach(btn => {
            btn.addEventListener('click', () => nextStep(currentStep + 1));
        });
        document.querySelectorAll(PREV_BTN).forEach(btn => {
            btn.addEventListener('click', () => prevStep(currentStep - 1));
        });

        // Mostrar/ocultar datos cónyuge
        document.getElementById('natural_estado_civil').addEventListener('change', function () {
            const esCasado = this.value === 'C';
            const conyugeSection = document.querySelector('.conyuge-section');
            const conyugeFields = document.querySelectorAll('[data-conyuge]');

            if (esCasado) {
                conyugeSection.style.display = 'block';
                conyugeFields.forEach(field => {
                    if (!field.hasAttribute('data-optional')) {
                        field.setAttribute('required', 'required');
                    }
                });
            } else {
                conyugeSection.style.display = 'none';
                conyugeFields.forEach(field => {
                    field.removeAttribute('required');
                    if (field.tagName === 'SELECT') {
                        field.selectedIndex = 0;
                    } else {
                        field.value = '';
                    }
                });
            }
        });

        document.getElementById('button_activate_apoderado').addEventListener('click', function () {
            const section = document.querySelector('.apoderado-section');
            const apoderadoFields = section.querySelectorAll('[data-apoderado]');
            const removeButton = document.getElementById('button_remove_apoderado');
            const isVisible = section.style.display === 'block';

            // Toggle visibility
            section.style.display = isVisible ? 'none' : 'block';
            removeButton.style.display = isVisible ? 'none' : 'inline-block'; // Mostrar/ocultar remover

            // Habilita o deshabilita los "required"
            apoderadoFields.forEach(field => {
                if (isVisible) {
                    field.removeAttribute('required');
                    if (field.tagName === 'SELECT') {
                        field.selectedIndex = 0;
                    } else {
                        field.value = '';
                    }
                } else {
                    if (!field.hasAttribute('data-optional')) {
                        field.setAttribute('required', 'required');
                    }
                }
            });
        });

        const removeBtn = document.getElementById('button_remove_apoderado');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                const section = document.querySelector('.apoderado-section');
                const apoderadoFields = section.querySelectorAll('[data-apoderado]');

                apoderadoFields.forEach(field => {
                    field.removeAttribute('required');
                    if (field.tagName === 'SELECT') {
                        field.selectedIndex = 0;
                    } else {
                        field.value = '';
                    }
                });

                section.style.display = 'none';
            });
        }

        // Alternar entre actividad económica
        document.querySelectorAll('input[name="actividad_economica"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.querySelector('.asalariado-fields').style.display =
                    this.value === 'asalariado' ? 'block' : 'none';
                document.querySelector('.no-asalariado-fields').style.display =
                    this.value === 'no_asalariado' ? 'block' : 'none';
            });
        });


        // Mostrar/ocultar detalles de autoretenedores
        document.getElementById('juridica_es_autoretenedor').addEventListener('change', function () {
            const details = document.getElementById('autoretenedor-details');
            details.style.display = this.checked ? 'block' : 'none';
            details.querySelector('input').required = this.checked;
        });

        // Mostrar/ocultar detalles de grandes contribuyentes
        document.getElementById('juridica_es_grande_contribuyente').addEventListener('change', function () {
            const details = document.getElementById('gran-contribuyente-details');
            details.style.display = this.checked ? 'block' : 'none';
            details.querySelector('input').required = this.checked;
        });

        document.querySelectorAll('.documento-item input[type="file"]').forEach(input => {
            input.addEventListener('change', function () {
                const fileName = this.files[0]?.name || 'Ningún archivo seleccionado';
                const displayElement = document.createElement('div');
                displayElement.className = 'file-display';
                displayElement.textContent = fileName;

                const existingDisplay = this.parentNode.querySelector('.file-display');
                if (existingDisplay) existingDisplay.remove();

                this.parentNode.appendChild(displayElement);
            });
        });

        // Agregar sucursal
        document.querySelector('.add-sucursal').addEventListener('click', function () {
            const newSucursal = cloneTemplate();
            container.insertBefore(newSucursal, this);
        });

        // Eliminar sucursal
        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-sucursal')) {
                e.target.closest('.sucursal-template').remove();
            }
        });

        // Inicializar todas las referencias
        Object.entries(referenceConfig).forEach(([tipo, config]) => {
            initReferences(tipo, config);
        });

        // Toggle Productos
        document.querySelector('.toggle-product').addEventListener('click', ()=> toggleProductos());
        // Agregar Producto
        document.querySelector('.add-producto').addEventListener('click', ()=> agregarProducto());



        // Función para recolectar datos de referencias
        window.getReferenciasData = function () {
            return {
                referencias_financieras: getReferenciasTipo('financieras', referenceConfig.financieras.campos),
                referencias_proveedores: getReferenciasTipo('proveedores', referenceConfig.proveedores.campos),
                referencias_comerciales: getReferenciasTipo('comerciales', referenceConfig.comerciales.campos)
            };
        };


        formElement.addEventListener('submit', async (event) => {
            event.preventDefault();
            hideMessages();
            setLoadingState(true);

            if (!validateStep(4)) return;

            const formDataTest = buildFormData();


            // const formData = new FormData(formElement);
            // const data = Object.fromEntries(formData.entries());

            try {
                const result = await submitFormData(formDataTest);
                console.log('Form submitted successfully via WP REST API:', result);
                displayMessage(SUCCESS_MESSAGE_ID, result.message || 'Formulario enviado con éxito.');
                alert('Formulario enviado con éxito.');
                setTimeout(() => {
                    formElement.reset();
                    currentStep = 1;
                    updateProgress();
                    showStep(1);                    
                }, 5000);
            } catch (error) {
                console.error('Failed to submit form via WP REST API:', error.message);
                displayMessage(ERROR_MESSAGE_ID, `Error al enviar: ${error.message}`, true);
            } finally {
                setLoadingState(false);
            }
        });

         showStep(1);
         return;
    }
    function updateProgress() {
        document.getElementById('progress').style.width = `${(currentStep / 4) * 100}%`;
    }

    function showStep(step) {       
        document.querySelectorAll('.form-step').forEach(el => {
            el.classList.remove('active-step');
        });
        document.querySelector(`[data-step="${step}"]`).classList.add('active-step');
        setLoadingState(false);
        hideMessages();
        return;
    }

    async function nextStep(next) {
        if (validateStep(currentStep)) {
            currentStep = next;
            updateProgress();
            showStep(next);

            // Actualiza campos según tipo de persona
            if (currentStep === 2) {
                const tipo = checkTipoPersona();
                document.getElementById('naturalFields').style.display =
                    tipo === 'natural' ? 'block' : 'none';
                document.getElementById('juridicaFields').style.display =
                    tipo === 'juridica' ? 'block' : 'none';

                const loadingIndicator = getElement(LOADING_INDICATOR_ID);
                if (loadingIndicator) loadingIndicator.textContent = TEXTS.loading || 'Cargando...';
                
                setLoadingState(true);
                hideMessages();


                try {
                    console.log('Initializing form, select data will be fetched via WP REST API.');

                //     // Cargar datos para los selects
                //     // El 'select_id' (ej: 'opciones_categoria') debe coincidir con lo que espera tu endpoint REST en PHP
                //     // y cómo tu MFA_API_Handler construye la URL para la API externa.
                const select1Data = await fetchSelectOptions('opciones_bancos'); // Cambia 'opciones_tipo_documento'
                const select2Data = await fetchSelectOptions('opciones_ciudad'); // Cambia 'opciones_ciudad'
                const select3Data = await fetchSelectOptions('opciones_coord_comercial');
                
                
                if (tipo === 'juridica') {
                    populateSelect('juridica_ciudad', select2Data);
                    populateSelect('juridica_representante_lugar_expedicion', select2Data);
                    populateSelect('juridica_representante_lugar_nacimiento', select2Data);
                    populateSelect('juridica_banco', select1Data);
                    populateSelect('juridica_coordinacion', select3Data);
                    
                } else {
                    populateSelect('natural_lugar_expedicion', select2Data);
                    populateSelect('natural_lugar_nacimiento', select2Data);
                    populateSelect('natural_ciudad_residencia', select2Data);
                    populateSelect('natural_banco', select1Data);
                    populateSelect('natural_coordinacion', select3Data);
                    populateSelect('conyuge_lugar_nacimiento', select2Data);
                    populateSelect('conyuge_ciudad_residencia', select2Data);
                    populateSelect('natural_apoderado_lugar_nacimiento', select2Data);
                    populateSelect('natural_apoderado_ciudad_residencia', select2Data);
                    populateSelect('natural_ciudad_empresa', select2Data);
                    populateSelect('natural_negocio_ciudad', select2Data);
                }

                } catch (error) {
                    console.error('Failed to initialize form with select data:', error.message);
                    displayMessage(ERROR_MESSAGE_ID, `Error al inicializar: ${error.message}`, true);
                } finally {
                    setLoadingState(false);
                }
                return;
            }
            // Actualiza campos según tipo de persona
            if (currentStep === 3) {
                const tipo = checkTipoPersona();
                document.getElementById('naturalFields3').style.display =
                    tipo === 'natural' ? 'block' : 'none';
                document.getElementById('juridicaFields3').style.display =
                    tipo === 'juridica' ? 'block' : 'none';
            }
            if (currentStep === 4) {
                const tipo = checkTipoPersona();
                document.getElementById('naturalFields4').style.display =
                    tipo === 'natural' ? 'block' : 'none';
                document.getElementById('juridicaFields4').style.display =
                    tipo === 'juridica' ? 'block' : 'none';
            }
        }
    }

    function prevStep() {
        currentStep--;
        updateProgress();
        showStep(currentStep);
    }

    function verificarSeleccionActividad() {
        const opciones = document.getElementsByName('actividad_economica');
        return Array.from(opciones).some(opcion => opcion.checked);
    }

    function checkTipoPersona() {
        return document.getElementById('tipoPersona').value;
    }

    function validateStep(step) {
        let isValid = true;
        document.getElementById('errorMessages').innerHTML = '';

        switch (step) {
            case 1:
                if (!checkTipoPersona()) {
                    showError('Seleccione un tipo de persona');
                    isValid = false;
                }
                break;

            case 2:
                const inputs = document.querySelectorAll('[data-step="2"] [required]:not([disabled]):not([type="hidden"])');                
                inputs.forEach(input => {
                    if (input.offsetParent !== null && !input.value) {
                        showError('Complete todos los campos requeridos ' + input.placeholder);
                        isValid = false;
                    }
                });
                break;
            case 3:
                if (checkTipoPersona() === 'natural') {
                    if (!verificarSeleccionActividad()) {
                        alert("Debe seleccionar una actividad económica");
                        showError('Complete todos los campos requeridos ' + input.placeholder);
                        isValid = false;
                        return;
                    }                    
                }


                const inputs3 = document.querySelectorAll('[data-step="3"] [required]:not([disabled]):not([type="hidden"])');                

                inputs3.forEach(input => {
                    if (input.offsetParent !== null && !input.value) {
                        showError('Complete todos los campos requeridos');
                        isValid = false;
                    }
                });
                break;
            case 4:
                isValid = validateFileInputs()
                break;
        }

        return isValid;
    }

    function validateFileInputs() {
        let isValid = true;
        const container = checkTipoPersona() === 'natural' ? '#naturalFields4 ' : '#juridicaFields4 ';


        document.querySelectorAll(container + 'input[type="file"][required]').forEach(input => {
            if (!input.files || input.files.length === 0) {
                alert(`El archivo ${input.id} es requerido`);
                isValid = false;
            } else if (Array.from(input.files).some(file => file.size > 5 * 1024 * 1024)) {
                alert(`El archivo ${input.id} supera los 5MB`);
                isValid = false;
            }
        });

        return isValid;
    }

    function showError(message) {
        displayMessage(ERROR_MESSAGE_ID, message, true);

        document.querySelectorAll('.error-message').forEach(error => error.innerHTML = `<div class="error">${message}</div>`);
    }

    function buildFormData() {
        const formData = new FormData();
        const tipoPersona = checkTipoPersona();

        if (tipoPersona === 'natural') {
            // Datos Personales
            const datosPersonales = {
                primer_nombre: document.getElementById('natural_primer_nombre').value,
                segundo_nombre: document.getElementById('natural_segundo_nombre').value,
                primer_apellido: document.getElementById('natural_primer_apellido').value,
                segundo_apellido: document.getElementById('natural_segundo_apellido').value,
                tipo_documento: document.getElementById('natural_tipo_documento').value,
                numero_documento: document.getElementById('natural_numero_documento').value,
                fecha_expedicion: document.getElementById('natural_fecha_expedicion').value,
                lugar_expedicion: document.getElementById('natural_lugar_expedicion').value,
                fecha_nacimiento: document.getElementById('natural_fecha_nacimiento').value,
                lugar_nacimiento: document.getElementById('natural_lugar_nacimiento').value,
                sexo: document.getElementById('natural_sexo').value,
                nacionalidad: document.getElementById('natural_nacionalidad').value,
                ciudad_residencia: document.getElementById('natural_ciudad_residencia').value,
                direccion: document.getElementById('natural_direccion').value,
                correo: document.getElementById('natural_correo').value,
                celular: document.getElementById('natural_celular').value,
                estado_civil: document.getElementById('natural_estado_civil').value,
                ocupacion: document.getElementById('natural_ocupacion').value,
                tipo_vivienda: document.getElementById('natural_tipo_vivienda').value,
                nivel_estudio: document.getElementById('natural_nivel_estudio').value,
                banco: document.getElementById('natural_banco').value,
                tipo_cuenta: document.getElementById('natural_tipo_cuenta').value,
                numero_cuenta: document.getElementById('natural_numero_cuenta').value,
                coordinacion: document.getElementById('natural_coordinacion').value,
            };
            formData.append('datos_personales', JSON.stringify(datosPersonales));

            // Datos Cónyuge
            const conyugeData = {
                primer_nombre_conyuge: document.getElementById('conyuge_primer_nombre').value,
                segundo_nombre_conyuge: document.getElementById('conyuge_segundo_nombre').value,
                primer_apellido_conyuge: document.getElementById('conyuge_primer_apellido').value,
                segundo_apellido_conyuge: document.getElementById('conyuge_segundo_apellido').value,
                tipo_documento_conyuge: document.getElementById('conyuge_tipo_documento').value,
                numero_documento_conyuge: document.getElementById('conyuge_numero_documento').value,
                fecha_nacimiento_conyuge: document.getElementById('conyuge_fecha_nacimiento').value,
                lugar_nacimiento_conyuge: document.getElementById('conyuge_lugar_nacimiento').value,
                empresa_conyuge: document.getElementById('conyuge_empresa').value,
                antiguedad_conyuge: document.getElementById('conyuge_antiguedad').value,
                cargo_conyuge: document.getElementById('conyuge_cargo').value,
                ciudad_residencia_conyuge: document.getElementById('conyuge_ciudad_residencia').value,
                direccion_conyuge: document.getElementById('conyuge_direccion').value,
                celular_conyuge: document.getElementById('conyuge_celular').value,
                correo_conyuge: document.getElementById('conyuge_correo').value
            };

            formData.append('datos_conyuge', JSON.stringify(conyugeData));

            // Datos Apoderado
            const apoderadoData = {
                primer_nombre_apoderado: document.getElementById('natural_apoderado_primer_nombre').value,
                segundo_nombre_apoderado: document.getElementById('natural_apoderado_segundo_nombre').value,
                primer_apellido_apoderado: document.getElementById('natural_apoderado_primer_apellido').value,
                segundo_apellido_apoderado: document.getElementById('natural_apoderado_segundo_apellido').value,
                tipo_documento_apoderado: document.getElementById('natural_apoderado_tipo_documento').value,
                numero_documento_apoderado: document.getElementById('natural_apoderado_numero_documento').value,
                fecha_nacimiento_apoderado: document.getElementById('natural_apoderado_fecha_nacimiento').value,
                lugar_nacimiento_apoderado: document.getElementById('natural_apoderado_lugar_nacimiento').value,
                empresa_apoderado: document.getElementById('natural_apoderado_empresa').value,
                antiguedad_apoderado: document.getElementById('natural_apoderado_antiguedad').value,
                cargo_apoderado: document.getElementById('natural_apoderado_cargo').value,
                ciudad_residencia_apoderado: document.getElementById('natural_apoderado_ciudad_residencia').value,
                direccion_apoderado: document.getElementById('natural_apoderado_direccion').value,
                celular_apoderado: document.getElementById('natural_apoderado_celular').value,
                correo_apoderado: document.getElementById('natural_apoderado_correo').value
            };
            formData.append('datos_apoderado', JSON.stringify(apoderadoData));

            // Actividad Económica
            const actividadType = document.querySelector('input[name="actividad_economica"]:checked').value;
            if (actividadType === 'asalariado') {
                const asalariadoData = {
                    empresa_asalariado: document.getElementById('natural_empresa').value,
                    ciudad_empresa_asalariado: document.getElementById('natural_ciudad_empresa').value,
                    direccion_empresa_asalariado: document.getElementById('natural_direccion_empresa').value,
                    telefono_empresa_asalariado: document.getElementById('natural_telefono_empresa').value,
                    actividad_economica_asalariado: document.getElementById('natural_actividad_economica').value,
                    antiguedad_asalariado: document.getElementById('natural_antiguedad').value,
                    cargo_asalariado: document.getElementById('natural_cargo').value,
                    ingresos_mensuales_asalariado: document.getElementById('natural_ingresos_asalariado').value
                };
                formData.append('actividad_economica_asalariado', JSON.stringify(asalariadoData));
            } else {
                const noAsalariadoData = {
                    negocio_nombre_no_asalariado: document.getElementById('natural_negocio_nombre').value,
                    negocio_ciudad_no_asalariado: document.getElementById('natural_negocio_ciudad').value,
                    negocio_direccion_no_asalariado: document.getElementById('natural_negocio_direccion').value,
                    negocio_celular_no_asalariado: document.getElementById('natural_negocio_celular').value,
                    actividad_economica_no_asalariado: document.getElementById('natural_actividad').value,
                    antiguedad_no_asalariado: document.getElementById('natural_antiguedad').value,
                    cargo_no_asalariado: document.getElementById('natural_cargo').value,
                    correo_no_asalariado: document.getElementById('natural_correo').value
                };
                formData.append('actividad_economica_no_asalariado', JSON.stringify(noAsalariadoData));
            }

            // Información Financiera
            const infoFinanciera = {
                ingresos_anuales: document.getElementById('natural_ingresos_anuales').value,
                ingresos_mensuales: document.getElementById('natural_ingresos_mensuales').value,
                egresos_anuales: document.getElementById('natural_egresos_anuales').value,
                egresos_mensuales: document.getElementById('natural_egresos_mensuales').value,
                otros_ingresos_anuales: document.getElementById('natural_otros_ingresos_anuales').value,
                otros_ingresos_mensuales: document.getElementById('natural_otros_ingresos_mensuales').value,
                total_ingresos_anuales: document.getElementById('natural_total_ingresos_anuales').value,
                total_ingresos_mensuales: document.getElementById('natural_total_ingresos_mensuales').value,
                total_activos: document.getElementById('natural_total_activos').value,
                total_pasivos: document.getElementById('natural_total_pasivos').value,
                concepto_otros_ingresos: document.getElementById('natural_concepto_otros_ingresos').value,
                concepto_patrimonio: document.getElementById('natural_concepto_patrimonio').value
            };

            formData.append('informacion_financiera', JSON.stringify(infoFinanciera));

            // Origen de Fondos
            // const origenFondos = {
            //     fuente_fondos: document.getElementById('natural_fuente_fondos').value,
            //     descripcion_fondos: document.getElementById('natural_descripcion_fondos').value
            // };
            // formData.append('declaracion_origen_fondos', JSON.stringify(origenFondos));

            // Operaciones Internacionales
            // const operacionesIntl = {
            //     realiza_operaciones: document.getElementById('natural_realiza_operaciones').checked
            // };
            // formData.append('actividad_operaciones_internacionales', JSON.stringify(operacionesIntl));

            // Personas PEPs
            const personasPEPs = {
                maneja_recursos_publicos: document.getElementById('natural_pep1').checked,
                ejerce_poder_publico: document.getElementById('natural_pep2').checked,
                reconocimiento_publico: document.getElementById('natural_pep3').checked,
                vinculo_persona_pep: document.getElementById('natural_pep4').checked,
                obligaciones_tributarias_extranjero: document.getElementById('natural_pep5').checked
            };
            formData.append('personas_peps', JSON.stringify(personasPEPs));

            // Archivos
            // const fileInputs = [
            //     'cert_bancaria_ext', 'nss_doc_identidad', 'cert_bancaria',
            //     'ref_comercial_laboral', 'cert_universitarios', 'fotocopia_rut',
            //     'hoja_vida', 'doc_identidad_150'
            // ];
            const fileInputs = [
                'cert_bancaria',
                'ref_comercial_laboral', 'fotocopia_rut',
                'hoja_vida', 'doc_identidad_150'
            ];


            fileInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input.files) {
                    for (let i = 0; i < input.files.length; i++) {
                        formData.append(inputId, input.files[i]);
                    }
                }
            });
        }

        if (tipoPersona === 'juridica') {
            // Datos de la empresa (del código anterior)
            const datosEmpresa = {
                razon_social: document.getElementById('juridica_razon_social').value,
                nit: document.getElementById('juridica_nit').value,
                sector_economico: document.getElementById('juridica_sector_economico').value,
                coordinacion: document.getElementById('juridica_coordinacion').value,
                CIIU: document.getElementById('juridica_ciiu').value,
                descripcion_actividad: document.getElementById('juridica_descripcion_actividad').value,
                clase_sociedad: document.getElementById('juridica_clase_sociedad').value,
                representante_legal: {
                    nombre: document.getElementById('juridica_representante_nombre').value,
                    nacionalidad: document.getElementById('juridica_representante_nacionalidad').value,
                    tipo_documento: document.getElementById('juridica_representante_tipo_documento').value,
                    numero_documento: document.getElementById('juridica_representante_numero_documento').value,
                    fecha_expedicion: document.getElementById('juridica_representante_fecha_expedicion').value,
                    lugar_expedicion: document.getElementById('juridica_representante_lugar_expedicion').value,
                    lugar_nacimiento: document.getElementById('juridica_representante_lugar_nacimiento').value,
                    fecha_nacimiento: document.getElementById('juridica_representante_fecha_nacimiento').value,
                    direccion: document.getElementById('juridica_direccion_empresa').value,
                    telefono: document.getElementById('juridica_telefono').value
                },
                regimen_comun: document.getElementById('juridica_regimen_comun').checked,
                regimen_simplificado: document.getElementById('juridica_regimen_simplificado').checked,
                declara_renta: document.getElementById('juridica_declara_renta').checked,
                autoretenedores: {
                    es_autoretenedor: document.getElementById('juridica_es_autoretenedor').checked,
                    numero_resolucion: document.getElementById('juridica_es_autoretenedor').checked
                        ? document.getElementById('juridica_autoretenedor_resolucion').value
                        : ""
                },
                grandes_contribuyentes: {
                    es_grande_contribuyente: document.getElementById('juridica_es_grande_contribuyente').checked,
                    numero_resolucion: document.getElementById('juridica_es_grande_contribuyente').checked
                        ? document.getElementById('juridica_gran_contribuyente_resolucion').value
                        : ""
                },
                correo_electronico: document.getElementById('juridica_correo').value,
                pagina_web: document.getElementById('juridica_pagina_web').value,
                direccion_empresa: document.getElementById('juridica_direccion_empresa').value,
                ciudad: document.getElementById('juridica_ciudad').value,
                pais: 'Colombia', // Asumiendo campo fijo
                telefono: document.getElementById('juridica_telefono').value,
                AA: document.getElementById('juridica_aa').value,
                fax: document.getElementById('juridica_fax').value,
                contacto_comercial: {
                    nombre: document.getElementById('contacto_comercial_nombre').value,
                    telefono: document.getElementById('contacto_comercial_telefono').value,
                    correo: document.getElementById('contacto_comercial_correo').value
                },
                contacto_contabilidad: {
                    nombre: document.getElementById('contacto_contabilidad_nombre').value,
                    telefono: document.getElementById('contacto_contabilidad_telefono').value,
                    correo: document.getElementById('contacto_contabilidad_correo').value
                },
                numero_cuenta: document.getElementById('juridica_numero_cuenta').value,
                tipo_cuenta: document.getElementById('juridica_tipo_cuenta').value,
                banco: document.getElementById('juridica_banco').value,
            };
            // Información Financiera
            const infoFinanciera = {
                ingresos_mensuales: {
                    ventas: Number(document.getElementById('juridica_ingresos_ventas').value),
                    otros: Number(document.getElementById('juridica_ingresos_otros').value),
                    cual: document.getElementById('juridica_ingresos_cual').value
                },
                otros_ingresos_no_operacionales: Number(document.getElementById('juridica_ingresos_no_operacionales').value),
                fecha_corte: document.getElementById('juridica_fecha_corte').value,
                egresos_mensuales: Number(document.getElementById('juridica_egresos').value),
                total_activos: Number(document.getElementById('juridica_total_activos').value),
                total_pasivos: Number(document.getElementById('juridica_total_pasivos').value)
            };


            // PEPs Empresa
            const personasPEPs = {
                maneja_recursos_publicos: document.getElementById('pep1').checked,
                ejerce_poder_publico: document.getElementById('pep2').checked,
                reconocimiento_publico: document.getElementById('pep3').checked,
                vinculo_persona_pep: document.getElementById('pep4').checked,
                obligaciones_tributarias_extranjero: document.getElementById('pep5').checked
            };

            // Transacciones Moneda Extranjera
            const transacciones = {
                realiza_transacciones: document.querySelector('input[name="realiza_transacciones"]:checked').value === 'true',
                productos: [], // Agregar lógica para productos dinámicos
                operaciones_internacionales: {
                    giros: document.getElementById('giros').checked,
                    importacion: document.getElementById('importacion').checked,
                    exportacion: document.getElementById('exportacion').checked,
                    prestamos: document.getElementById('prestamos').checked,
                    pagos_servicios: document.getElementById('pagos_servicios').checked,
                    otros: document.getElementById('otros_operaciones').checked
                }
            };
            // Obtener productos solo si realiza transacciones
            if (transacciones.realiza_transacciones) {
                const productos = document.querySelectorAll('#productos-container .producto');

                productos.forEach(producto => {
                    const inputs = producto.querySelectorAll('input');
                    const productoData = {};

                    inputs.forEach(input => {
                        const field = input.name.split('][')[1].replace('producto_', '');
                        productoData[field] = input.type === 'number' ? parseFloat(input.value) : input.value;
                    });

                    transacciones.productos.push(productoData);
                });
            }


            // Agregar datos estructurados
            formData.append('datos_empresa', JSON.stringify(datosEmpresa));
            formData.append('informacion_financiera_empresa', JSON.stringify(infoFinanciera));
            formData.append('personas_peps_empresa', JSON.stringify(personasPEPs));
            formData.append('transacciones_moneda_extranjera', JSON.stringify(transacciones));

            // Agregar referencias y sucursales
            const referenciasData = getReferenciasData();
            const sucursalesData = getSucursalesData();



            formData.append('informacion_sucursales', JSON.stringify(sucursalesData));
            formData.append('referencias_financieras', JSON.stringify(referenciasData.referencias_financieras));
            formData.append('referencias_proveedores', JSON.stringify(referenciasData.referencias_proveedores));
            formData.append('referencias_comerciales', JSON.stringify(referenciasData.referencias_comerciales));

            // Archivos (ajustar según IDs correctos)
            // const fileFields = [
            //     'cert_bancaria_ext', 'ptin_identificacion_preparador', 'itin_identificacion_contribuyente',
            //     'nss_doc_identidad', 'ein_identificacion_fiscal', 'poder_apoderado',
            //     'declaracion_renta', 'estados_financieros', 'ref_comercial_laboral',
            //     'cert_bancaria', 'cert_existencia_sfc', 'cert_existencia_cc',
            //     'fotocopia_nit_rut', 'doc_identidad_150_rl'
            // ];
            const fileFields = [
                'itin_identificacion_contribuyente',
                'nss_doc_identidad', 'ein_identificacion_fiscal',
                'declaracion_renta', 'estados_financieros', 'ref_comercial_laboral',
                'cert_bancaria', 'cert_existencia_sfc', 'cert_existencia_cc',
                'fotocopia_nit_rut', 'doc_identidad_150_rl'
            ];


            fileFields.forEach(field => {
                const input = document.getElementById(field);
                if (input.files.length > 0) {
                    for (let i = 0; i < input.files.length; i++) {
                        formData.append(field, input.files[i]);
                    }
                }
            });
        }

        return formData;
    }

    // Función inicializadora modificada
    function initReferences(tipo, config) {
        const addButton = document.querySelector(config.addButton);
        const template = document.querySelector(config.template);
        const container = template.parentElement;
        let counter = 1;

        addButton.addEventListener('click', () => {
            const newReference = template.cloneNode(true);
            newReference.classList.remove('referencia-template');
            newReference.classList.add('active-reference');
            newReference.style.display = 'block';

            // Mantener las clases originales
            newReference.querySelectorAll('input').forEach(input => {
                input.value = '';
            });

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-ref';
            removeBtn.innerHTML = '×';
            removeBtn.addEventListener('click', () => newReference.remove());

            newReference.appendChild(removeBtn);
            container.insertBefore(newReference, addButton);
            counter++;
        });
    }

    // Clonar plantilla
    function cloneTemplate() {
        const template = document.querySelector('.sucursal-template');
        const clone = template.cloneNode(true);
        clone.style.display = 'block';

        // Generar IDs únicos
        clone.querySelectorAll('input').forEach(input => {
            const originalClass = Array.from(input.classList).find(c => c.startsWith('sucursal-'));
            input.id = `${originalClass}-${counter}`;
            input.name = `sucursales[${counter}][${originalClass.replace('sucursal-', '')}]`;
        });

        counter++;
        return clone;
    }

    function getReferenciasTipo(tipo, campos) {
        return Array.from(document.querySelectorAll(`.referencia-type[data-tipo="${tipo}"] .active-reference`)).map(ref => {
            const referencia = {};
            campos.forEach(campo => {
                const input = ref.querySelector(`.ref-${tipo}-${campo}`);
                if (!input) return;

                const value = input.value.trim();
                switch (campo) {
                    case 'entidad':
                        referencia[tipo === 'comerciales' ? 'entidad' : 'nombre_entidad'] = value;
                        break;
                    case 'cuenta':
                        referencia['numero_cuenta'] = value;
                        break;
                    default:
                        referencia[campo] = value;
                }
            });
            return referencia;
        }).filter(ref => Object.values(ref).some(v => v !== ''));
    }

    function getSucursalesData() {
        return Array.from(document.querySelectorAll('.sucursales-container > .sucursal-template:not([style*="display: none"])')).map(sucursal => ({
            direccion: sucursal.querySelector('.sucursal-direccion').value,
            ciudad: sucursal.querySelector('.sucursal-ciudad').value,
            pais: sucursal.querySelector('.sucursal-pais').value,
            telefono: sucursal.querySelector('.sucursal-telefono').value,
            AA: sucursal.querySelector('.sucursal-aa').value,
            fax: sucursal.querySelector('.sucursal-fax').value
        }));
    }

    function toggleProductos() {
        const realizaTransacciones = document.querySelector('input[name="realiza_transacciones"]:checked')?.value === 'true';
        const productosGroup = document.getElementById('productos-group');
        const productosContainer = document.getElementById('productos-container');

        if (realizaTransacciones) {
            productosGroup.style.display = 'block';
        } else {
            productosGroup.style.display = 'none';
            productosContainer.innerHTML = '';
            productoCounter = 0;
        }
    }

    function agregarProducto() {
        const container = document.getElementById('productos-container');
        const template = document.getElementById('producto-template');
        const clone = template.content.cloneNode(true);

        const inputs = clone.querySelectorAll('input');
        inputs.forEach(input => {
            const originalName = input.getAttribute('name');
            input.setAttribute('name', `productos[${productoCounter}][${originalName}]`);
        });

        container.appendChild(clone);
        productoCounter++;
        document.querySelector('.remove-producto').addEventListener('click', (e)=> eliminarProducto(e));

    }

    function eliminarProducto(button) {
        const productoDiv = button.closest('.producto');
        productoDiv.remove();
    }



export { initializeForm };