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
    private static $BUFFER_SIZE = 100;

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

    public function handshake() {
        $packet = new MCPacket();
        $packet->writeVarInt(0);
        $packet->writeVarInt($this->version);
        $packet->writeUtf($this->host);
        $packet->writeShort($this->port);
        $packet->writeVarInt(1);  // Intention to query status
        echo "handshake data: " . substr(chunk_split(bin2hex($packet->getData()), 4, " "), 0, -1);
        fwrite($this->conn, $packet->getData());
    }

    public function ping() {
        // create and send ping request
        $packet = new MCPacket();
        $packet->writeVarInt(1); // Test ping
        $packet->writeLong($this->pingToken);
        $sent = microtime(true);
        fwrite($this->conn, $packet->getData());

        // receive ping response
        $buffer = fread($this->conn, self::$BUFFER_SIZE);
        if ($buffer != FALSE) {
            $response = new MCPacket($buffer);
            $received = microtime(true);
            if ($response->readVarInt() != 1) {
                throw new MCPingException("Received invalid ping response packet.");
            }
            $receivedToken = $response->readLong();
            if ($receivedToken != $this->pingToken) {
                throw new MCPingException("Received mangled ping response packet (expected token \"" .
                $this->pingToken . "\", received \"" . $receivedToken . "\")");
            }

            $delta = ($received - $sent);
            // We have no trivial way of getting a time delta :(
            return $delta; //(delta.days * 24 * 60 * 60 + delta.seconds) * 1000 + delta.microseconds / 1000.0
        }
    }

}
