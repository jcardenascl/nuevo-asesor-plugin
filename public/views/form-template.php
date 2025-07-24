<div id="mfa-form-container " class="mfa-form-container form-container">
    <div id="mfa-loading" class="mfa-message">
        <?php esc_html_e('Cargando...', 'mi-formulario-api'); ?>
    </div>
    <div id="mfa-error" class="mfa-message mfa-error-message" style="display: none;"></div>
    <div id="mfa-success-message" class="mfa-message mfa-success-message" style="display: none;"></div>
    <div id="progressBar">
        <div id="progress"></div>
    </div>
    <div class="loader-container">
        <div class="loader"></div>
    </div>
    <div id="errorMessages" class="error-message"></div>


    <form id="mfa-external-api-form" novalidate>

                <!-- Paso 1 -->
        <div class="form-step firts" data-step="1">
            <p>
                <?php esc_html_e('¿El nuevo asesor será persona jurídica o persona natural?', 'mi-formulario-api'); ?>
            </p>
            <select name="tipo_persona" id="tipoPersona" required>
                <option value=""><?php esc_html_e('Seleccione tipo persona', 'mi-formulario-api'); ?></option>
                <option value="natural"><?php esc_html_e('Persona Natural', 'mi-formulario-api'); ?></option>
                <option value="juridica"><?php esc_html_e('Persona Jurídica', 'mi-formulario-api'); ?></option>
            </select>
            <div class="content-btn">
                <button type="button" class="js-next-step">
                    <?php esc_html_e('Siguiente', 'mi-formulario-api'); ?>
                </button>
            </div>

        </div>

        <!-- Paso 2 -->
        <div class="form-step" data-step="2">
            <!-- Campos para Persona Natural -->
            <div id="naturalFields" style="display:none;">
                <!-- Datos Personales -->
                <fieldset>
                   <legend><?php esc_html_e('Datos Personales', 'mi-formulario-api'); ?></legend>
                   <div class="form-grid">
                      <!-- Fila 1 -->
                      <div class="form-group">
                         <label for="natural_primer_nombre">Primer nombre</label>
                         <input type="text" id="natural_primer_nombre" name="natural_primer_nombre" required>
                      </div>
                      <div class="form-group">
                         <label for="natural_segundo_nombre">Segundo nombre</label>
                         <input type="text" id="natural_segundo_nombre" name="natural_segundo_nombre">
                      </div>
                      <div class="form-group">
                         <label for="natural_primer_apellido">Primer apellido</label>
                         <input type="text" id="natural_primer_apellido" name="natural_primer_apellido" required>
                      </div>
                      <div class="form-group">
                         <label for="natural_segundo_apellido">Segundo apellido</label>
                         <input type="text" id="natural_segundo_apellido" name="natural_segundo_apellido">
                      </div>
                      <!-- Fila 2 -->
                      <div class="form-group">
                         <label for="natural_tipo_documento">Tipo de documento</label>
                         <select id="natural_tipo_documento" name="natural_tipo_documento" required>
                            <option value="">Seleccione</option>
                            <option value="C.C.">Cédula de Ciudadanía</option>
                            <option value="C.E.">Cédula de Extranjería</option>
                            <option value="Pasaporte">Pasaporte</option>
                         </select>
                      </div>
                      <div class="form-group">
                         <label for="natural_numero_documento">Número de documento</label>
                         <input type="text" id="natural_numero_documento" name="natural_numero_documento" required>
                      </div>
                      <div class="form-group">
                         <label for="natural_fecha_expedicion">Fecha de expedición</label>
                         <input type="date" id="natural_fecha_expedicion" name="natural_fecha_expedicion" required>
                      </div>
                      <div class="form-group">
                         <label for="natural_lugar_expedicion">Lugar de expedición</label>
                         <select id="natural_lugar_expedicion" name="natural_lugar_expedicion" required>
                            <option value="">Seleccione</option>
                         </select>
                      </div>
                      <!-- Fila 3 -->
                      <div class="form-group">
                         <label for="natural_fecha_nacimiento">Fecha de nacimiento</label>
                         <input type="date" id="natural_fecha_nacimiento" name="natural_fecha_nacimiento" required>
                      </div>
                      <div class="form-group">
                         <label for="natural_lugar_nacimiento">Lugar de nacimiento</label>
                         <select id="natural_lugar_nacimiento" name="natural_lugar_nacimiento" required>
                            <option value="">Seleccione</option>
                         </select>
                      </div>
                      <div class="form-group">
                         <label for="natural_sexo">Sexo</label>
                         <select id="natural_sexo" name="natural_sexo" required>
                            <option value="">Seleccione</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                         </select>
                      </div>
                      <div class="form-group">
                         <label for="natural_nacionalidad">Nacionalidad</label>
                         <input type="text" id="natural_nacionalidad" name="natural_nacionalidad" required>
                      </div>
                      <!-- Fila 4 -->
                      <div class="form-group">
                         <label for="natural_ciudad_residencia">Ciudad de residencia</label>
                         <select id="natural_ciudad_residencia" name="natural_ciudad_residencia" required>
                            <option value="">Seleccione</option>
                         </select>
                      </div>
                      <div class="form-group">
                         <label for="natural_direccion">Dirección de residencia</label>
                         <input type="text" id="natural_direccion" name="natural_direccion" required>
                      </div>
                      <div class="form-group">
                         <label for="natural_correo">Correo electrónico</label>
                         <input type="email" id="natural_correo" name="natural_correo" required>
                      </div>
                      <div class="form-group">
                         <label for="natural_celular">Celular</label>
                         <input type="tel" id="natural_celular" name="natural_celular" pattern="[0-9]{10}" required>
                      </div>
                      <!-- Fila 5 -->
                      <div class="form-group">
                         <label for="natural_estado_civil">Estado civil</label>
                         <select id="natural_estado_civil" name="natural_estado_civil" required>
                            <option value="">Seleccione</option>
                            <option value="S">Soltero</option>
                            <option value="C">Casado</option>
                            <option value="UL">Union Libre</option>
                            <option value="D">Divorciado</option>
                            <option value="V">Viudo</option>
                         </select>
                      </div>
                      <div class="form-group">
                         <label for="natural_ocupacion">Ocupación</label>
                         <input type="text" id="natural_ocupacion" name="natural_ocupacion" required>
                      </div>
                      <div class="form-group">
                         <label for="natural_tipo_vivienda">Tipo de vivienda</label>
                         <select id="natural_tipo_vivienda" name="natural_tipo_vivienda" required>
                            <option value="">Seleccione</option>
                            <option value="A">Arrendada</option>
                            <option value="P">Propia</option>
                            <option value="F">Familiar</option>
                         </select>
                      </div>
                      <div class="form-group">
                         <label for="natural_nivel_estudio">Nivel de estudio</label>
                         <select id="natural_nivel_estudio" name="natural_nivel_estudio" required>
                            <option value="">Seleccione</option>
                            <option value="P">Primaria</option>
                            <option value="S">Bachillerato</option>
                            <option value="TE">Técnico</option>
                            <option value="T">Tecnólogo</option>
                            <option value="U">Universitario</option>
                            <option value="PO">Especialista</option>

                         </select>
                      </div>
                      <!-- Fila 6 -->
                      <div class="form-group">
                         <label for="natural_banco">Banco</label>
                         <select id="natural_banco" name="natural_banco" required>
                            <option value="">Seleccione</option>
                         </select>
                      </div>
                      <div class="form-group">
                         <label for="natural_tipo_cuenta">Tipo de cuenta</label>
                         <select id="natural_tipo_cuenta" name="natural_tipo_cuenta" required>
                            <option value="">Seleccione</option>
                            <option value="C">Corriente</option>
                            <option value="A">Ahorro</option>
                         </select>
                      </div>
                      <div class="form-group">
                         <label for="natural_numero_cuenta">Número de cuenta</label>
                         <input type="number" id="natural_numero_cuenta" name="natural_numero_cuenta" required>
                      </div>
                      <div class="form-group">
                         <label for="natural_coordinacion">Coordinación</label>
                         <select id="natural_coordinacion" name="natural_coordinacion" required>
                            <option value="">Seleccione</option>
                         </select>
                      </div>
                   </div>
                    <div class="content-btn" style="margin-top: 20px;">
                        <button type="button" id="button_activate_apoderado" class="add-apoderado" >Registrar Apoderado</button>
                        <button type="button" id="button_remove_apoderado" class="remove-apoderado" style="display: none;">Remover Apoderado</button>
                    </div>
                </fieldset>

                <!-- Datos Cónyuge -->
                <fieldset class="conyuge-section" style="display: none;">
                    <legend>Datos del Cónyuge</legend>
                    <div class="form-grid">
                        <!-- Fila 1 -->
                        <div class="form-group">
                            <label for="conyuge_primer_nombre">Primer nombre:</label>
                            <input data-conyuge type="text" id="conyuge_primer_nombre" name="conyuge_primer_nombre" placeholder="Primer nombre">
                        </div>
                        <div class="form-group">
                            <label for="conyuge_segundo_nombre">Segundo nombre:</label>
                            <input data-conyuge  data-optional type="text" id="conyuge_segundo_nombre" name="conyuge_segundo_nombre" placeholder="Segundo nombre">
                        </div>
                        <div class="form-group">
                            <label for="conyuge_primer_apellido">Primer apellido:</label>
                            <input data-conyuge  type="text" id="conyuge_primer_apellido" name="conyuge_primer_apellido" placeholder="Primer apellido">
                        </div>
                        <div class="form-group">
                            <label for="conyuge_segundo_apellido">Segundo apellido:</label>
                            <input data-conyuge data-optional type="text" id="conyuge_segundo_apellido" name="conyuge_segundo_apellido" placeholder="Segundo apellido">
                        </div>

                        <!-- Fila 2 -->
                        <div class="form-group">
                            <label for="conyuge_tipo_documento">Tipo de identificación:</label>
                            <select data-conyuge  id="conyuge_tipo_documento" name="conyuge_tipo_documento">
                                <option value="">Seleccione</option>
                                <option value="C.C.">Cédula de Ciudadanía</option>
                                <option value="C.E.">Cédula de Extranjería</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="conyuge_numero_documento">Número de identificación:</label>
                            <input data-conyuge  type="text" id="conyuge_numero_documento" name="conyuge_numero_documento" placeholder="Número de documento">
                        </div>
                        <div class="form-group">
                            <label for="conyuge_fecha_nacimiento">Fecha de nacimiento:</label>
                            <input data-conyuge  type="date" id="conyuge_fecha_nacimiento" name="conyuge_fecha_nacimiento">
                        </div>
                        <div class="form-group">
                            <label for="conyuge_lugar_nacimiento">Lugar de nacimiento</label>
                            <select data-conyuge  id="conyuge_lugar_nacimiento" name="conyuge_lugar_nacimiento">
                                <option value="">Seleccione</option>
                            </select>
                        </div>

                        <!-- Fila 3 -->
                        <div class="form-group">
                            <label for="conyuge_empresa">Empresa donde trabaja:</label>
                            <input data-conyuge  type="text" id="conyuge_empresa" name="conyuge_empresa" placeholder="Empresa">
                        </div>
                        <div class="form-group">
                            <label for="conyuge_antiguedad">Antigüedad (años):</label>
                            <input data-conyuge  type="number" id="conyuge_antiguedad" name="conyuge_antiguedad" min="0" placeholder="Años">
                        </div>
                        <div class="form-group">
                            <label for="conyuge_cargo">Cargo u ocupación:</label>
                            <input data-conyuge  type="text" id="conyuge_cargo" name="conyuge_cargo" placeholder="Cargo">
                        </div>
                        <div></div>

                        <!-- Fila 4 -->
                        <div class="form-group">
                            <label for="conyuge_ciudad_residencia">Ciudad de residencia:</label>
                            <select data-conyuge id="conyuge_ciudad_residencia" name="conyuge_ciudad_residencia">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="conyuge_direccion">Dirección de residencia:</label>
                            <input data-conyuge  type="text" id="conyuge_direccion" name="conyuge_direccion" placeholder="Dirección">
                        </div>
                        <div class="form-group">
                            <label for="conyuge_celular">Celular:</label>
                            <input data-conyuge  type="tel" id="conyuge_celular" name="conyuge_celular" placeholder="Celular" pattern="[0-9]{10}">
                        </div>
                        <div class="form-group">
                            <label for="conyuge_correo">Correo electrónico:</label>
                            <input data-conyuge  type="email" id="conyuge_correo" name="conyuge_correo" placeholder="Correo">
                        </div>
                    </div>
                </fieldset>

                <!-- Datos Apoderado -->
                <fieldset class="apoderado-section" style="display: none;">
                    <legend>Datos del Apoderado</legend>
                    <div class="form-grid">
                        <!-- Fila 1 -->
                        <div class="form-group">
                            <label for="natural_apoderado_primer_nombre">Primer nombre:</label>
                            <input data-apoderado type="text" id="natural_apoderado_primer_nombre" name="natural_apoderado_primer_nombre" placeholder="Primer nombre">
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_segundo_nombre">Segundo nombre:</label>
                            <input data-apoderado data-optional type="text" id="natural_apoderado_segundo_nombre" name="natural_apoderado_segundo_nombre" placeholder="Segundo nombre">
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_primer_apellido">Primer apellido:</label>
                            <input data-apoderado type="text" id="natural_apoderado_primer_apellido" name="natural_apoderado_primer_apellido" placeholder="Primer apellido">
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_segundo_apellido">Segundo apellido:</label>
                            <input data-apoderado data-optional type="text" id="natural_apoderado_segundo_apellido" name="natural_apoderado_segundo_apellido" placeholder="Segundo apellido">
                        </div>
                        <!-- Fila 2 -->
                        <div class="form-group">
                            <label for="natural_apoderado_tipo_documento">Tipo de identificación:</label>
                            <select data-apoderado id="natural_apoderado_tipo_documento" name="natural_apoderado_tipo_documento">
                                <option value="">Tipo de documento</option>
                                <option value="C.C.">Cédula de Ciudadanía</option>
                                <option value="C.E.">Cédula de Extranjería</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_numero_documento">Número de identificación:</label>
                            <input data-apoderado type="text" id="natural_apoderado_numero_documento" name="natural_apoderado_numero_documento" placeholder="Número de documento">
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_fecha_nacimiento">Fecha de nacimiento:</label>
                            <input data-apoderado type="date" id="natural_apoderado_fecha_nacimiento" name="natural_apoderado_fecha_nacimiento">
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_lugar_nacimiento">Lugar de nacimiento</label>
                            <select data-apoderado id="natural_apoderado_lugar_nacimiento" name="natural_apoderado_lugar_nacimiento" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <!-- Fila 3 -->
                        <div class="form-group">
                            <label for="natural_apoderado_empresa">Empresa donde trabaja:</label>
                            <input data-apoderado type="text" id="natural_apoderado_empresa" name="natural_apoderado_empresa" placeholder="Empresa">
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_antiguedad">Antigüedad (años):</label>
                            <input data-apoderado type="number" id="natural_apoderado_antiguedad" name="natural_apoderado_antiguedad" min="0" placeholder="Años">
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_cargo">Cargo u ocupación:</label>
                            <input data-apoderado type="text" id="natural_apoderado_cargo" name="natural_apoderado_cargo" placeholder="Cargo">
                        </div>
                        <div></div>
                        <!-- Espacio vacío -->
                        <!-- Fila 4 -->
                        <div class="form-group">
                            <label for="natural_apoderado_ciudad_residencia">Ciudad de residencia:</label>
                            <select data-apoderado id="natural_apoderado_ciudad_residencia" name="natural_apoderado_ciudad_residencia" required>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_direccion">Dirección de residencia:</label>
                            <input data-apoderado type="text" id="natural_apoderado_direccion" name="natural_apoderado_direccion" placeholder="Dirección">
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_celular">Celular:</label>
                            <input data-apoderado type="tel" id="natural_apoderado_celular" name="natural_apoderado_celular" placeholder="Celular" pattern="[0-9]{10}">
                        </div>
                        <div class="form-group">
                            <label for="natural_apoderado_correo">Correo electrónico:</label>
                            <input data-apoderado type="email" id="natural_apoderado_correo" name="natural_apoderado_correo" placeholder="Correo">
                        </div>
                    </div>
                </fieldset>

            </div>

            <!-- Campos para Persona Jurídica -->
            <div id="juridicaFields" style="display:none;">
                <!-- Datos de la Empresa -->
                <fieldset>
                    <legend>Datos de la Empresa</legend>
                    <div class="form-columns">
                        <div>
                            <div>
                                <label for="juridica_razon_social">Razón Social:</label>
                                <input type="text" id="juridica_razon_social" placeholder="Razón Social" required>
                            </div>
                            <div>
                                <label for="juridica_nit">NIT:</label>
                                <input type="number" id="juridica_nit" placeholder="NIT" pattern="[0-9]{9}-[0-9]"
                                    required>
                            </div>
                            <div>
                                <label for="juridica_sector_economico">Sector Económico:</label>
                                <input type="text" id="juridica_sector_economico" placeholder="Sector Económico"
                                    required>
                            </div>
                            <div>
                                <label for="juridica_coordinacion"><?php esc_html_e('Coordinación:','mi-formulario-api'); ?></label>
                                <select id="juridica_coordinacion" required>
                                    <option value=""><?php esc_html_e('Coordinación','mi-formulario-api'); ?> </option>
                                </select>
                            </div>
                            <div>
                                <label for="juridica_ciiu">Código CIIU:</label>
                                <input type="text" id="juridica_ciiu" placeholder="Código CIIU" required>
                            </div>
                            <div>
                                <label for="juridica_correo">Correo electrónico:</label>
                                <input type="email" id="juridica_correo" placeholder="Correo electrónico" required>
                            </div>

                            <div>
                                <label for="juridica_direccion_empresa">Dirección principal:</label>
                                <input type="text" id="juridica_direccion_empresa" placeholder="Dirección principal"
                                    required>
                            </div>
                            <div>
                                <label for="juridica_ciudad">Ciudad:</label>
                                <select id="juridica_ciudad" required>
                                    <option value=""><?php esc_html_e('Ciudad','mi-formulario-api'); ?> </option>
                                </select>
                            </div>

                        </div>
                        <div>

                            <div>
                                <label for="juridica_clase_sociedad">Clase de Sociedad:</label>
                                <select id="juridica_clase_sociedad" required>
                                    <option value="">Clase de Sociedad</option>
                                    <option value="SAS">SAS</option>
                                    <option value="LTDA">LTDA</option>
                                    <option value="SA">SA</option>
                                </select>
                            </div>
                            <div>
                                <label for="juridica_telefono">Teléfono principal:</label>
                                <input type="tel" id="juridica_telefono" placeholder="Teléfono principal" required>
                            </div>
                            <div>
                                <label for="juridica_fax">Fax:</label>
                                <input type="text" id="juridica_fax" placeholder="Fax">
                            </div>
                            <!-- Campo País (fijo para Colombia) -->
                            <div>
                                <label for="juridica_pais">País:</label>
                                <input type="text" id="juridica_pais" placeholder="País">
                            </div>

                            <!-- Campo AA -->
                            <div>
                                <label for="juridica_aa">Código AA:</label>
                                <input type="text" id="juridica_aa" placeholder="Ej: A123" required>
                            </div>
                            <div>
                                <label for="juridica_pagina_web">Página web:</label>
                                <input type="url" id="juridica_pagina_web" placeholder="Página web">
                            </div>
                        </div>
                        <div>
                            <div>
                                <label for="juridica_descripcion_actividad">Descripción de la actividad
                                    principal:</label>
                                <textarea id="juridica_descripcion_actividad"
                                    placeholder="Descripción de la actividad principal" required></textarea>
                            </div>
                            <div class="regimen-selector">
                                <label><input type="checkbox" id="juridica_regimen_comun"> Régimen Común</label>
                                <label><input type="checkbox" id="juridica_regimen_simplificado"> Simplificado</label>
                                <label><input type="checkbox" id="juridica_declara_renta"> Declara Renta</label>
                            </div>
                            <!-- Autoretenedores -->
                            <div class="regimen-selector">
                                <label><input type="checkbox" id="juridica_es_autoretenedor"> ¿Es autoretenedor?</label>
                                <div id="autoretenedor-details" style="display: none;">
                                    <input type="text" id="juridica_autoretenedor_resolucion"
                                        placeholder="Número de resolución" required>
                                </div>
                            </div>

                            <!-- Grandes Contribuyentes -->
                            <div class="regimen-selector">
                                <label><input type="checkbox" id="juridica_es_grande_contribuyente"> ¿Es gran
                                    contribuyente?</label>
                                <div id="gran-contribuyente-details" style="display: none;">
                                    <input type="text" id="juridica_gran_contribuyente_resolucion"
                                        placeholder="Número de resolución" required>
                                </div>
                            </div>

                            <div>
                                <label for="juridica_numero_cuenta">Número de Cuenta:</label>
                                <input type="number" id="juridica_numero_cuenta" placeholder="Número de cuenta"
                                    required>
                            </div>
                            <div>
                                <label for="juridica_tipo_cuenta">Tipo de cuenta:</label>
                                <select id="juridica_tipo_cuenta" required>
                                    <option value="">Tipo de cuenta</option>
                                    <option value="corriente">Corriente</option>
                                    <option value="ahorro">Ahorro</option>
                                </select>
                            </div>
                            <div>
                                <label for="juridica_banco">Banco:</label>
                                <select id="juridica_banco" required>
                                    <option value="">Banco:</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Representante Legal -->
                    <div class="sub-section">
                        <h4>Representante Legal</h4>
                        <div class="form-columns">
                            <div>
                                <div>
                                    <label for="juridica_representante_nombre">Nombre completo:</label>
                                    <input type="text" id="juridica_representante_nombre" placeholder="Nombre completo"
                                        required>
                                </div>
                                <div>
                                    <label for="juridica_representante_nacionalidad">Nacionalidad:</label>
                                    <input type="text" id="juridica_representante_nacionalidad"
                                        placeholder="Nacionalidad" required>
                                </div>
                                <div>
                                    <label for="juridica_representante_tipo_documento">Tipo documento:</label>
                                    <select id="juridica_representante_tipo_documento" required>
                                        <option value="">Tipo documento</option>
                                        <option value="C.C.">C.C.</option>
                                        <option value="C.E.">C.E.</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                
                                <div>
                                    <label for="juridica_representante_numero_documento">Número documento:</label>
                                    <input type="text" id="juridica_representante_numero_documento"
                                        placeholder="Número documento" required>
                                </div>
                                <div>
                                    <label for="juridica_representante_fecha_expedicion">Fecha expedición
                                        documento:</label>
                                    <input type="date" id="juridica_representante_fecha_expedicion"
                                        placeholder="Fecha expedición documento" required>
                                </div>
                                <div>
                                    <label for="juridica_representante_lugar_expedicion"><?php esc_html_e('Lugar de expedición:','mi-formulario-api'); ?> </label>
                                    <select id="juridica_representante_lugar_expedicion" required>
                                        <option value=""><?php esc_html_e('Lugar de expedición','mi-formulario-api'); ?> </option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                
                                <div>
                                    <label for="juridica_representante_lugar_nacimiento">Lugar de nacimiento:</label>
                                    <select id="juridica_representante_lugar_nacimiento" required>
                                        <option value=""><?php esc_html_e('Lugar de nacimiento','mi-formulario-api'); ?> </option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="juridica_representante_fecha_nacimiento"><?php esc_html_e('Fecha de nacimiento:','mi-formulario-api'); ?></label>
                                    <input type="date" id="juridica_representante_fecha_nacimiento" required>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="sub-section">
                        <!-- Sección de Contactos Específicos -->
                        <div class="form-columns">
                            <!-- Contacto Comercial -->
                            <div class="contacto-section">
                                <h5>Contacto Comercial</h5>
                                <div>
                                    <label for="contacto_comercial_nombre">Nombre completo:</label>
                                    <input type="text" id="contacto_comercial_nombre"
                                        placeholder="Nombre contacto comercial" required>
                                </div>
                                <div>
                                    <label for="contacto_comercial_telefono">Teléfono:</label>
                                    <input type="tel" id="contacto_comercial_telefono"
                                        placeholder="Teléfono contacto comercial" pattern="[0-9]{10}" required>
                                </div>
                                <div>
                                    <label for="contacto_comercial_correo">Correo:</label>
                                    <input type="email" id="contacto_comercial_correo"
                                        placeholder="Correo contacto comercial" required>
                                </div>
                            </div>

                            <!-- Contacto Contabilidad -->
                            <div class="contacto-section">
                                <h5>Contacto Contabilidad</h5>
                                <div>
                                    <label for="contacto_contabilidad_nombre">Nombre completo:</label>
                                    <input type="text" id="contacto_contabilidad_nombre"
                                        placeholder="Nombre contacto contabilidad" required>
                                </div>
                                <div>
                                    <label for="contacto_contabilidad_telefono">Teléfono:</label>
                                    <input type="tel" id="contacto_contabilidad_telefono"
                                        placeholder="Teléfono contacto contabilidad" pattern="[0-9]{10}" required>
                                </div>
                                <div>
                                    <label for="contacto_contabilidad_correo">Correo:</label>
                                    <input type="email" id="contacto_contabilidad_correo"
                                        placeholder="Correo contacto contabilidad" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Sucursales -->
                <fieldset>
                    <legend>Sucursales</legend>
                    <div class="sucursales-container">
                        <div class="sucursal-template" style="display: none;">
                            <div class="form-columns">
                                <div>
                                    <div>
                                        <label>Dirección:</label>
                                        <input type="text" placeholder="Dirección" class="sucursal-direccion">
                                    </div>
                                    <div>
                                        <label>Ciudad:</label>
                                        <input type="text" placeholder="Ciudad" class="sucursal-ciudad">
                                    </div>
                                    <div>
                                        <label>País:</label>
                                        <input type="text" placeholder="País" class="sucursal-pais">
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <label>Teléfono:</label>
                                        <input type="tel" placeholder="Teléfono" class="sucursal-telefono">
                                    </div>
                                    <div>
                                        <label>Código AA:</label>
                                        <input type="text" placeholder="Código AA" class="sucursal-aa">
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <label>Fax:</label>
                                        <input type="text" placeholder="Fax" class="sucursal-fax">
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="remove-sucursal">Eliminar</button>
                        </div>
                        <button type="button" class="add-sucursal">Agregar Sucursal</button>
                    </div>
                </fieldset>

            </div>
            <div class="content-btn">
                <button type="button" class="js-prev-step">
                    <?php esc_html_e('Anterior', 'mi-formulario-api'); ?>
                </button>
                <button type="button" class="js-next-step" >
                    <?php esc_html_e('Siguiente', 'mi-formulario-api'); ?>
                </button>
            </div>
        </div>

        <!-- Paso 3 -->
        <div class="form-step" data-step="3">
            <div id="naturalFields3" style="display:none;">
                <!-- Actividad Económica -->
                <fieldset>
                    <legend>Actividad Económica</legend>
                    <div class="economic-activity-selector">
                        <label><input type="radio" name="actividad_economica" value="asalariado" required>
                            Asalariado</label>
                        <label><input type="radio" name="actividad_economica" value="no_asalariado"> No
                            Asalariado</label>
                    </div>

                    <!-- Campos Asalariado -->
                    <div class="asalariado-fields">
                        <div class="form-grid">
                            <!-- Fila 1 -->
                            <div class="form-group">
                                <label for="natural_empresa">Nombre de la empresa:</label>
                                <input type="text" id="natural_empresa" name="natural_empresa" placeholder="Nombre de la empresa">
                            </div>
                            <div class="form-group">
                                <label for="natural_ciudad_empresa">Ciudad:</label>
                                <select id="natural_ciudad_empresa" name="natural_ciudad_empresa" required>
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="natural_direccion_empresa">Dirección:</label>
                                <input type="text" id="natural_direccion_empresa" name="natural_direccion_empresa" placeholder="Dirección de la empresa">
                            </div>
                            <div class="form-group">
                                <label for="natural_telefono_empresa">Celular:</label>
                                <input type="tel" id="natural_telefono_empresa" name="natural_telefono_empresa" placeholder="Teléfono empresa" pattern="[0-9]{10}">
                            </div>
                            <!-- Fila 2 -->
                            <div class="form-group">
                                <label for="natural_actividad_economica">Actividad económica:</label>
                                <input type="text" id="natural_actividad_economica" name="natural_actividad_economica" placeholder="Actividad económica">
                            </div>
                            <div class="form-group">
                                <label for="natural_antiguedad">Antigüedad (años):</label>
                                <input type="number" id="natural_antiguedad" name="natural_antiguedad" placeholder="Antigüedad en años" min="0">
                            </div>
                            <div class="form-group">
                                <label for="natural_cargo">Cargo:</label>
                                <input type="text" id="natural_cargo" name="natural_cargo" placeholder="Cargo">
                            </div>
                            <div class="form-group">
                                <label for="natural_ingresos_asalariado">Ingresos mensuales:</label>
                                <input type="number" id="natural_ingresos_asalariado" name="natural_ingresos_asalariado" placeholder="Ingresos mensuales (COP)">
                            </div>
                        </div>
                    </div>

                    <!-- Campos No Asalariado -->
                    <div class="no-asalariado-fields" style="display: none;">
                        <div class="form-grid">
                            <!-- Fila 1 -->
                            <div class="form-group">
                                <label for="natural_negocio_nombre">Nombre de la empresa o negocio:</label>
                                <input type="text" id="natural_negocio_nombre" name="natural_negocio_nombre" placeholder="Nombre de la empresa o negocio">
                            </div>
                            <div class="form-group">
                                <label for="natural_negocio_ciudad">Ciudad:</label>
                                <select id="natural_negocio_ciudad" name="natural_negocio_ciudad" required>
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="natural_negocio_direccion">Dirección:</label>
                                <input type="text" id="natural_negocio_direccion" name="natural_negocio_direccion" placeholder="Dirección">
                            </div>
                            <div class="form-group">
                                <label for="natural_negocio_celular">Celular:</label>
                                <input type="tel" id="natural_negocio_celular" name="natural_negocio_celular" placeholder="Celular" pattern="[0-9]{10}">
                            </div>
                            <!-- Fila 2 -->
                            <div class="form-group">
                                <label for="natural_actividad">Actividad económica:</label>
                                <input type="text" id="natural_actividad" name="natural_actividad" placeholder="Actividad económica">
                            </div>
                            <div class="form-group">
                                <label for="natural_antiguedad">Antigüedad (años):</label>
                                <input type="number" id="natural_antiguedad" name="natural_antiguedad" placeholder="Antigüedad en años" min="0">
                            </div>
                            <div class="form-group">
                                <label for="natural_cargo">Cargo:</label>
                                <input type="text" id="natural_cargo" name="natural_cargo" placeholder="Cargo u ocupación">
                            </div>
                            <div class="form-group">
                                <label for="natural_correo">Correo:</label>
                                <input type="email" id="natural_correo" name="natural_correo" placeholder="Correo electrónico">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Información Financiera -->
                <fieldset>
                    <legend>Información Financiera</legend>
                    <div class="form-grid-info">
                        <!-- Fila 1 -->
                        <div class="form-group">
                            <label for="natural_ingresos_anuales">Ingresos anuales:</label>
                            <input type="number" id="natural_ingresos_anuales" name="natural_ingresos_anuales" placeholder="Ingresos anuales (COP)" required>
                        </div>
                        <div class="form-group">
                            <label for="natural_ingresos_mensuales">Ingresos mensuales:</label>
                            <input type="number" id="natural_ingresos_mensuales" name="natural_ingresos_mensuales" placeholder="Ingresos mensuales (COP)" required>
                        </div>
                        <!-- Fila 2 -->
                        <div class="form-group">
                            <label for="natural_egresos_anuales">Egresos anuales:</label>
                            <input type="number" id="natural_egresos_anuales" name="natural_egresos_anuales" placeholder="Egresos anuales (COP)" required>
                        </div>
                        <div class="form-group">
                            <label for="natural_egresos_mensuales">Egresos mensuales:</label>
                            <input type="number" id="natural_egresos_mensuales" name="natural_egresos_mensuales" placeholder="Egresos mensuales (COP)" required>
                        </div>
                        <!-- Fila 3 -->
                        <div class="form-group">
                            <label for="natural_otros_ingresos_anuales">Otros ingresos (anuales):</label>
                            <input type="number" id="natural_otros_ingresos_anuales" name="natural_otros_ingresos_anuales" placeholder="Otros ingresos anuales (COP)">
                        </div>
                        <div class="form-group">
                            <label for="natural_otros_ingresos_mensuales">Otros ingresos (mensuales):</label>
                            <input type="number" id="natural_otros_ingresos_mensuales" name="natural_otros_ingresos_mensuales" placeholder="Otros ingresos mensuales (COP)">
                        </div>
                        <!-- Fila 4 -->
                        <div class="form-group">
                            <label for="natural_total_ingresos_anuales">Total ingresos anuales:</label>
                            <input type="number" id="natural_total_ingresos_anuales" name="natural_total_ingresos_anuales" placeholder="Total ingresos anuales (COP)">
                        </div>
                        <div class="form-group">
                            <label for="natural_total_ingresos_mensuales">Total ingresos mensuales:</label>
                            <input type="number" id="natural_total_ingresos_mensuales" name="natural_total_ingresos_mensuales" placeholder="Total ingresos mensuales (COP)">
                        </div>
                        <!-- Fila 5 -->
                        <div class="form-group">
                            <label for="natural_total_activos">Total activos:</label>
                            <input type="number" id="natural_total_activos" name="natural_total_activos" placeholder="Total activos (COP)" required>
                        </div>
                        <div class="form-group">
                            <label for="natural_total_pasivos">Total pasivos:</label>
                            <input type="number" id="natural_total_pasivos" name="natural_total_pasivos" placeholder="Total pasivos (COP)" required>
                        </div>
                        <!-- Fila 6 -->
                        <div class="form-group">
                            <label for="natural_concepto_otros_ingresos">Concepto otros ingresos:</label>
                            <input type="text" id="natural_concepto_otros_ingresos" name="natural_concepto_otros_ingresos" placeholder="Describa los otros ingresos">
                        </div>
                        <div class="form-group">
                            <label for="natural_concepto_patrimonio">Concepto patrimonio:</label>
                            <input type="text" id="natural_concepto_patrimonio" name="natural_concepto_patrimonio" placeholder="Describa el patrimonio">
                        </div>
                    </div>
                </fieldset>

                <!-- Origen de Fondos -->

                <!-- Operaciones Internacionales -->

                <!-- Personas PEPs -->
                <fieldset>
                    <legend>Declaración PEPs</legend>
                    <div class="form-check-grid check-input">
                        <label><input type="checkbox" id="natural_pep1"> ¿Maneja recursos públicos?</label>
                        <label><input type="checkbox" id="natural_pep2"> ¿Ejerce poder público?</label>
                        <label><input type="checkbox" id="natural_pep3"> ¿Tiene reconocimiento público?</label>
                        <label><input type="checkbox" id="natural_pep4"> ¿Vínculo con persona PEP?</label>
                        <label><input type="checkbox" id="natural_pep5"> ¿Obligaciones tributarias en el
                            extranjero?</label>
                    </div>
                </fieldset>
            </div>

            <!-- Campos para Persona Jurídica -->
            <div id="juridicaFields3" style="display:none;">
                <!-- Información Financiera -->
                <fieldset>
                    <legend>Información Financiera</legend>
                    <div class="form-columns">
                        <div>
                            <h5>Ingresos Mensuales</h5>
                            <div>
                                <label for="juridica_ingresos_ventas">Ventas (COP):</label>
                                <input type="number" id="juridica_ingresos_ventas" placeholder="Ventas (COP)" required>
                            </div>
                            <div>
                                <label for="juridica_ingresos_otros">Otros ingresos (COP):</label>
                                <input type="number" id="juridica_ingresos_otros" placeholder="Otros ingresos (COP)">
                            </div>
                            <div>
                                <label for="juridica_ingresos_cual">Especificar otros ingresos:</label>
                                <input type="text" id="juridica_ingresos_cual" placeholder="Especificar otros ingresos">
                            </div>
                            <div>
                                <label for="juridica_ingresos_no_operacionales">Otros ingresos no operacionales
                                    (COP):</label>
                                <input type="number" id="juridica_ingresos_no_operacionales"
                                    placeholder="Otros ingresos no operacionales (COP)">
                            </div>
                        </div>
                        <div>
                            <h5>Egresos y Patrimonio</h5>
                            <div>
                                <label for="juridica_egresos">Egresos mensuales (COP):</label>
                                <input type="number" id="juridica_egresos" placeholder="Egresos mensuales (COP)"
                                    required>
                            </div>
                            <div>
                                <label for="juridica_total_activos">Total activos (COP):</label>
                                <input type="number" id="juridica_total_activos" placeholder="Total activos (COP)"
                                    required>
                            </div>
                            <div>
                                <label for="juridica_total_pasivos">Total pasivos (COP):</label>
                                <input type="number" id="juridica_total_pasivos" placeholder="Total pasivos (COP)"
                                    required>
                            </div>
                        </div>
                        <div>
                            <h5>Fecha de Corte</h5>
                            <input type="date" id="juridica_fecha_corte" required>
                        </div>
                    </div>
                </fieldset>

                <!-- Referencias -->
                <fieldset>
                    <legend>Referencias Comerciales y Financieras</legend>
                    <div class="referencias-container">
                        <!-- Referencias Financieras -->
                        <div class="referencia-type" data-tipo="financieras">
                            <h4>Financieras</h4>
                            <div class="referencia-template">
                                <div>
                                    <label>Entidad financiera:</label>
                                    <input type="text" class="ref-financieras-entidad" placeholder="Nombre entidad">
                                </div>
                                <div>
                                    <label>Ciudad:</label>
                                    <input type="text" class="ref-financieras-ciudad" placeholder="Ciudad">
                                </div>
                                <div>
                                    <label>Número de cuenta:</label>
                                    <input type="text" class="ref-financieras-cuenta" placeholder="Número de cuenta">
                                </div>
                                <div>
                                    <label>Antigüedad:</label>
                                    <input type="text" class="ref-financieras-antiguedad" placeholder="Ej: 5 años">
                                </div>
                                <div>
                                    <label>Producto:</label>
                                    <input type="text" class="ref-financieras-producto"
                                        placeholder="Ej: Cuenta corriente">
                                </div>
                            </div>
                            <button type="button" class="add-ref-financiera">Agregar Referencia</button>
                        </div>

                        <!-- Referencias Proveedores -->
                        <div class="referencia-type" data-tipo="proveedores">
                            <h4>Proveedores</h4>
                            <div class="referencia-template">
                                <div>
                                    <label>Nombre proveedor:</label>
                                    <input type="text" class="ref-proveedores-entidad" placeholder="Nombre entidad">
                                </div>
                                <div>
                                    <label>Ciudad:</label>
                                    <input type="text" class="ref-proveedores-ciudad" placeholder="Ciudad">
                                </div>
                                <div>
                                    <label>Número de cuenta:</label>
                                    <input type="text" class="ref-proveedores-cuenta" placeholder="Número de cuenta">
                                </div>
                                <div>
                                    <label>Antigüedad:</label>
                                    <input type="text" class="ref-proveedores-antiguedad" placeholder="Ej: 3 años">
                                </div>
                                <div>
                                    <label>Producto:</label>
                                    <input type="text" class="ref-proveedores-producto" placeholder="Ej: Suministros">
                                </div>
                            </div>
                            <button type="button" class="add-ref-proveedor">Agregar Proveedor</button>
                        </div>

                        <!-- Referencias Comerciales -->
                        <div class="referencia-type" data-tipo="comerciales">
                            <h4>Comerciales</h4>
                            <div class="referencia-template">
                                <div>
                                    <label>Entidad:</label>
                                    <input type="text" class="ref-comerciales-entidad" placeholder="Nombre entidad">
                                </div>
                                <div>
                                    <label>Dirección:</label>
                                    <input type="text" class="ref-comerciales-direccion"
                                        placeholder="Dirección completa">
                                </div>
                                <div>
                                    <label>Ciudad:</label>
                                    <input type="text" class="ref-comerciales-ciudad" placeholder="Ciudad">
                                </div>
                                <div>
                                    <label>Persona de contacto:</label>
                                    <input type="text" class="ref-comerciales-persona_contacto"
                                        placeholder="Nombre completo">
                                </div>
                                <div>
                                    <label>Teléfono:</label>
                                    <input type="tel" class="ref-comerciales-telefono" placeholder="Teléfono">
                                </div>
                            </div>
                            <button type="button" class="add-ref-comercial">Agregar Referencia</button>
                        </div>
                    </div>
                </fieldset>

                <!-- Transacciones Internacionales -->
                <fieldset id="transaccionesExtranjeras">
                    <legend>Transacciones en Moneda Extranjera</legend>

                    <!-- Realiza Transacciones -->
                    <div class="form-group">
                        <label>¿Realiza transacciones en moneda extranjera?</label>
                        <div class="check-container">
                            <label>
                                <input type="radio" name="realiza_transacciones" value="true" class="toggle-product" required
                                    > Sí
                            </label>
                            <label>
                                <input type="radio" name="realiza_transacciones" value="false"
                                    class="toggle-product"> No
                            </label>
                        </div>
                    </div>

                    <!-- Productos (Contenedor dinámico - Oculto inicialmente) -->
                    <div class="form-group" id="productos-group" style="display: none;">
                        <h4>Productos en Moneda Extranjera</h4>
                        <div id="productos-container">
                            <!-- Los productos se agregarán aquí dinámicamente -->
                        </div>
                        <button type="button" class="btn-add add-producto" >➕ Agregar Producto</button>
                    </div>

                    <!-- Operaciones Internacionales -->
                    <div class="form-group">
                        <h4>Operaciones Internacionales</h4>
                        <div class="checkbox-group check-container">
                            <label><input type="checkbox" name="giros" id="giros"> Giros</label>
                            <label><input type="checkbox" name="importacion" id="importacion"> Importación</label>
                            <label><input type="checkbox" name="exportacion" id="exportacion"> Exportación</label>
                            <label><input type="checkbox" name="prestamos" id="prestamos"> Préstamos</label>
                            <label><input type="checkbox" name="pagos_servicios" id="pagos_servicios"> Pagos de
                                Servicios</label>
                            <label><input type="checkbox" name="otros" id="otros_operaciones"> Otros</label>
                        </div>
                    </div>
                </fieldset>

                <!-- Template para productos (hidden) -->
                <template id="producto-template">
                    <div class="producto sub-section">
                        <div class="producto-header">
                            <h5>Producto</h5>
                            <button type="button" class="btn-remove remove-ref remove-producto"
                                >×</button>
                        </div>
                        <div class="form-columns">
                            <div>
                                <label>Identificación</label>
                                <input type="text" name="producto_identificacion" required placeholder="Ej. Cuenta USD">
                            </div>
                            <div>
                                <label>Tipo</label>
                                <input type="text" name="producto_tipo" required placeholder="Ej. Cuenta bancaria ">
                            </div>
                            <div>
                                <label>Monto Promedio</label>
                                <input type="number" name="producto_monto" required placeholder="Ej. 100000">
                            </div>
                            <div>
                                <label>Entidad Bancaria</label>
                                <input type="text" name="producto_entidad" required placeholder="Ej. Bank of America">
                            </div>
                            <div>
                                <label>Ciudad</label>
                                <input type="text" name="producto_ciudad" required placeholder="Ej. Miami">
                            </div>
                            <div>
                                <label>País</label>
                                <input type="text" name="producto_pais" required placeholder="Ej. EE.UU">
                            </div>
                            <div>
                                <label>Moneda</label>
                                <input type="text" name="producto_moneda" required placeholder="Ej. USD">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- PEPs Empresa -->
                <fieldset>
                    <legend>Declaración PEPs (Empresa)</legend>
                    <div class="form-check-grid">
                        <label><input type="checkbox" class="pep-empresa" id="pep1"> Maneja recursos públicos</label>
                        <label><input type="checkbox" class="pep-empresa" id="pep2"> Ejerce poder público</label>
                        <label><input type="checkbox" class="pep-empresa" id="pep3"> Reconocimiento público</label>
                        <label><input type="checkbox" class="pep-empresa" id="pep4"> Vinculación con PEPs</label>
                        <label><input type="checkbox" class="pep-empresa" id="pep5"> Obligaciones tributarias
                            extranjero</label>
                    </div>
                </fieldset>
            </div>

            <div class="content-btn">
                <button type="button" class="js-prev-step">
                    <?php esc_html_e('Anterior', 'mi-formulario-api'); ?>
                </button>
                <button type="button" class="js-next-step">
                    <?php esc_html_e('Siguiente', 'mi-formulario-api'); ?>
                </button>
            </div>
        </div>
        <!-- Paso 4 -->
        <div class="form-step" data-step="4">
            <div id="naturalFields4" style="display:none;">
                <fieldset class="documentos-upload">
                    <legend>Carga de Documentos</legend>
                    <div class="documentos-grid">
                        <!-- Documento de Identidad 150% -->
                        <div class="documento-item">
                            <label for="doc_identidad_150">Documento Identidad*</label>
                            <input type="file" id="doc_identidad_150" name="doc_identidad_150" accept=".pdf,.jpg,.png"
                                required>
                            <small class="helper-text">(Foto de la cedula por ambas caras, Formato PDF o imagen .jpg,
                                .png)</small>
                        </div>
                        <!-- Hoja de Vida -->
                        <div class="documento-item">
                            <label for="hoja_vida">Hoja de Vida*</label>
                            <input type="file" id="hoja_vida" name="hoja_vida" accept=".pdf," required>
                            <small class="helper-text">(Actualizada, Formato PDF)</small>
                        </div>
                        <!-- Referencia Comercial/Laboral -->
                        <div class="documento-item">
                            <label for="ref_comercial_laboral">Referencia Comercial</label>
                            <input type="file" id="ref_comercial_laboral" name="ref_comercial_laboral"
                                accept=".pdf,.jpg,.png">
                            <small class="helper-text">(Formato PDF o imagen .jpg,
                                .png)</small>
                        </div>
                        <!-- Fotocopia RUT -->
                        <div class="documento-item">
                            <label for="fotocopia_rut">Fotocopia RUT*</label>
                            <input type="file" id="fotocopia_rut" name="fotocopia_rut" accept=".pdf,.jpg,.png" required>
                            <small class="helper-text">(Vigente, Formato PDF o imagen .jpg, .png)</small>
                        </div>

                        <!-- Certificación Bancaria Local -->
                        <div class="documento-item">
                            <label for="cert_bancaria">Certificación Bancaria*</label>
                            <input type="file" id="cert_bancaria" name="cert_bancaria" accept=".pdf,.jpg,.png" required>
                            <small class="helper-text">(Formato PDF o imagen .jpg, .png)</small>
                        </div>

                    </div>
                </fieldset>

            </div>
            <div id="juridicaFields4" style="display:none;">
                <fieldset class="documentos-upload">
                    <legend>Carga de Documentos - Persona Jurídica</legend>
                    <div class="documentos-grid">
                        <!-- Fotocopia NIT/RUT -->  
                        <div class="documento-item">
                            <label for="fotocopia_nit_rut">Fotocopia NIT/RUT*</label>
                            <input type="file" id="fotocopia_nit_rut" name="fotocopia_nit_rut" accept=".pdf,.jpg,.png"
                                required>
                            <small class="helper-text">(Actualizado, formato PDF o imagen .jpg, .png)</small>
                        </div>

                        <!-- Documento Identidad 150% RL -->
                        <div class="documento-item">
                            <label for="doc_identidad_150_rl">Documento Identidad RL Ampliado*</label>
                            <input type="file" id="doc_identidad_150_rl" name="doc_identidad_150_rl"
                                accept=".pdf,.jpg,.png" required>
                            <small class="helper-text">(Representante Legal, 150% tamaño real, formato PDF o imagen
                                .jpg, .png)</small>
                        </div>

                        <!-- ITIN Identificación Contribuyente -->
                        <div class="documento-item">
                            <label for="itin_identificacion_contribuyente">ITIN Contribuyente*</label>
                            <input type="file" id="itin_identificacion_contribuyente"
                                name="itin_identificacion_contribuyente" accept=".pdf,.jpg,.png" required>
                            <small class="helper-text">(Certificado actualizado, formato PDF o imagen .jpg,
                                .png)</small>
                        </div>

                        <!-- Nómina de Servicios Sociales -->
                        <div class="documento-item">
                            <label for="nss_doc_identidad">NSS + Documento Identidad*</label>
                            <input type="file" id="nss_doc_identidad" name="nss_doc_identidad" accept=".pdf,.jpg,.png"
                                required multiple>
                            <small class="helper-text">(Formato PDF o imagen .jpg, .png)</small>
                        </div>

                        <!-- EIN Identificación Fiscal -->
                        <div class="documento-item">
                            <label for="ein_identificacion_fiscal">EIN Identificación Fiscal*</label>
                            <input type="file" id="ein_identificacion_fiscal" name="ein_identificacion_fiscal"
                                accept=".pdf,.jpg,.png" required>
                            <small class="helper-text">(Documento vigente, formato PDF o imagen .jpg, .png)</small>
                        </div>

                        <!-- Declaración de Renta -->
                        <div class="documento-item">
                            <label for="declaracion_renta">Declaración de Renta*</label>
                            <input type="file" id="declaracion_renta" name="declaracion_renta" accept=".pdf,.jpg,.png"
                                required>
                            <small class="helper-text">(Últimos 2 periodos, formato PDF o imagen .jpg, .png)</small>
                        </div>

                        <!-- Estados Financieros -->
                        <div class="documento-item">
                            <label for="estados_financieros">Estados Financieros*</label>
                            <input type="file" id="estados_financieros" name="estados_financieros"
                                accept=".pdf,.jpg,.png" required multiple>
                            <small class="helper-text">(Últimos 3 años, formato PDF o imagen .jpg, .png)</small>
                        </div>

                        <!-- Referencia Comercial/Laboral -->
                        <div class="documento-item">
                            <label for="ref_comercial_laboral">Referencia Comercial</label>
                            <input type="file" id="ref_comercial_laboral" name="ref_comercial_laboral"
                                accept=".pdf,.jpg,.png">
                            <small class="helper-text">(Carta en papel membretado, formato PDF o imagen .jpg,
                                .png)</small>
                        </div>

                        <!-- Certificación Bancaria Local -->
                        <div class="documento-item">
                            <label for="cert_bancaria">Certificación Bancaria*</label>
                            <input type="file" id="cert_bancaria" name="cert_bancaria" accept=".pdf,.jpg,.png" required>
                            <small class="helper-text">(Formato PDF o imagen .jpg, .png)</small>
                        </div>

                        <!-- Certificado Existencia SFC -->
                        <div class="documento-item">
                            <label for="cert_existencia_sfc">Certificado Existencia SFC*</label>
                            <input type="file" id="cert_existencia_sfc" name="cert_existencia_sfc"
                                accept=".pdf,.jpg,.png" required>
                            <small class="helper-text">(Vigente, formato PDF o imagen .jpg, .png)</small>
                        </div>

                        <!-- Certificado Existencia CC -->
                        <div class="documento-item">
                            <label for="cert_existencia_cc">Certificado Existencia Cámara de Comercio*</label>
                            <input type="file" id="cert_existencia_cc" name="cert_existencia_cc" accept=".pdf,.jpg,.png"
                                required>
                            <small class="helper-text">(Vigente, máximo 3 meses, formato PDF o imagen .jpg,
                                .png)</small>
                        </div>

                    </div>
                </fieldset>
            </div>
            <div class="content-btn">
                <button type="button" class="js-prev-step">
                    <?php esc_html_e('Anterior', 'mi-formulario-api'); ?>
                </button>
                <button type="submit" id="mfa-submit-button"><?php esc_html_e('Enviar', 'mi-formulario-api'); ?></button>
            </div>
        </div>
    </form>
</div>