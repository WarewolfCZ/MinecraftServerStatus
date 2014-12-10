<?php

/**
 * @author WarewolfCZ
 */
require_once('MCPacket.php');

class MCPinger {

    private $conn;
    private $host;
    private $port;
    private $version;
    private $pingToken;
    

    public function __construct($connection, $host, $port = 0, $version = 47, $pingToken = NULL) {
        $this->conn = $connection;
        $this->host = $host;
        $this->port = $port;
        $this->version = $version;
        if ($pingToken == NULL) {
            $pingToken = rand(0, (1 << 63) - 1);
        }
        $this->pingToken = $pingToken;
    }

    /**
     * Initialize communication
     */
    public function handshake() {
        $packet = new MCPacket();
        $packet->writeVarInt(0);
        $packet->writeVarInt($this->version);
        $packet->writeUtf($this->host);
        $packet->writeShort($this->port);
        $packet->writeVarInt(1);  // Intention to query status
        echo "handshake data: " . substr(chunk_split(bin2hex($packet->getBuffer()), 4, " "), 0, -1) . "<br />\n";
        $this->conn->writePacket($packet);
        
    }

    /**
     * Ping server and return latency [ms]
     * @return float
     * @throws MCPingException
     */
    public function ping() {
        // create and send ping request
        $packet = new MCPacket();
        $packet->writeVarInt(1); // Test ping
        $packet->writeLong($this->pingToken);
        $sent = microtime(true);
        //echo "ping data: " . substr(chunk_split(bin2hex($packet->getBuffer()), 4, " "), 0, -1) . "<br />\n";
        $this->conn->writePacket($packet);

        // receive ping response
        $response = $this->conn->readPacket();
        if ($response != NULL) {
            $received = microtime(true);
            if ($response->readVarInt() != 1) {
                throw new MCPingException("Received invalid ping response packet.");
            }
            $receivedToken = $response->readLong();
            if ($receivedToken != $this->pingToken) {
                throw new MCPingException("Received mangled ping response packet (expected token \"" .
                $this->pingToken . "\", received \"" . $receivedToken . "\")");
            }

            $delta = ($received - $sent) * 1000.0;
            // We have no trivial way of getting a time delta :(
            return $delta; //(delta.days * 24 * 60 * 60 + delta.seconds) * 1000 + delta.microseconds / 1000.0
        }
    }

}
