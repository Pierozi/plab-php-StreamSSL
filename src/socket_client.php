<?php

namespace PLAB\Stream {

class Client
{

    /**
     * Encryption TLS
     * @const int
     */
    const ENCRYPTION_TLS    = STREAM_CRYPTO_METHOD_TLS_CLIENT;

    /**
     * Constructor
     * @param $uri
     */
    public function __construct($uri) {
        $this->uri = $uri;
    }

    /**
     * read user choice command
     */
    public function readSTDIN() {

        do {
            echo 'Enter 1 to get a Star Wars Quote', PHP_EOL;
            echo 'Enter 2 to get a Star Trek Quote', PHP_EOL;
            echo 'Enter q to quit', PHP_EOL;
            echo 'Enter e to stop server', PHP_EOL;
            echo 'Any other choice retrieves a random quote', PHP_EOL;
            echo 'Press enter to continue', PHP_EOL, PHP_EOL;

            $key = trim(fgets(STDIN));

            if ('q' === $key) {
                break;
            }

            switch ($key) {
                case '1':
                    $command = 'starwars';
                    break;

                case '2':
                    $command = 'startrek';
                    break;

                case 'e':
                    $command = 'shutdown';
                    break;

                default:
                    $command = 'dummy';
                    break;
            }

            $this->send($command);
        } while (true);
    }

    /**
     * Send data to server
     * @param $command
     */
    protected function send($command) {
        $client = stream_socket_client($this->uri, $errorCode, $errorString, 30,
            STREAM_CLIENT_CONNECT);

        if (!is_resource($client)) {
            echo 'Could not connect, ', $errorString, PHP_EOL;
            die($errorCode);
        }

        stream_socket_enable_crypto($client, true, static::ENCRYPTION_TLS);

        fwrite($client, $command);
        echo 'Quote returned: ', fgets($client);

        fclose($client);
    }
}

}
