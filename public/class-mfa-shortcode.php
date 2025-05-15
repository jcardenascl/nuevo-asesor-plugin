<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MFA_Shortcode {

    const SHORTCODE_TAG = 'mi_formulario_api';

    public function __construct() {
        add_shortcode( self::SHORTCODE_TAG, [ $this, 'render_form_shortcode' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_styles' ] );
    }

    public function enqueue_scripts_styles() {
        global $post;

        $load_scripts = false;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, self::SHORTCODE_TAG ) ) {
            $load_scripts = true;
        }
        // También podrías comprobar widgets o campos ACF si el shortcode se usa allí.
        // Para simplificar, si es singular y tiene el shortcode, o si no es admin (para otros casos).
        // Si el shortcode se usa en muchos sitios, es mejor ser más específico o usar `wp_footer` condicionalmente.

        if ( ! is_admin() && ( is_singular() || $load_scripts ) ) { // Carga si es singular o si el shortcode está en el contenido principal

            wp_enqueue_style(
                'mfa-style',
                MFA_PLUGIN_URL . 'public/css/mfa-style.css',
                [],
                MFA_VERSION
            );

            wp_enqueue_script(
                'mfa-api-service',
                MFA_PLUGIN_URL . 'public/js/api-service.js',
                [], // No tiene dependencias directas de otros scripts nuestros aquí
                MFA_VERSION,
                true // Cargar en el footer
            );
            // Informa a WordPress que este script es un módulo ES6
            wp_script_add_data( 'mfa-api-service', 'type', 'module' );


            wp_enqueue_script(
                'mfa-form-handler',
                MFA_PLUGIN_URL . 'public/js/form-handler.js',
                ['mfa-api-service'], // Depende de api-service
                MFA_VERSION,
                true
            );
            wp_script_add_data( 'mfa-form-handler', 'type', 'module' );

            wp_enqueue_script(
                'mfa-main-js', // Handle del script principal
                MFA_PLUGIN_URL . 'public/js/main.js',
                ['mfa-form-handler'], // Depende de form-handler
                MFA_VERSION,
                true
            );
            wp_script_add_data( 'mfa-main-js', 'type', 'module' );


            // Pasar datos de PHP a JavaScript
            // El handle 'mfa-main-js' es donde pasaremos los parámetros,
            // pero los módulos importados también tendrán acceso si se exponen globalmente (no recomendado)
            // o si 'mfa-main-js' los pasa a sus módulos importados (mejor).
            // Para simplificar, 'mfa_params' será global para los módulos en este ejemplo,
            // pero en una app compleja, main.js importaría y pasaría config a otros módulos.
            $js_data = [
                'rest_url'               => esc_url_raw( rest_url( 'mfa/v1/' ) ), // URL base para tus endpoints REST
                'nonce'                  => wp_create_nonce( 'wp_rest' ),        // Nonce para la API REST (acción por defecto)
                'selectDataEndpointBase' => 'select-options/',                  // Parte del path para datos de select
                'submitEndpointPath'     => 'submit-form',                      // Parte del path para submit del formulario
                'text'                   => [                                   // Para textos traducibles en JS
                    'loading'           => __('Cargando...', 'mi-formulario-api'),
                    'select_default'    => __('Seleccione una opción', 'mi-formulario-api'),
                    'submit_button'     => __('Enviar Formulario', 'mi-formulario-api'),
                    'form_error_generic' => __('Ocurrió un error. Por favor, inténtelo de nuevo.', 'mi-formulario-api'),
                ]
            ];

            wp_localize_script( 'mfa-main-js', 'mfa_params', $js_data ); // Pasa los datos a mfa-main-js
        }
    }

    public function render_form_shortcode( $atts ) {
        // $atts son los atributos del shortcode, ej: [mi_formulario_api param="valor"]
        // Por ahora no los usamos, pero podrías usarlos para configurar el formulario.
        // Ejemplo: shortcode_atts( array('config_id' => 'default',), $atts );

        ob_start();
        require MFA_PLUGIN_DIR . 'public/views/form-template.php';
        return ob_get_clean();
    }
}