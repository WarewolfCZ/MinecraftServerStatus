<?php

class MCPacket {
    private $data;
    private $position;
    
    public function __construct($buffer=NULL) {
        $this->data = $buffer;
        $this->position = 0;
    }
    
    /**
     * Store VarInt value to data buffer
     */
    public function addVarInt($value) {
        var $remaining = (int) $value;
        for ($i = 1; $i < 5; $i++) {
            if ($remaining & ~0x7F == 0) { // 8th bit is 0
                $this->data .= pack("C", $remaining); // pack as unsigned char
                return;
            } else {
                // clear everything but 7 least significant bits and set 8th bit to 1
                $this->data .= pack("C", $remaining & 0x7F | 0x80); // pack as unsigned char
            }
            // shift right
            $remaining = $remaining >> 7;
        }
        throw new MCException("The value " . $value . " is too big to store in VarInt");
    }
    
    /**
     * Store long value to data buffer
     */
    public function addLong($value) {
        $this->data .= pack("L", (long) $value);
    }
    
    public function addShort($value) {
        //$this->data .= pack("v", (int) $value);
    }
    
    /* 
     * Store string to data buffer
     */
    public function addString($value) {
        $this->data .= pack("v", $value);
    }
    
    /**
     * Parse next VarInt value and return it, buffer position is incremented
     */
    public function readVarInt() {
        $result = NULL;
        if ($this->data != NULL && strlen($this->data) >= $this->position + 4) {
            $arr = unpack("I", substr($this->data, $this->position, 4));
            if (count($arr) > 0) {
                if ($arr[1] < 0) {
                    $result = (int) sprintf('%u', $arr[1]);
                } else {
                    $result = $arr[1];
                }
                $this->position += 4;
            }
        }
        return $result;
    }
    
    /**
     * Parse next long value and return it, buffer position is incremented
     */
    public function readLong() {
        $result = NULL;
        if ($this->data != NULL && strlen($this->data) >= $this->position + 4) {
            $arr = unpack("L", /*substr(*/$this->data/*, $this->position, 4)*/);
            if (count($arr) > 0) {
                $result = $arr[1];
                $this->position += 4;
            }
        }
        return $result;
    }
    
    /**
     * Clear data value and reset position counter
     */
    public function clearData() {
        $this->data = NULL;
        $this->position = 0;
    }
    
    /**
     * Reset position counter to zero
     **/
    public function rewind() {
        $this->position = 0;
    }
    
    /**
     * Return value of data buffer
     */
    public function getData() {
        return $this->data;
    }
}
