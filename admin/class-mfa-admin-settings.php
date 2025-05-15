<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MFA_Admin_Settings {

    const OPTION_GROUP = 'mfa_option_group';
    const OPTION_NAME = 'mfa_api_options'; // Nombre de la opción donde se guardará todo el array
    const PAGE_SLUG = 'mfa-settings';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_plugin_page' ] );
        add_action( 'admin_init', [ $this, 'page_init' ] );
    }

    public function add_plugin_page() {
        add_options_page(
            __( 'Configuración Nuevo Asesor', 'mi-formulario-api' ), // Título de la página
            __( 'Nuevo asesor', 'mi-formulario-api' ),             // Título del menú
            'manage_options',                                           // Capability
            self::PAGE_SLUG,                                            // Slug del menú
            [ $this, 'create_admin_page' ]                              // Callback para renderizar la página
        );
    }

    public function create_admin_page() {
        // $this->options se usa en la vista si es necesario, pero get_option es más directo en los callbacks
        require_once MFA_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    public function page_init() {
        register_setting(
            self::OPTION_GROUP,          // Option group
            self::OPTION_NAME,           // Option name
            [ $this, 'sanitize_options' ] // Sanitize callback
        );

        add_settings_section(
            'mfa_setting_section_api',                             // ID
            __( 'Configuración de la API Externa', 'mi-formulario-api' ), // Título
            null,                                                  // Callback (opcional)
            self::PAGE_SLUG                                        // Página donde se muestra (debe coincidir con el slug de add_options_page)
        );

        add_settings_field(
            'api_environment',                                    // ID
            __( 'Entorno API', 'mi-formulario-api' ),            // Título
            [ $this, 'api_environment_callback' ],                // Callback
            self::PAGE_SLUG,                                      // Página
            'mfa_setting_section_api'                             // Sección
        );

        add_settings_field(
            'api_dev_url',
            __( 'URL API Desarrollo', 'mi-formulario-api' ),
            [ $this, 'api_dev_url_callback' ],
            self::PAGE_SLUG,
            'mfa_setting_section_api'
        );

        add_settings_field(
            'api_prod_url',
            __( 'URL API Producción', 'mi-formulario-api' ),
            [ $this, 'api_prod_url_callback' ],
            self::PAGE_SLUG,
            'mfa_setting_section_api'
        );

        add_settings_field(
            'api_username',
            __( 'Usuario API', 'mi-formulario-api' ),
            [ $this, 'api_username_callback' ],
            self::PAGE_SLUG,
            'mfa_setting_section_api'
        );

        add_settings_field(
            'api_password',
            __( 'Contraseña API', 'mi-formulario-api' ),
            [ $this, 'api_password_callback' ],
            self::PAGE_SLUG,
            'mfa_setting_section_api'
        );
    }

    public function sanitize_options( $input ) {
        $sanitized_input = [];
        $options = get_option( self::OPTION_NAME, [] ); // Obtener opciones antiguas para no perder campos no enviados

        $sanitized_input['api_environment'] = isset( $input['api_environment'] ) ? sanitize_text_field( $input['api_environment'] ) : ($options['api_environment'] ?? 'dev');
        $sanitized_input['api_dev_url'] = isset( $input['api_dev_url'] ) ? esc_url_raw( trim($input['api_dev_url']) ) : ($options['api_dev_url'] ?? '');
        $sanitized_input['api_prod_url'] = isset( $input['api_prod_url'] ) ? esc_url_raw( trim($input['api_prod_url']) ) : ($options['api_prod_url'] ?? '');
        $sanitized_input['api_username'] = isset( $input['api_username'] ) ? sanitize_text_field( $input['api_username'] ) : ($options['api_username'] ?? '');

        // La contraseña se guarda tal cual, WordPress la maneja. No la mostramos en el value por defecto para no exponerla innecesariamente.
        // Solo actualizar si se ha ingresado una nueva contraseña.
        if ( ! empty( $input['api_password'] ) ) {
            $sanitized_input['api_password'] = $input['api_password'];
        } else {
            // Mantener la contraseña existente si no se proporciona una nueva
            $sanitized_input['api_password'] = $options['api_password'] ?? '';
        }
        return $sanitized_input;
    }

    // Callbacks para los campos
    public function api_environment_callback() {
        $options = get_option( self::OPTION_NAME );
        $current_env = $options['api_environment'] ?? 'dev';
        ?>
        <select id="api_environment" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[api_environment]">
            <option value="dev" <?php selected( $current_env, 'dev' ); ?>><?php esc_html_e( 'Desarrollo', 'mi-formulario-api' ); ?></option>
            <option value="prod" <?php selected( $current_env, 'prod' ); ?>><?php esc_html_e( 'Producción', 'mi-formulario-api' ); ?></option>
        </select>
        <?php
    }

    public function api_dev_url_callback() {
        $options = get_option( self::OPTION_NAME );
        $value = $options['api_dev_url'] ?? '';
        printf(
            '<input type="url" id="api_dev_url" name="%s[api_dev_url]" value="%s" class="regular-text" placeholder="https://dev.api.example.com/v1/" />',
            esc_attr( self::OPTION_NAME ),
            esc_attr( $value )
        );
    }

    public function api_prod_url_callback() {
        $options = get_option( self::OPTION_NAME );
        $value = $options['api_prod_url'] ?? '';
        printf(
            '<input type="url" id="api_prod_url" name="%s[api_prod_url]" value="%s" class="regular-text" placeholder="https://api.example.com/v1/" />',
            esc_attr( self::OPTION_NAME ),
            esc_attr( $value )
        );
    }

    public function api_username_callback() {
        $options = get_option( self::OPTION_NAME );
        $value = $options['api_username'] ?? '';
        printf(
            '<input type="text" id="api_username" name="%s[api_username]" value="%s" class="regular-text" autocomplete="off" />',
            esc_attr( self::OPTION_NAME ),
            esc_attr( $value )
        );
    }

    public function api_password_callback() {
        // No mostramos la contraseña guardada en el campo por seguridad.
        // El usuario debe reingresarla si quiere cambiarla.
        printf(
            '<input type="password" id="api_password" name="%s[api_password]" value="" class="regular-text" autocomplete="new-password" />',
            esc_attr( self::OPTION_NAME )
        );
        echo '<p class="description">' . esc_html__( 'Dejar en blanco para conservar la contraseña actual. La contraseña se almacena de forma segura por WordPress.', 'mi-formulario-api' ) . '</p>';
    }
}