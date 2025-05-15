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
                'args'                => $this->get_form_submit_args(), // Definir argumentos para validación/sanitización
            ],
        ]);
    }

    /**
     * Define los argumentos esperados para el endpoint de envío del formulario.
     * Esto ayuda con la validación y sanitización automática de WordPress.
     */
    public function get_form_submit_args() {
        return [
            'nombre' => [
                'required'          => true,
                'type'              => 'string',
                'description'       => __('Nombre del usuario.', 'mi-formulario-api'),
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'email' => [
                'required'          => true,
                'type'              => 'string',
                'format'            => 'email',
                'description'       => __('Email del usuario.', 'mi-formulario-api'),
                'sanitize_callback' => 'sanitize_email',
            ],
            'tipo_documento' => [ // Corresponde al name del select en el form
                'required'          => false, // O true si es obligatorio
                'type'              => 'string', // O integer si los values son numéricos
                'description'       => __('Tipo de documento seleccionado.', 'mi-formulario-api'),
                'sanitize_callback' => 'sanitize_text_field', // O 'absint' si es numérico
            ],
            'ciudad_id' => [ // Corresponde al name del select en el form
                'required'          => false,
                'type'              => 'string', // O integer
                'description'       => __('Ciudad seleccionada.', 'mi-formulario-api'),
                'sanitize_callback' => 'sanitize_text_field', // O 'absint'
            ],
            'mensaje' => [
                'required'          => false,
                'type'              => 'string',
                'description'       => __('Mensaje del usuario.', 'mi-formulario-api'),
                'sanitize_callback' => 'sanitize_textarea_field',
            ],
            // Añade más campos aquí según tu formulario
        ];
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
            'opciones_ciudad'         => 'api/mission/ciudades/',      // Reemplaza con tu endpoint real
            'opciones_coord_comercial'      => 'api/fabrica/coordinacion_comercial/'
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
        // $formatted_options = [];
        // if (is_array($response_data) && isset($response_data['items'])) { // Suponiendo que la API devuelve { "items": [ { "id": 1, "name": "Ciudad A" } ] }
        //     foreach ($response_data['items'] as $item) {
        //         $formatted_options[] = [
        //             'value' => $item['id'],
        //             'label' => $item['name'],
        //         ];
        //     }
        // } else { return new WP_Error('api_data_format_error', __('Formato inesperado de datos de la API externa.','mi-formulario-api'), ['status' => 500]); }
        // return new WP_REST_Response( $formatted_options, 200 );

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
        // Los parámetros ya están validados y sanitizados por 'args' si se usó $request->get_params()
        // o get_json_params() si el Content-Type es application/json
        $form_data = $request->get_json_params(); // Para JSON payload
        // Si envías como FormData desde JS (no application/json), usarías:
        // $form_data = $request->get_params(); // Para x-www-form-urlencoded o multipart/form-data

        if ( empty( $form_data ) ) {
            return new WP_Error( 'missing_payload', __( 'No se recibieron datos en el formulario.', 'mi-formulario-api' ), [ 'status' => 400 ] );
        }

        // IMPORTANTE: Ajusta 'form/submit-endpoint' al endpoint real de tu API externa para enviar datos.
        $external_api_endpoint = 'api/fabrica/asesor_wordpress/crear_asesor_';

        $response_data = $this->api_handler->make_request( $external_api_endpoint, 'POST', $form_data );

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
}