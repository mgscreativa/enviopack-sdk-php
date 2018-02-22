<?php
/**
 * EnvíoPack Api Integration Library
 * @author    Martin Briglia, MGS Creativa
 * @url http://www.mgscreativa.com
 * @copyright Copyright (C) 2018 MGS Creativa - All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

$GLOBALS["LIB_BASE_PATH"] = dirname( __FILE__ );

class EnvioPackApi {
    const version = "0.1.5";

    private $api_key;
    private $secret_key;
    private $access_token;
    private $access_data;

    function __construct() {
        $i = func_num_args();

        if ( $i > 2 || $i < 1 )
        {
            throw new EnvioPackApiException( "Invalid arguments. Use CLIENT_ID and CLIENT SECRET, or ACCESS_TOKEN" );
        }

        if ( $i == 1 )
        {
            $this->access_token = func_get_arg( 0 );
        }

        if ( $i == 2 )
        {
            $this->api_key    = func_get_arg( 0 );
            $this->secret_key = func_get_arg( 1 );
        }
    }

    public function get_access_token() {
        if ( isset ( $this->access_token ) && ! is_null( $this->access_token ) )
        {
            return $this->access_token;
        }

        if ( isset ( $this->access_data['token'] ) && ! is_null( $this->access_data['token'] ) )
        {
            return $this->access_data['token'];
        }

        $app_client_values = array(
            'api-key'    => $this->api_key,
            'secret-key' => $this->secret_key,
        );

        $access_data = EPRestClient::post( array(
            "uri"     => "/auth",
            "data"    => $app_client_values,
            "headers" => array(
                "content-type" => "application/x-www-form-urlencoded",
            )
        ) );

        if ( $access_data["status"] != 200 )
        {
            throw new EnvioPackApiException ( $access_data['response']['message'], $access_data['status'] );
        }

        $this->access_data = $access_data['response'];

        return $this->access_data['token'];
    }

    public function check_credentials() {
        try
        {
            $this->get_access_token();

            return true;
        }
        catch ( Exception $e )
        {
            return false;
        }
    }

    public function source_address() {
        $request = array(
            "uri"    => "/direcciones-de-envio",
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function get_quote( $params ) {
        $params["access_token"] = $this->get_access_token();

        $request = array(
            "uri"    => "/cotizar/costo",
            "params" => $params,
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function quote_home_delivery_price( $params ) {
        $params["access_token"] = $this->get_access_token();

        $request = array(
            "uri"    => "/cotizar/precio/a-domicilio",
            "params" => $params,
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function quote_branch_delivery_price( $params ) {
        $params["access_token"] = $this->get_access_token();

        $request = array(
            "uri"    => "/cotizar/precio/a-sucursal",
            "params" => $params,
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function get_couriers() {
        $request = array(
            "uri"    => "/correos",
            "params" => array(
                "access_token"    => $this->get_access_token(),
                "filtrar_activos" => true,
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function get_courier_branches( $courier = null ) {
        $request = array(
            "uri"    => "/sucursales",
            "params" => array(
                "access_token" => $this->get_access_token(),
                "id_correo"    => $courier,
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function get_courier_services( $courier = null ) {
        $request = array(
            "uri"    => "/correos/" . $courier . "/servicios",
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function get_states() {
        $request = array(
            "uri"    => "/provincias",
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function get_state( $privinciaId = null ) {
        $request = array(
            "uri"    => "/provincias/" . $privinciaId,
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function validate_state_zip( $privinciaId = null, $zip = null ) {
        $request = array(
            "uri"    => "/provincia/" . $privinciaId . '/validar-codigo-postal',
            "params" => array(
                "access_token"  => $this->get_access_token(),
                "codigo_postal" => $zip,
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function get_state_cities( $privinciaId = null ) {
        $request = array(
            "uri"    => "/localidades",
            "params" => array(
                "access_token" => $this->get_access_token(),
                "id_provincia" => $privinciaId,
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function create_order( $params ) {
        $request = array(
            "uri"    => "/pedidos",
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
            "data"   => $params,
        );

        $response = EPRestClient::post( $request );

        return $response;
    }

    public function get_order( $pedidoId ) {
        $request = array(
            "uri"    => "/pedidos/" . $pedidoId,
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function get_orders( $params ) {
        $params["access_token"] = $this->get_access_token();

        $request = array(
            "uri"    => "/pedidos",
            "params" => $params,
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function create_shipment( $params ) {
        $request = array(
            "uri"    => "/envios",
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
            "data"   => $params,
        );

        $response = EPRestClient::post( $request );

        return $response;
    }

    public function get_shipment( $envioId ) {
        $request = array(
            "uri"    => "/envios/" . $envioId,
            "params" => array(
                "access_token" => $this->get_access_token(),
            )
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function process_shipment( $params ) {
        $request = array(
            "uri"    => "/envios/procesar",
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
            "data"   => $params,
        );

        $response = EPRestClient::post( $request );

        return $response;
    }

    public function get_print_labels( $ids ) {
        $request = array(
            "uri"    => "/envios/etiquetas",
            "params" => array(
                "access_token" => $this->get_access_token(),
                "ids"          => implode( ',', $ids ),
            )
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function get_api_config() {
        $request = array(
            "uri"    => "/configuraciones-api",
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
        );

        $response = EPRestClient::get( $request );

        return $response;
    }

    public function set_api_config( $params ) {
        $request = array(
            "uri"    => "/configuraciones-api",
            "params" => array(
                "access_token" => $this->get_access_token(),
            ),
            "data"   => $params,
        );

        $response = EPRestClient::post( $request );

        return $response;
    }

    /**
     * Generic resource get
     *
     * @param $request
     * @param $params
     * @param $authenticate = true
     *
     * @return array(json)
     * @throws Exception si se encuentra un error en la solucitud.
     */
    public function get( $request, $params = null, $authenticate = true ) {
        if ( is_string( $request ) )
        {
            $request = array(
                "uri"          => $request,
                "params"       => $params,
                "authenticate" => $authenticate
            );
        }

        $request["params"] = isset ( $request["params"] ) && is_array( $request["params"] ) ? $request["params"] : array();

        if ( ! isset ( $request["authenticate"] ) || $request["authenticate"] !== false )
        {
            $request["params"]["access_token"] = $this->get_access_token();
        }

        $result = EPRestClient::get( $request );

        return $result;
    }

    /**
     * Generic resource post
     *
     * @param $request
     * @param $data
     * @param $params
     *
     * @return array(json)
     * @throws Exception si se encuentra un error en la solucitud.
     */
    public function post( $request, $data = null, $params = null ) {
        if ( is_string( $request ) )
        {
            $request = array(
                "uri"    => $request,
                "data"   => $data,
                "params" => $params
            );
        }

        $request["params"] = isset ( $request["params"] ) && is_array( $request["params"] ) ? $request["params"] : array();

        if ( ! isset ( $request["authenticate"] ) || $request["authenticate"] !== false )
        {
            $request["params"]["access_token"] = $this->get_access_token();
        }

        $result = EPRestClient::post( $request );

        return $result;
    }

    /**
     * Generic resource put
     *
     * @param $request
     * @param $data
     * @param $params
     *
     * @return array(json)
     * @throws Exception si se encuentra un error en la solucitud.
     */
    public function put( $request, $data = null, $params = null ) {
        if ( is_string( $request ) )
        {
            $request = array(
                "uri"    => $request,
                "data"   => $data,
                "params" => $params
            );
        }

        $request["params"] = isset ( $request["params"] ) && is_array( $request["params"] ) ? $request["params"] : array();

        if ( ! isset ( $request["authenticate"] ) || $request["authenticate"] !== false )
        {
            $request["params"]["access_token"] = $this->get_access_token();
        }

        $result = EPRestClient::put( $request );

        return $result;
    }

    /**
     * Generic resource delete
     *
     * @param $request
     * @param $params
     *
     * @return array(json)
     * @throws Exception si se encuentra un error en la solucitud.
     */
    public function delete( $request, $params = null ) {
        if ( is_string( $request ) )
        {
            $request = array(
                "uri"    => $request,
                "params" => $params
            );
        }

        $request["params"] = isset ( $request["params"] ) && is_array( $request["params"] ) ? $request["params"] : array();

        if ( ! isset ( $request["authenticate"] ) || $request["authenticate"] !== false )
        {
            $request["params"]["access_token"] = $this->get_access_token();
        }

        $result = EPRestClient::delete( $request );

        return $result;
    }
}


/**
 * EnvíoPack cURL RestClient
 */
class EPRestClient {
    const API_BASE_URL = "https://api.enviopack.com";

    private static function build_request( $request ) {
        if ( ! extension_loaded( "curl" ) )
        {
            throw new EnvioPackApiException( "cURL extension not found. You need to enable cURL in your php.ini or another configuration you have." );
        }

        if ( ! isset( $request["method"] ) )
        {
            throw new EnvioPackApiException( "No HTTP METHOD specified" );
        }

        if ( ! isset( $request["uri"] ) )
        {
            throw new EnvioPackApiException( "No URI specified" );
        }

        $headers              = array( "accept: application/json" );
        $json_content         = true;
        $form_content         = false;
        $default_content_type = true;

        if ( isset( $request["headers"] ) && is_array( $request["headers"] ) )
        {
            foreach ( $request["headers"] as $h => $v )
            {
                $h = strtolower( $h );
                $v = strtolower( $v );

                if ( $h == "content-type" )
                {
                    $default_content_type = false;
                    $json_content         = $v == "application/json";
                    $form_content         = $v == "application/x-www-form-urlencoded";
                }

                array_push( $headers, $h . ": " . $v );
            }
        }
        if ( $default_content_type )
        {
            array_push( $headers, "content-type: application/json" );
        }

        $connect = curl_init();

        curl_setopt( $connect, CURLOPT_USERAGENT, "EnvíoPack PHP SDK v" . EnvioPackApi::version );
        curl_setopt( $connect, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $connect, CURLOPT_SSL_VERIFYPEER, true );
        curl_setopt( $connect, CURLOPT_CAINFO, $GLOBALS["LIB_BASE_PATH"] . "/cacert.pem" );
        curl_setopt( $connect, CURLOPT_CUSTOMREQUEST, $request["method"] );
        curl_setopt( $connect, CURLOPT_HTTPHEADER, $headers );

        if ( isset ( $request["params"] ) && is_array( $request["params"] ) && count( $request["params"] ) > 0 )
        {
            $request["uri"] .= ( strpos( $request["uri"], "?" ) === false ) ? "?" : "&";
            $request["uri"] .= self::build_query( $request["params"] );
        }
        curl_setopt( $connect, CURLOPT_URL, self::API_BASE_URL . $request["uri"] );

        if ( isset( $request["data"] ) )
        {
            if ( $json_content )
            {
                if ( gettype( $request["data"] ) == "string" )
                {
                    json_decode( $request["data"], true );
                }
                else
                {
                    $request["data"] = json_encode( $request["data"] );
                }

                if ( function_exists( 'json_last_error' ) )
                {
                    $json_error = json_last_error();
                    if ( $json_error != JSON_ERROR_NONE )
                    {
                        throw new EnvioPackApiException( "JSON Error [{$json_error}] - Data: " . $request["data"] );
                    }
                }
            }
            else if ( $form_content )
            {
                $request["data"] = self::build_query( $request["data"] );
            }

            curl_setopt( $connect, CURLOPT_POSTFIELDS, $request["data"] );
        }

        return $connect;
    }

    private static function exec( $request ) {
        $connect = self::build_request( $request );

        $api_result    = curl_exec( $connect );
        $api_http_code = curl_getinfo( $connect, CURLINFO_HTTP_CODE );

        if ( $api_result === false )
        {
            throw new EnvioPackApiException ( curl_error( $connect ) );
        }

        $response = array(
            "status"   => $api_http_code,
            "response" => json_decode( $api_result, true )
        );

        if ( $response['status'] >= 400 )
        {
            $message = $response['response']['message'];
            if ( isset ( $response['response']['cause'] ) )
            {
                if ( isset ( $response['response']['cause']['code'] ) && isset ( $response['response']['cause']['description'] ) )
                {
                    $message .= " - " . $response['response']['cause']['code'] . ': ' . $response['response']['cause']['description'];
                }
                else if ( is_array( $response['response']['cause'] ) )
                {
                    foreach ( $response['response']['cause'] as $cause )
                    {
                        $message .= " - " . $cause['code'] . ': ' . $cause['description'];
                    }
                }
            }

            throw new EnvioPackApiException ( $message, $response['status'] );
        }

        curl_close( $connect );

        return $response;
    }

    private static function build_query( $params ) {
        if ( function_exists( "http_build_query" ) )
        {
            return http_build_query( $params, "", "&" );
        }
        else
        {
            foreach ( $params as $name => $value )
            {
                $elements[] = "{$name}=" . urlencode( $value );
            }

            return implode( "&", $elements );
        }
    }

    public static function get( $request ) {
        $request["method"] = "GET";

        return self::exec( $request );
    }

    public static function post( $request ) {
        $request["method"] = "POST";

        return self::exec( $request );
    }

    public static function put( $request ) {
        $request["method"] = "PUT";

        return self::exec( $request );
    }

    public static function delete( $request ) {
        $request["method"] = "DELETE";

        return self::exec( $request );
    }
}

class EnvioPackApiException extends Exception {
    public function __construct( $message, $code = 500, Exception $previous = null ) {
        parent::__construct( $message, $code, $previous );
    }
}
