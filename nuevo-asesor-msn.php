<?php
/**
 * Plugin Name:       Nuevo Asesor
 * Plugin URI:        https://ejemplo.com/plugins/mi-formulario-api/
 * Description:       Muestra y administra un formulario que interactúa con una API externa de forma segura a través de la API REST de WordPress.
 * Version:           1.0.0
 * Author:            Jose Cardenas
 * Author URI:        https://ejemplo.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mi-formulario-api
 * Domain Path:       /languages
 * Requires PHP:      7.2
 * Requires at least: 5.2
 */

// Prevenir acceso directo al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes del plugin
define( 'MFA_VERSION', '1.0.2' );
define( 'MFA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MFA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MFA_MIN_PHP_VERSION', '7.2' );
define( 'MFA_MIN_WP_VERSION', '5.2' );

// Comprobar versiones de PHP y WordPress
if ( version_compare( PHP_VERSION, MFA_MIN_PHP_VERSION, '<' ) ) {
    add_action( 'admin_notices', 'mfa_php_version_notice' );
    return;
}

if ( version_compare( get_bloginfo( 'version' ), MFA_MIN_WP_VERSION, '<' ) ) {
    add_action( 'admin_notices', 'mfa_wp_version_notice' );
    return;
}

function mfa_php_version_notice() {
    ?>
    <div class="error">
        <p><?php printf( esc_html__( 'Mi Formulario API requiere PHP versión %s o superior. Estás usando la versión %s.', 'mi-formulario-api' ), esc_html( MFA_MIN_PHP_VERSION ), esc_html( PHP_VERSION ) ); ?></p>
    </div>
    <?php
}

function mfa_wp_version_notice() {
    ?>
    <div class="error">
        <p><?php printf( esc_html__( 'Mi Formulario API requiere WordPress versión %s o superior. Estás usando la versión %s.', 'mi-formulario-api' ), esc_html( MFA_MIN_WP_VERSION ), esc_html( get_bloginfo( 'version' ) ) ); ?></p>
    </div>
    <?php
}


// Incluir archivos necesarios
require_once MFA_PLUGIN_DIR . 'admin/class-mfa-admin-settings.php';
require_once MFA_PLUGIN_DIR . 'public/class-mfa-shortcode.php';
require_once MFA_PLUGIN_DIR . 'includes/class-mfa-api-handler.php';
require_once MFA_PLUGIN_DIR . 'includes/class-mfa-rest-controller.php';

// Inicializar clases
if ( class_exists( 'MFA_Admin_Settings' ) ) {
    new MFA_Admin_Settings();
}

if ( class_exists( 'MFA_Shortcode' ) ) {
    new MFA_Shortcode();
}

if ( class_exists( 'MFA_REST_Controller' ) ) {
    // La clase MFA_REST_Controller engancha 'rest_api_init' en su constructor
    new MFA_REST_Controller();
}

// Hooks de activación y desactivación (opcional)
register_activation_hook(__FILE__, 'mfa_activate_plugin');
function mfa_activate_plugin() {
    // Código a ejecutar en la activación, ej: setear opciones por defecto
    // Asegurarse de que los roles/capacidades estén configurados si es necesario
    // Borrar transients antiguos si existen
    delete_transient( MFA_API_Handler::TOKEN_TRANSIENT_KEY );
}

register_deactivation_hook(__FILE__, 'mfa_deactivate_plugin');
function mfa_deactivate_plugin() {
    // Código a ejecutar en la desactivación
    // Por ejemplo, borrar transients
    delete_transient( MFA_API_Handler::TOKEN_TRANSIENT_KEY );
}