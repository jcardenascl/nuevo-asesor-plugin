<div id="mfa-form-container" class="mfa-form-container">
    <div id="mfa-loading" class="mfa-message" style="display: none;">
        <?php esc_html_e('Cargando...', 'mi-formulario-api'); ?>
    </div>
    <div id="mfa-error" class="mfa-message mfa-error-message" style="display: none;"></div>
    <div id="mfa-success-message" class="mfa-message mfa-success-message" style="display: none;"></div>

    <form id="mfa-external-api-form">
        <p>
            <label for="mfa-nombre"><?php esc_html_e('Nombre:', 'mi-formulario-api'); ?></label>
            <input type="text" id="mfa-nombre" name="nombre" required>
        </p>

        <p>
            <label for="mfa-email"><?php esc_html_e('Correo Electrónico:', 'mi-formulario-api'); ?></label>
            <input type="email" id="mfa-email" name="email" required>
        </p>

        <p>
            <label for="mfa-opcion-api-1"><?php esc_html_e('Tipo de Documento (desde API):', 'mi-formulario-api'); ?></label>
            <select id="mfa-opcion-api-1" name="tipo_documento">
                <option value=""><?php esc_html_e('Cargando...', 'mi-formulario-api'); ?></option>
            </select>
        </p>

        <p>
            <label for="mfa-opcion-api-2"><?php esc_html_e('Ciudad (desde API):', 'mi-formulario-api'); ?></label>
            <select id="mfa-opcion-api-2" name="ciudad_id">
                <option value=""><?php esc_html_e('Cargando...', 'mi-formulario-api'); ?></option>
            </select>
        </p>

        <p>
            <label for="mfa-mensaje"><?php esc_html_e('Mensaje:', 'mi-formulario-api'); ?></label>
            <textarea id="mfa-mensaje" name="mensaje" rows="4"></textarea>
        </p>


        <button type="submit" id="mfa-submit-button">
            <?php esc_html_e('Enviar Formulario', 'mi-formulario-api'); ?>
        </button>
    </form>
</div>