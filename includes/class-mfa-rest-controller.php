<?php
// includes/class-mfa-rest-controller.php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MFA_REST_Controller extends WP_REST_Controller {

    protected $namespace = 'mfa/v1'; // Define tu namespace
    protected $api_handler;

    public function __construct() {
        $this->api_handler = new MFA_API_Handler();
        // El hook rest_api_init es el lugar correcto para registrar rutas REST.
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        // Endpoint para obtener datos para los selects
        // Ejemplo: /wp-json/mfa/v1/select-options/opciones_ciudad
        register_rest_route( $this->namespace, '/select-options/(?P<select_id>[a-zA-Z0-9_.-]+)', [
            [
                'methods'             => WP_REST_Server::READABLE, // GET
                'callback'            => [ $this, 'get_select_options' ],
                'permission_callback' => [ $this, 'check_permissions' ],
                'args'                => [
                    'select_id' => [
                        'validate_callback' => function($param, $request, $key) {
                            return is_string($param) && !empty($param);
                        },
                        'sanitize_callback' => 'sanitize_key', // Buena práctica
                        'required' => true,
                        'description' => __( 'Identificador del recurso para el select.', 'mi-formulario-api' ),
                    ],
                    // Podrías añadir más args aquí si necesitas pasar parámetros de filtro a la API externa
                    // 'filter_param' => [ ... ]
                ],
            ],
        ]);

        // Endpoint para enviar el formulario
        // Ejemplo: /wp-json/mfa/v1/submit-form
        register_rest_route( $this->namespace, '/submit-form', [
            [
                'methods'             => WP_REST_Server::CREATABLE, // POST
                'callback'            => [ $this, 'submit_form_data' ],
                'permission_callback' => [ $this, 'check_permissions' ],
                // 'args'                => $this->get_form_submit_args(), // Definir argumentos para validación/sanitización
            ],
        ]);
    }

    /**
     * Define los argumentos esperados para el endpoint de envío del formulario.
     * Esto ayuda con la validación y sanitización automática de WordPress.
     */
    public function get_form_submit_args() {
        return [
            'datos_personales' => [
                'required' => true,
                'type' => 'string',
                'description' => 'JSON con información personal',
                'validate_callback' => function($value) {
                    return $this->is_valid_json($value, [
                        'nombre' => 'string',
                        'tipo_documento' => 'string',
                        'numero_documento' => 'string',
                        'coordinacion' => 'string'
                    ]);
                }
            ],
            'datos_conyuge' => [
                'required' => true,
                'type' => 'string',
                'validate_callback' => function($value) {
                    return $this->is_valid_json($value, [
                        'nombre' => 'string',
                        'tipo_documento' => 'string',
                        'numero_documento' => 'string'
                    ]);
                }
            ],
            'datos_apoderado' => [
                'required' => true,
                'type' => 'string',
            ],
            'actividad_economica_asalariado' => [
                'required' => true,
                'type' => 'string',
                'validate_callback' => function($value) {
                    return $this->is_valid_json($value, [
                        'empresa' => 'string',
                        'ingresos_mensuales' => 'numeric'
                    ]);
                }
            ],
            'informacion_financiera' => [
                'required' => true,
                'type' => 'string',
                'validate_callback' => function($value) {
                    $data = json_decode($value, true);
                    return is_numeric($data['ingresos_mensuales'] ?? null);
                }
            ],
            'declaracion_origen_fondos' => [
                'required' => true,
                'type' => 'string',
                'validate_callback' => function($value) {
                    return $this->is_valid_json($value, [
                        'fuente_fondos' => 'string',
                        'descripcion_fondos' => 'string'
                    ]);
                }
            ],
            'actividad_operaciones_internacionales' => [
                'required' => true,
                'type' => 'string',
                'validate_callback' => function($value) {
                    $data = json_decode($value, true);
                    return isset($data['realiza_operaciones']) && is_bool($data['realiza_operaciones']);
                }
            ],
            'personas_peps' => [
                'required' => true,
                'type' => 'string',
                'validate_callback' => function($value) {
                    return $this->is_valid_json($value, [
                        'maneja_recursos_publicos' => 'boolean',
                        'ejerce_poder_publico' => 'boolean'
                    ]);
                }
            ]
        ];  
    }

    private function is_valid_json($value, $structure) {
        $data = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) return false;
        
        foreach ($structure as $key => $type) {
            if (!array_key_exists($key, $data)) return false;
            
            switch ($type) {  // ✅ Alternativa compatible
                case 'string': $valid = is_string($data[$key]); break;
                case 'numeric': $valid = is_numeric($data[$key]); break;
                case 'boolean': $valid = is_bool($data[$key]); break;
                default: $valid = true;
            }
            
            if (!$valid) return false;
        }
        return true;
    }


    /**
     * Verifica los permisos para acceder a los endpoints.
     * Un nonce válido es suficiente para formularios públicos.
     */
    public function check_permissions( WP_REST_Request $request ) {
        $nonce = $request->get_header('X-WP-Nonce');
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new WP_Error(
                'rest_forbidden_nonce',
                __( 'Nonce inválido.', 'mi-formulario-api' ),
                [ 'status' => 403 ] // Forbidden
            );
        }
        // Podrías añadir más comprobaciones, ej: current_user_can('alguna_capacidad')
        return true;
    }

    public function get_select_options( WP_REST_Request $request ) {
        $select_id = $request->get_param('select_id'); // Ya sanitizado por 'sanitize_key' en args

        // IMPORTANTE: Define cómo mapeas $select_id a un endpoint de tu API externa.
        // Por ejemplo, si $select_id es 'opciones_tipo_documento', tu API externa podría tener un endpoint como 'lists/document-types'
        // Si $select_id es 'opciones_ciudad', podría ser 'geo/cities'
        $external_api_endpoint_map = [
            'opciones_bancos' => 'api/mission/bancos/', // Reemplaza con tu endpoint real
            'opciones_ciudad'         => 'api/mission/ciudades/obtener_todas_las_ciudades/',      // Reemplaza con tu endpoint real
            'opciones_coord_comercial'      => 'api/fabrica/coordinacion_comercial/get_all_offices/'
        ];

        if ( ! isset( $external_api_endpoint_map[$select_id] ) ) {
            return new WP_Error(
                'invalid_select_id',
                __( 'Identificador de select no válido o no mapeado.', 'mi-formulario-api' ),
                [ 'status' => 404 ] // Not Found
            );
        }
        $external_api_endpoint = $external_api_endpoint_map[$select_id];

        // Aquí podrías pasar parámetros adicionales desde la petición REST si los definiste en 'args'
        // $filter_param = $request->get_param('filter_param');
        // if ($filter_param) { $external_api_endpoint = add_query_arg('filter', $filter_param, $external_api_endpoint); }

        $response_data = $this->api_handler->make_request( $external_api_endpoint, 'GET' );

        if ( is_wp_error( $response_data ) ) {
            // El WP_Error de make_request ya debería tener status y mensaje apropiados.
            return $response_data;
        }

        // IMPORTANTE: Transforma $response_data al formato { value: '...', label: '...' } si es necesario.
        // Si tu API externa ya devuelve este formato, no necesitas transformar.
        // Ejemplo de transformación:
        $formatted_options = [];
        if (is_array($response_data) && isset($response_data['data'])) { // Suponiendo que la API devuelve { "items": [ { "id": 1, "name": "Ciudad A" } ] }
            foreach ($response_data['data'] as $item) {
                $formatted_options[] = [
                    'value' => $item['id'],
                    'label' => $item['nombre'],
                ];
            }
        } else if (is_array($response_data) && isset($response_data['results'])) { // Suponiendo que la API devuelve { "items": [ { "id": 1, "name": "Ciudad A" } ] }
            foreach ($response_data['results'] as $item) {
                $formatted_options[] = [
                    'value' => $item['id'],
                    'label' => $item['nombre'],
                ];
            }
        } else if (is_array($response_data) && isset($response_data[0]['id']) && isset($response_data[0]['nombre'])) {
            // Aquí manejamos el caso de array plano de objetos
            foreach ($response_data as $item) {
                $formatted_options[] = [
                    'value' => $item['id'],
                    'label' => $item['nombre'],
                ];
            }
        } else { return new WP_Error('api_data_format_error', __('Formato inesperado de datos de la API externa.','mi-formulario-api'), ['status' => 500]); }
        return new WP_REST_Response( $formatted_options, 200 );

        // Si la API ya devuelve el formato correcto (array de {value, label}):
        if ( ! is_array( $response_data ) ) {
             return new WP_Error('api_data_format_error', __('La API externa no devolvió un array para las opciones del select.','mi-formulario-api'), ['status' => 500, 'raw_response' => $response_data]);
        }
        // Validar que cada item tenga value y label (opcional pero recomendado)
        // foreach($response_data as $item) {
        // if (!isset($item['value']) || !isset($item['label'])) { ... error ... }
        // }

        return new WP_REST_Response( $response_data, 200 );
    }

    public function submit_form_data( WP_REST_Request $request ) {
        $form_data = $request->get_params(); // Para x-www-form-urlencoded o multipart/form-data
        $files = $request->get_file_params();

        $payload = array_merge($form_data, $files);

        if ( empty( $form_data ) ) {
            return new WP_Error( 'missing_payload', __( 'No se recibieron datos en el formulario.', 'mi-formulario-api' ), [ 'status' => 400 ] );
        }


        // IMPORTANTE: Ajusta 'form/submit-endpoint' al endpoint real de tu API externa para enviar datos.
        $external_api_endpoint = 'api/fabrica/asesor_wordpress/crear_asesor_wordpress/';

        $response_data = $this->api_handler->make_request( $external_api_endpoint, 'POST', $payload);

        if ( is_wp_error( $response_data ) ) {
            return $response_data;
        }

        // IMPORTANTE: Ajusta el mensaje de éxito según la respuesta de tu API.
        // Si la API externa devuelve un mensaje en $response_data['message']:
        $success_message = $response_data['message'] ?? __('Formulario enviado con éxito a la API externa.', 'mi-formulario-api');

        return new WP_REST_Response( [
            'success' => true,
            'message' => $success_message,
            'api_response' => $response_data // Opcional: devolver la respuesta completa de la API
        ], 200 );
    }


    public function validate_simple_api_request() {
        $errors = new WP_Error();

        // Parámetros JSON requeridos
        $required_params = [
            'datos_personales',
            'datos_conyuge',
            'datos_apoderado',
            'actividad_economica_asalariado',
            'informacion_financiera',
            'declaracion_origen_fondos',
            'actividad_operaciones_internacionales',
            'personas_peps'
        ];

        // Validar existencia y formato JSON de los parámetros
        foreach ($required_params as $param) {
            $params = $request->get_params();
            if (!$params[$param]) {
                $errors->add($param, "Parámetro $param faltante");
                continue;
            }

            $decoded = json_decode($_POST[$param], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors->add($param, "Formato JSON inválido en $param");
            }
        }

        // Validación básica de datos personales
        if (isset($_POST['datos_personales'])) {
            $dp = json_decode($_POST['datos_personales'], true);
            
            $required_fields = [
                'nombre', 'tipo_documento', 'numero_documento', 'coordinacion',
                'fecha_expedicion', 'lugar_expedicion', 'lugar_nacimiento',
                'fecha_nacimiento', 'direccion', 'telefono', 'correo'
            ];

            foreach ($required_fields as $field) {
                if (empty($dp[$field])) {
                    $errors->add('datos_personales', "Campo requerido: $field");
                }
            }

            // Validar valores permitidos
            $allowed = [
                'tipo_documento' => ['C.C.', 'T.I.', 'C.E.', 'Pasaporte'],
                'estado_civil' => ['Soltero', 'Casado', 'Viudo', 'Divorciado', 'Unión Libre'],
                'tipo_cuenta' => ['ahorro', 'corriente']
            ];

            foreach ($allowed as $field => $values) {
                if (isset($dp[$field]) && !in_array($dp[$field], $values)) {
                    $errors->add('datos_personales', "Valor no permitido en $field");
                }
            }
        }

        // Validar archivos requeridos
        $required_files = [
            'cert_bancaria_ext',
            'ref_comercial_laboral',
            'fotocopia_rut',
            'hoja_vida',
            'doc_identidad_150'
        ];

        foreach ($required_files as $file) {
            $files = $request->get_file_params();
            if (empty($files[$file]) || $files[$file]['error'] !== UPLOAD_ERR_OK) {
                $errors->add($file, "Archivo requerido: $file");
            }
        }

        return $errors->has_errors() ? $errors : true;
    }


}