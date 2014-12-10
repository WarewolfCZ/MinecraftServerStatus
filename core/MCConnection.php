<?php

/**
 * @author WarewolfCZ
 */
require_once("MCPacket.php");

class MCConnection {

    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function writePacket(MCPacket $packet) {
        $data = $packet->flush();
        $packet->writeVarInt(strlen($data));
        $length = $packet->flush();
        echo "Sending: " . substr(chunk_split(bin2hex($length), 4, " "), 0, -1) . "<br />\n";
        echo "Sending: " . substr(chunk_split(bin2hex($data), 4, " "), 0, -1) . "<br />\n";
        // send data length first
        fwrite($this->conn, $length);
        fwrite($this->conn, $data);
    }

    public function readPacket() {
        $result = NULL;
        // first value is always length
        $length = $this->freadVarInt();
        $buffer = fread($this->conn, $length);
        if ($buffer != FALSE) {
            $result = new MCPacket($buffer);
            echo "Received: " . substr(chunk_split(bin2hex($buffer), 4, " "), 0, -1) . "<br />\n";
        }
        return $result;
    }

    public function freadVarInt() {
        $result = 0;
        for ($i = 0; $i < 5; $i++) {
            $byte = fread($this->conn, 1);
            if ($byte == FALSE) {
                break;
            }
            // bindec is expecting string, not binary string => not usable
            $part = hexdec(bin2hex($byte));
            //printf("part: %d <br/>", $part);
            // add part to result (shift by i * 7)
            $result |= ($part & 0x7F) << 7 * $i;
            if (($part & 0x80) == 0) { // 8th bit is set to zero => last octet of VarInt
                return $result;
            }
        }
        throw MCException("Server sent invalid length");
    }

}
