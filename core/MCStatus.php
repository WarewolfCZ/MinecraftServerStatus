<?php

/**
 * Status response from server
 *
 * @author WarewolfCZ
 */
class MCStatus {
    private $latency;
    private $values;
    
    /**
     * 
     * @param type $value
     */
    public function setLatency($value) {
        $this->latency = $value;
    }
    
    /**
     * 
     * @param type $json
     * @throws MCException
     */
    public function decodeJson($json) {
        $ret = $this->values = json_decode($json, true); 
        if ($ret == NULL || $ret == FALSE) {
            throw new MCException("Cannot decode status JSON. Error code: " . json_last_error());
        }
    }
    
    /**
     * 
     * @return float
     */
    public function getLatency() {
        return $this->latency;
    }
    
    /**
     * 
     * @return int
     */
    public function getOnlinePlayers() {
        $result = -1;
        if ($this->values != NULL) {
            $result = $this->values["players"]["online"];
        }
        return $result;
    }
    
    /**
     * 
     * @return int
     */
    public function getMaxPlayers() {
        $result = -1;
        if ($this->values != NULL) {
            $result = $this->values["players"]["max"];
        }
        return $result;
    }
    
    /**
     * 
     * @return string
     */
    public function getVersion() {
        $result = NULL;
        if ($this->values != NULL) {
            $result = $this->values["version"]["name"];
        }
        return $result;
    }
    
        
    /**
     * 
     * @return string
     */
    public function getDescription() {
        $result = NULL;
        if ($this->values != NULL) {
            $result = $this->values["description"];
        }
        return $result;
    }
}
