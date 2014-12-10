<?php
/**
 * @author WarewolfCZ
 */
require_once('exception/MCPingException.php');
require_once('exception/MCConnException.php');
require_once('MCPinger.php');

class MCServer {
    
    private $host;
    private $port;
    private static $CONNECT_TIMEOUT = 10; // connection timeout in seconds
    
    public function __construct($mchost, $mcport=25565) {
        //TODO: validate host and port
        $this->host = $mchost;
        $this->port = $mcport;
    }
    
    /**
     * Ping server and return latency
     * @throws MCPingException
     **/
    public function ping() {
        $delay = -1;
        $fp = fsockopen($this->host, $this->port, $errno, $errstr, self::$CONNECT_TIMEOUT);
        if (!$fp) {
            throw new MCConnException( $this->host . ": " . $errstr);
        } else {
            $pinger = new MCPinger($fp, $this->host, $this->port);
            $pinger->handshake();
            //fwrite($fp, "You message");
            /*while (!feof($fp)) {
                echo fgets($fp, 128);
            }*/
            fclose($fp);
        }
        
        if (true) {
            throw new MCPingException("Cannot ping server " . $this->host);
        }
        return $delay;
    }
}