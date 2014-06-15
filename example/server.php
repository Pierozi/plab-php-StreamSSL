<?php

require_once( dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/socket_server.php' );

use PLAB\Stream\Server;

$Server = new Server('tcp://0.0.0.0:1234', array(
    'local_cert' => __DIR__ . '/cert/server.pem',
    'passphrase' => 'MY_PASSPHRASE',
    'allow_self_signed' => true,
    'verify_peer' => false
));

$Server->listen();

