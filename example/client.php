<?php

require_once( dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/socket_client.php' );

use PLAB\Stream\Client;

$Client = new Client('tcp://127.0.0.1:1234', [
	'ssl' => [
		'allow_self_signed' => true,
        'verify_peer' 		=> false,
        'verify_peer_name' 	=> false,
    ],
]);
$Client->readSTDIN();

