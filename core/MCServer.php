<?php
/**
 * @author WarewolfCZ
 */
require_once('exception/MCPingException.php');
require_once('exception/MCConnException.php');
require_once('MCConnection.php');
require_once('MCPinger.php');

class MCServer {
    
    private $host;
    private $port;
    private static $CONNECT_TIMEOUT = 10; // connection timeout in seconds
    
    public function __construct($mchost, $mcport=25565) {
        //TODO: validate host and port
        $this->host = htmlspecialchars($mchost);
        $this->port = (int) $mcport;
    }
    
    /**
     * Ping server and return latency
     * @throws MCPingException
     **/
    public function ping() {
        $latency = -1;
        $fp = fsockopen($this->host, $this->port, $errno, $errstr, self::$CONNECT_TIMEOUT);
        if (!$fp) {
            throw new MCConnException( $this->host . ": " . $errstr);
        } else {
            $conn = new MCConnection($fp);
            $pinger = new MCPinger($conn, $this->host, $this->port, 47, 1234);
            $pinger->handshake();
            $latency = $pinger->ping();
            fclose($fp);
        }
        return $latency;
    }
    
    /**
     * Get server status
     * @return type
     * @throws MCConnException
     */
    public function status() {
        $fp = fsockopen($this->host, $this->port, $errno, $errstr, self::$CONNECT_TIMEOUT);
        if (!$fp) {
            throw new MCConnException( $this->host . ": " . $errstr);
        } else {
            $conn = new MCConnection($fp);
            $pinger = new MCPinger($conn, $this->host, $this->port, 47, 1234);
            $pinger->handshake();
            return $pinger->getStatus();
        }
    }
}