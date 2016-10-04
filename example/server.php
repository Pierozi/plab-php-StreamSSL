<?php

require_once( dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/socket_server.php' );

use PLAB\Stream\Server;

$Server = new Server('tcp://0.0.0.0:1234', [
    'local_cert' => __DIR__ . '/cert/cert.pem',
]);

$Server->listen();

