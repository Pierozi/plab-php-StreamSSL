<?php

namespace PLAB\Stream {

class Server
{

    protected $WarsQuote;
    protected $TrekQuote;
    protected $Quotes;

    /**
     * Encryption TLS
     * @const int
     */
    const ENCRYPTION_TLS    = STREAM_CRYPTO_METHOD_TLS_SERVER;

    /**
     * @var Resource Socket of Stream Server
     */
    protected $Socket;

    /**
     * Constructor
     * @param $uri
     * @param $sslContext
     */
    public function __construct($uri, $sslContext) {

        echo 'Instance of ' . __CLASS__, PHP_EOL;

        $this->WarsQuote    = file(__DIR__ . DIRECTORY_SEPARATOR . 'starwars.txt');
        $this->TrekQuote    = file(__DIR__ . DIRECTORY_SEPARATOR . 'startrek.txt');
        $this->Quotes       = array_merge($this->WarsQuote, $this->TrekQuote);

        $this->open($uri, $sslContext);
    }

    /**
     * Open server connection
     * @param $uri
     * @param $sslContext
     */
    protected function open($uri, $sslContext) {

        $streamContext  = stream_context_create(array('ssl' => $sslContext));
        $this->Socket   = stream_socket_server($uri, $errno, $errstr,
            STREAM_SERVER_BIND | STREAM_SERVER_LISTEN
            , $streamContext);

        echo 'Server Stream opening on [ ' . $uri . ' ]', PHP_EOL;

        if (false === $this->Socket) {
            echo 'ERROR: ', $errstr, PHP_EOL;
            exit($errno);
        }
    }

    /**
     * Close server connection
     */
    protected function close() {

        echo 'Shutting Down Server...', PHP_EOL;
        fclose($this->Socket);
    }

    /**
     * Listen client connection
     */
    public function listen() {

        echo 'Listening...', PHP_EOL;

        $shutdown = false;

        do {

            if (!$this->has())
                continue; //Nothing bytes change on socket, continue to listen

            //Accept new client connection on server socket
            $client = stream_socket_accept($this->Socket, 5, $peerName);

            if (!is_resource($client))
                die("Operation timed out (nothing to accept).\n");

            print "connection from => " . $peerName . "\n";

            //Enable encryption on client socket
            stream_socket_enable_crypto($client, true, static::ENCRYPTION_TLS);

            $this->read($client, $shutdown);

            if ($shutdown) {
                $this->close();
                return;
            }

        } while (true);
    }

    /**
     * Listen read change on stream
     * @return bool true if change read found
     */
    protected function has() {

        $read   = array($this->Socket);
        $write  = null;
        $except = null;

        //Listen change on socket
        stream_select($read, $write, $except, 5, 0);

        return !empty($read);
    }

    protected function read($client, & $shutdown) {

        //Read data from client socket
        $buf = fread($client, 20);

        switch ($buf) {
            case 'starwars':
                $key = array_rand($this->WarsQuote);
                $quote = $this->WarsQuote[$key];
                break;

            case 'startrek':
                $key = array_rand($this->TrekQuote);
                $quote = $this->TrekQuote[$key];
                break;

            case 'shutdown':
                $shutdown = true;
                fclose($client);
                return;

            default:
                $key = array_rand($this->Quotes);
                $quote = $this->Quotes[$key];
        }


        echo "Sending...", PHP_EOL;

        fwrite($client, $quote); //Writing data on client socket
        fclose($client); //Close client socket
    }
}

}
