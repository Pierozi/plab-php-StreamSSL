<?php

require_once( dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/socket_client.php' );

use PLAB\Stream\Client;

$Client = new Client('tcp://127.0.0.1:1234');
$Client->readSTDIN();

