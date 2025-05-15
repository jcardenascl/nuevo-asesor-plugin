<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form method="post" action="options.php">
        <?php
        // Esta función imprime los campos de configuración ocultos necesarios (nonce, action, option_page)
        settings_fields( MFA_Admin_Settings::OPTION_GROUP );

        // Esta función imprime las secciones y campos registrados para esta página
        // El slug debe coincidir con el slug de add_options_page
        do_settings_sections( MFA_Admin_Settings::PAGE_SLUG );

        // Botón de guardar
        submit_button( __( 'Guardar Cambios', 'mi-formulario-api' ) );
        ?>
    </form>
</div>