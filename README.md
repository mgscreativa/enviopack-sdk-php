

# EnvíoPack SDK Api Integration Library

* [Install](#install)
* [Specific methods](#specific-methods)
* [Generic methods](#generic-methods)

<a name="install"></a>
## Install

### With Composer

From command line

```
composer require enviopack/sdk:0.1.4
```

As a dependency in your project's composer.json

```json
{
    "require": {
        "enviopack/sdk": "0.1.4"
    }
}
```

### By downloading

1. Clone/download this repository
2. Copy `lib/enviopack.php` to your project's desired folder.

<a name="specific-methods"></a>
## Specific methods

### Configure your credentials

* Get your **API_KEY** and **SECRET_KEY** in the following address: [https://app.enviopack.com/configuraciones-api](https://app.enviopack.com/configuraciones-api)

```php
require_once ('enviopack.php');

$ep = new EnvioPackApi ("API_KEY", "SECRET_KEY");
```

### Methods

#### Check credentials

```php
$result = $ep->check_credentials();

print_r( $result );
```

#### Get store source address

```php
$result = $ep->source_address();

print_r( $result );
```

#### Get shipment quotes from available couriers

```php
$params = array
(
    'provincia'       => 'Z', // Santa Cruz
    'codigo_postal'   => '9400',
    'peso'            => '0.35',
    'paquetes'        => '20x2x10',
    'despacho'        => 'S',
    'modalidad'       => 'D',
    'direccion_envio' => '1081', // Change with your source address ID
);

$result = $ep->get_quote( $params );

print_r( $result );
```

#### Quote customer home delivery price

```php
$params = array
(
    'provincia'     => 'Z', // Santa Cruz
    'codigo_postal' => '9400',
    'peso'          => '0.35',
    'paquetes'      => '20x2x10',
    'direccion_envio' => '1081', // Change with your source address ID
);

$result = $ep->quote_home_delivery_price( $params );

print_r ($result);
```

#### Quote customer branch delivery price

```php
$params = array
(
    'provincia'     => 'Z', // Santa Cruz
    'codigo_postal' => '9400',
    'peso'          => '0.35',
    'paquetes'      => '20x2x10',
    'direccion_envio' => '1081', // Change with your source address ID
);

$result = $ep->quote_branch_delivery_price( $params );

print_r ($result);
```

#### Create order

```php
$params = array
(
    "id_externo" => 'external_reference', // Change this
    "nombre"     => 'John',
    "apellido"   => 'Doe',
    "email"      => 'john@doe.com',
    "telefono"   => '1111-5555',
    "localidad"  => 'Río Gallegos',
    "provincia"  => 'Z', // Santa Cruz
    "monto"      => '252.52',
    "fecha_alta" => date( "c" ),
    "pagado"     => true,
);

$result = $ep->create_order( $params );

print_r( $result );
```

#### Create shipment

```php
$params = array
(
    "pedido"          => '222555', // Get this from create_order()
    "direccion_envio" => '1081',
    "destinatario"    => 'John Doe',
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
    "correo"          => 'oca',
    "calle"           => 'Av. José de San Martín',
    "numero"          => '457',
    "piso"            => null,
    "depto"           => null,
    "codigo_postal"   => '9400',
    "provincia"       => 'Z', // Santa Cruz
    "localidad"       => '133', // Río Gallegos
);

$result = $ep->create_shipment( $params );

print_r( $result );
```

#### Get PDF print labels

```php
// Shipment IDs Array. May contain just one element
$shipmentIDS  = array( 
    '111111',
    '222222',
    '333333',
);

$result = $ep->get_print_labels( $params );

print_r( $result );
```

<a name="generic-methods"></a>
## Generic methods

You can access any resource from the [EnvíoPack API](https://www.enviopack.com/documentacion/cotiza-un-envio/) using the generic methods.
The basic structure is:

`$ep->[method]($request)`

where `request` can be:

```php
array(
    "uri" => "The resource URI, relative to https://api.enviopack.com",
    "params" => "Optional. Key=>Value array with parameters to be appended to the URL",
    "data" => "Optional. Object or String to be sent in POST and PUT requests",
    "headers" => "Optional. Key => Value array with custom headers, like content-type: application/x-www-form-urlencoded",
    "authenticate" => "Optional. Boolean to specify if the GET method has to authenticate with credentials before request. Set it to false when accessing public APIs"
)
```

Examples:

```php
// Get a resource, with optional URL params. Also you can disable authentication for public APIs
$ep->get (
    array(
        "uri" => "/resource/uri",
        "params" => array(
            "param" => "value"
        ),
        "headers" => array(
            "header" => "value"
        ),
        "authenticate" => true
    )
);

// Create a resource with "data" and optional URL params.
$ep->post (
    array(
        "uri" => "/resource/uri",
        "params" => array(
            "param" => "value"
        ),
        "headers" => array(
            "header" => "value"
        ),
        "data" => [data]
    )
);

// Update a resource with "data" and optional URL params.
$ep->put (
    array(
        "uri" => "/resource/uri",
        "params" => array(
            "param" => "value"
        ),
        "headers" => array(
            "header" => "value"
        ),
        "data" => [data]
    )
);

// Delete a resource with optional URL params.
$ep->delete (
    array(
        "uri" => "/resource/uri",
        "params" => array(
            "param" => "value"
        ),
        "headers" => array(
            "header" => "value"
        )
    )
);
```

