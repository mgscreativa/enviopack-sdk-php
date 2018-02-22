<?php
$testFolder = dirname( __FILE__ );

if ( file_exists( $testFolder . "/credentials-private.ini" ) )
{
    $credentials = parse_ini_file( $testFolder . "/credentials-private.ini" );
}
else if ( file_exists( $testFolder . "/credentials.ini" ) )
{
    $credentials = parse_ini_file( $testFolder . "/credentials.ini" );
}
else
{
    die ( 'No credentials file found!' );
}

if ( ! class_exists( 'EnvioPackApi' ) )
{
    require_once( dirname( __FILE__ ) . '/../lib/enviopack.php' );
}

$apiKey    = $credentials["api_key"];
$secretKey = $credentials["secret_key"];

/***********************************************/
/* DO NOT CHANGE ANITHING BELOW THIS LINE
/***********************************************/

$ep = new EnvioPackApi( $apiKey, $secretKey );

$provinciaId  = 'Z';
$zip          = preg_replace( "/[^0-9]/", "", 'Z9400' );
$weight       = '0.35'; // En Kg
$packages     = '20x2x10'; // alto x ancho x largo en CM
$orderId      = generateRandomString();
$firstName    = 'Martin';
$lastName     = 'Briglia';
$email        = 'martin@mgscreativa.com';
$telephone    = '4738-8948';
$street       = "Fray Cayetano Rodriguez";
$streetNumber = "2368";
$city         = "Munro, Vicente Lopez";
$orderAmmount = '525.50';
$payed        = true;
$shipmentIDS  = array( '337449' );
$courier      = 'oca';

echo '<h3>check_credentials</h3>';
try
{
    $result = $ep->check_credentials();

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>source_address</h3>';
try
{
    $result = $ep->source_address();

    $sourceAddressId = $result['response'][0]['id'];
    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_states</h3>';
try
{
    $result = $ep->get_states();

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_state</h3>';
try
{
    $result = $ep->get_state( $provinciaId );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>validate_state_zip</h3>';
try
{
    $result = $ep->validate_state_zip( $provinciaId, $zip );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_state_cities</h3>';
try
{
    $result = $ep->get_state_cities( $provinciaId );

    $localidadId = $result['response'][2]['id'];
    echo '<pre>';
    print_r( $result );
    echo '</pre>';

}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

$params = array
(
    'provincia'       => $provinciaId,
    'codigo_postal'   => $zip,
    'peso'            => $weight,
    'paquetes'        => $packages,
    'despacho'        => 'S',
    'modalidad'       => 'D',
    'direccion_envio' => $sourceAddressId,
);

echo '<h3>get_quote</h3>';
try
{
    $result = $ep->get_quote( $params );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

$params = array
(
    'provincia'       => $provinciaId,
    'codigo_postal'   => $zip,
    'peso'            => $weight,
    'paquetes'        => $packages,
    'direccion_envio' => $sourceAddressId,
);

echo '<h3>quote_home_delivery_price</h3>';
try
{
    $result = $ep->quote_home_delivery_price( $params );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

$params = array
(
    'provincia'       => $provinciaId,
    'localidad'       => $localidadId,
    'peso'            => $weight,
    'paquetes'        => $packages,
    'direccion_envio' => $sourceAddressId,
);

echo '<h3>quote_branch_delivery_price</h3>';
try
{
    $result = $ep->quote_branch_delivery_price( $params );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_couriers</h3>';
try
{
    $result = $ep->get_couriers();

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_courier_branches</h3>';
try
{
    $result = $ep->get_courier_branches( $courier );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_courier_services</h3>';
try
{
    $result = $ep->get_courier_services( $courier );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

$params = array
(
    "id_externo" => $orderId,
    "nombre"     => $firstName,
    "apellido"   => $lastName,
    "email"      => $email,
    "telefono"   => $telephone,
    "localidad"  => $city,
    "provincia"  => $provinciaId,
    "monto"      => $orderAmmount,
    "fecha_alta" => date( "c" ),
    "pagado"     => $payed,
);

echo '<h3>create_order</h3>';
try
{
    $result = $ep->create_order( $params );

    $pedidoId = $result['response']['id'];
    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_order</h3>';
try
{
    $result = $ep->get_order( $pedidoId );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

$parameters = array(
    "con_ultimo_envio" => "1",
    "orden_columna"    => "fecha_alta",
    "orden_sentido"    => "desc",
    "pagina"           => "1",
    "ppp"              => "50",
    "q"                => "M0PERIAY",
    "seccion"          => "por-confirmar",
    "subseccion"       => "todos"
);

echo '<h3>get_orders</h3>';
try
{
    $result = $ep->get_orders( $shipmentIDS );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

$params = array
(
    "pedido"          => $pedidoId,
    "direccion_envio" => $sourceAddressId,
    "destinatario"    => $firstName . " " . $lastName,
    "confirmado"      => true,
    "paquetes"        => array(
        array(
            "alto"  => 20,
            "ancho" => 2,
            "largo" => 10,
            "peso"  => 0.35,
        )
    ),
    "despacho"        => "S",
    "modalidad"       => "D",
    "servicio"        => 'N',
    "correo"          => $courier,
    "calle"           => $street,
    "numero"          => $streetNumber,
    "piso"            => null,
    "depto"           => null,
    "codigo_postal"   => $zip,
    "provincia"       => $provinciaId,
    "localidad"       => $city,
);

echo '<h3>create_shipment</h3>';
try
{
    $result = $ep->create_shipment( $params );

    $envioId = $result['response']['id'];
    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_shipment</h3>';
try
{
    $result = $ep->get_shipment( $envioId );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

$params = array
(
    "id" => $envioId,
);

echo '<h3>process_shipment</h3>';
try
{
    $result = $ep->process_shipment( $shipmentIDS );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_print_labels</h3>';
try
{
    $result = $ep->get_print_labels( $shipmentIDS );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

echo '<h3>get_api_config</h3>';
try
{
    $result = $ep->get_api_config();

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

$params = array
(
    "webhook_url" => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/enviopack/index.php",
);

echo '<h3>set_api_config</h3>';
try
{
    $result = $ep->set_api_config( $params );

    echo '<pre>';
    print_r( $result );
    echo '</pre>';
}
catch ( Exception $e )
{
    echo '<p><strong>Error ' . $e->getCode() . '</strong>: ' . $e->getMessage() . '</p>';
}

function generateRandomString( $length = 8 ) {
    $characters       = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen( $characters );
    $randomString     = '';
    for ( $i = 0; $i < $length; $i ++ )
    {
        $randomString .= $characters[rand( 0, $charactersLength - 1 )];
    }

    return $randomString;
}
