<?php
/**
 * EnvíoPack Api Integration Library
 * @author    Martin Briglia, MGS Creativa
 * @url http://www.mgscreativa.com
 * @copyright Copyright (C) 2018 MGS Creativa - All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

if ( ! isset( $_GET['tipo'], $_GET['id'] ) )
{
    http_response_code( 200 );
    exit();
} else {
    echo 'Tipo: ' . _GET['tipo'] . ' ID: ' . $_GET['id'];
}
