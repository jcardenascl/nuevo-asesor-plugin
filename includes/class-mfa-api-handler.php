<?php
// includes/class-mfa-api-handler.php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MFA_API_Handler {

    private $api_options;
    private $base_url;
    const TOKEN_TRANSIENT_KEY = 'mfa_api_auth_token_v2'; // Cambiado para evitar colisiones si actualizas
    // Asume que el token expira en 1 hora (3600s). Ajusta según tu API.
    // Guardaremos el transient por un poco menos para estar seguros (ej. 55 minutos).
    const TOKEN_EXPIRATION_SECONDS = 55 * 60;


    public function __construct() {
        $this->api_options = get_option( MFA_Admin_Settings::OPTION_NAME );
        $current_env = $this->api_options['api_environment'] ?? 'dev';

        if ($current_env === 'dev' && !empty($this->api_options['api_dev_url'])) {
            $this->base_url = trailingslashit( esc_url_raw( $this->api_options['api_dev_url'] ) );
        } elseif ($current_env === 'prod' && !empty($this->api_options['api_prod_url'])) {
            $this->base_url = trailingslashit( esc_url_raw( $this->api_options['api_prod_url'] ) );
        } else {
            $this->base_url = null;
        }
    }

    private function get_token() {
        if ( empty( $this->base_url ) ) {
            return new WP_Error(
                'api_config_error',
                __( 'La URL base de la API no está configurada.', 'mi-formulario-api' ),
                ['status' => 500]
            );
        }

        $token = get_transient( self::TOKEN_TRANSIENT_KEY );
        if ( false === $token ) {
            // El token no existe o ha expirado, necesitamos uno nuevo.
            // IMPORTANTE: Ajusta 'auth/login' al endpoint de autenticación real de tu API externa
            $auth_endpoint = 'api/mission/auth/login/';
            $url = $this->base_url . $auth_endpoint;

            $credentials = [
                // IMPORTANTE: Ajusta los nombres de los campos ('username', 'password')
                // según lo que espere tu API externa para la autenticación.
                'username' => $this->api_options['api_username'] ?? '',
                'password' => $this->api_options['api_password'] ?? '',
                'rol' => 'WP',
            ];

            if ( empty($credentials['username']) || empty($credentials['password']) ) {
                 return new WP_Error(
                    'api_credentials_error',
                    __( 'Credenciales de API no configuradas.', 'mi-formulario-api' ),
                    ['status' => 500]
                );
            }

            $response = wp_remote_post( $url, [
                'method'    => 'POST',
                'headers'   => ['Content-Type' => 'application/json; charset=utf-8'],
                'body'      => wp_json_encode( $credentials ), // wp_json_encode es más seguro
                'timeout'   => 20, // Aumentado ligeramente
            ]);

            if ( is_wp_error( $response ) ) {
                // Error de conexión (DNS, timeout, etc.)
                return new WP_Error(
                    'api_connection_error',
                    sprintf(__( 'Error de conexión con la API externa: %s', 'mi-formulario-api' ), $response->get_error_message()),
                    ['status' => 503] // Service Unavailable
                );
            }

            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );
            $http_code = wp_remote_retrieve_response_code( $response );

            // IMPORTANTE: Ajusta la condición `empty( $data['token'] )`
            // según cómo tu API externa devuelva el token (ej. $data['access_token'], $data['auth']['token'], etc.)
            if ( $http_code >= 300 || empty( $data['access_token'] ) ) {
                $error_message = $data['message'] ?? ( is_string($body) && strlen($body) < 200 ? $body : __('Error desconocido durante la autenticación con la API externa.', 'mi-formulario-api') );
                return new WP_Error(
                    'api_auth_failed',
                    sprintf(__( 'Autenticación fallida con API externa (HTTP %d): %s', 'mi-formulario-api' ), $http_code, esc_html($error_message)),
                    ['status' => $http_code, 'api_response' => $data]
                );
            }

            $token = $data['access_token']; // Ajusta si el token está en otra propiedad
            set_transient( self::TOKEN_TRANSIENT_KEY, $token, self::TOKEN_EXPIRATION_SECONDS );
        }
        return $token;
    }

    public function make_request( $endpoint, $method = 'GET', $payload = [] ) {
        if ( empty($this->base_url) ) {
            return new WP_Error(
                'api_config_error',
                __( 'La URL base de la API no está configurada.', 'mi-formulario-api' ),
                ['status' => 500]
            );
        }

        $token = $this->get_token();
        if ( is_wp_Error( $token ) ) {
            return $token; // Propaga el error (config, credenciales, o auth)
        }

        $url = $this->base_url . ltrim($endpoint, '/'); // Asegurar que no haya doble slash

        $has_files = $this->payload_contains_files($payload);

        $args = [
            'method'    => strtoupper($method),
            'headers'   => [
                'Authorization' => 'Token ' . $token, 
                'Accept'        => 'application/json',
            ],
            'timeout'   => 60,
        ];

        if ( ! empty( $payload ) && ( $args['method'] === 'POST' || $args['method'] === 'PUT' || $args['method'] === 'PATCH' ) ) {
            if ($has_files) {
                // Para FormData: enviar como array sin codificar
                $args['body'] = $this->build_multipart_body($payload);
                $boundary = substr($args['body'], 2, 24);
                $args['headers'] = [
                    'Authorization' => 'Token ' . $token, 
                    'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
                ];
            } else {
                // Para JSON: codificar el payload
                $args['body'] = wp_json_encode($payload);
            }
        } elseif ( $args['method'] === 'GET' && !empty( $payload )) {
            // Para GET, los parámetros suelen ir en la URL
            $url = add_query_arg( $payload, $url );
        }


        // Construir respuesta detallada
        $response = wp_remote_request( $url, $args );

        if ( is_wp_error( $response ) ) {
            return new WP_Error(
                'api_request_connection_error',
                sprintf(__( 'Error de conexión en solicitud a API externa: %s', 'mi-formulario-api' ), $response->get_error_message()),
                ['status' => 503]
            );
        }

        $body = wp_remote_retrieve_body( $response );
        $http_code = wp_remote_retrieve_response_code( $response );

        $content_type = wp_remote_retrieve_header($response, 'content-type');

        // Intentar decodificar solo si es JSON
        $is_json = str_contains($content_type, 'application/json');
        $data = $is_json ? json_decode($body, true) : null;

        if ($http_code >= 300) {
            // Limpiar token si es error de autenticación
            if ($http_code === 401 || $http_code === 403) {
                delete_transient(self::TOKEN_TRANSIENT_KEY);
            }

            // Construir mensaje de error detallado
            $error_details = [
                'http_status' => $http_code,
                'endpoint' => $url,  // Asegúrate de tener acceso a la URL aquí
                'content_type' => $content_type,
                'raw_response' => strlen($body) > 500 ? substr($body, 0, 500) . '...' : $body
            ];

            // Extraer mensaje de error según formato esperado
            $api_error_message = '';
            if ($is_json && $data) {
                // Buscar mensajes comunes en diferentes estructuras de error
                $api_error_message = $data['message'] 
                    ?? $data['detail'] 
                    ?? $data['title'] 
                    ?? json_encode($data, JSON_PRETTY_PRINT);
            } else {
                // Limpiar respuesta HTML/XML para mostrar solo texto relevante
                $api_error_message = wp_strip_all_tags($body);
                $api_error_message = preg_replace('/\s+/', ' ', $api_error_message);
                $api_error_message = substr($api_error_message, 0, 200);
            }

            // Mensaje amigable para el cliente
            $user_message = sprintf(
                __('Error en API externa (HTTP %d): %s', 'mi-formulario-api'),
                $http_code,
                $api_error_message ?: __('Error desconocido', 'mi-formulario-api')
            );

            return new WP_Error(
                'api_request_failed',
                $user_message,
                [
                    'status' => $http_code,
                    'error_details' => $error_details,
                    'request_payload' => $payload // Solo si no contiene datos sensibles
                ]
            );
        }

        // Validar estructura mínima de respuesta exitosa
        if ($is_json && !is_array($data)) {
            return new WP_Error(
                'api_invalid_response',
                __('La API externa respondió con un formato JSON inválido', 'mi-formulario-api'),
                [
                    'status' => 500,
                    'raw_response' => $body
                ]
            );
        }


        return $data; // Devuelve los datos decodificados
    }



    // 4. Función auxiliar para detectar archivos en el payload
    private function payload_contains_files($payload) {
        foreach ($payload as $item) {
            if (is_array($item) && isset($item['name'], $item['type'], $item['tmp_name'], $item['error'], $item['size'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Construye el cuerpo multipart manualmente
     */
    private function build_multipart_body($payload) {
        $boundary = wp_generate_password(24);
        $body = '';

        foreach ($payload as $key => $value) {
            $body .= "--$boundary\r\n";

            if (is_array($value) && isset($value['tmp_name'])) { // Archivo
                $filename = sanitize_file_name($value['name']);
                $file_content = file_get_contents($value['tmp_name']);
                
                $body .= "Content-Disposition: form-data; name=\"$key\"; filename=\"$filename\"\r\n";
                $body .= "Content-Type: {$value['type']}\r\n\r\n";
                $body .= $file_content . "\r\n";
            } else { // Campo JSON
                $body .= "Content-Disposition: form-data; name=\"$key\"\r\n";
                $body .= "Content-Type: application/json\r\n\r\n";
                $body .= $value . "\r\n"; // Valor ya viene como JSON string
            }
        }

        $body .= "--$boundary--\r\n";
        return $body;
    }
}