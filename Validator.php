<?php

namespace alexeevdv\ip;

class Validator extends \yii\validators\Validator {

    /**
     * IP range
     * @var array
     */
    public $range = [];
    
    /**
     * Allow private addresses such as 192.168.1.1
     * @var bool
     */
    public $allowPrivate = true;
    
    /**
     * Allow reserved ranges
     * @var bool
     */
    public $allowReserved = true;

    /**
     * Validator initialization
     * @throws \yii\base\InvalidConfigException
     */
    public function init() {
        parent::init();
        $this->range = (array) $this->range;
        foreach($this->range as $range) {
            if (!$this->validateRange($range)) {
                throw new \yii\base\InvalidConfigException("`$range` is not a valid range value");
            }
        }        
    }


    /**
     * Validates IP address
     * @param mixed $value
     * @return null|array
     */
    protected function validateValue($value) {
        
        if (!$this->validateIP($value)) {
            return ["{attribute} is not a valid IPv4 address", []];
        }
        
        if (count($this->range)) {
            foreach($this->range as $range) {
                if ($this->valueIsInRange($value, $range)) {
                    return null;
                }
            }
            return ["{attribute} is not in allowed IPv4 range", []];
        }

        return null;
    }
    
    protected function validateIP($value) {
        $flags = FILTER_FLAG_IPV4;        
        if (!$this->allowPrivate)  $flags |= FILTER_FLAG_NO_PRIV_RANGE;
        if (!$this->allowReserved) $flags |= FILTER_FLAG_NO_RES_RANGE;
        
        return filter_var($value, FILTER_VALIDATE_IP, $flags);        
    }
    
    /**
     * @todo Implement
     */
    protected function validateRange($value) {
        // If it is just an IP address
        if ($this->validateIP($value)) {
            return true;
        }
        
        // No netmask
        if (strpos($value, "/") === false) {
            return false;
        }
        
        list($ip, $mask) = explode("/", $value, 2);
        
        if (!$this->validateIP($ip)) {
            return false;
        }
        
        if (!is_numeric($mask) || $mask != (int) $mask) {
            return false;
        }
        
        $mask = (int) $mask;
        
        if ($mask <= 0 || $mask > 32) {
            return false;
        }        
        
        return true;
    }

    protected function valueIsInRange($value, $range) {
        
        if ($this->validateIP($range)) {
            return $value == $range;
        }
        
        list($ip, $mask) = explode("/", $range);
        
        $l_range = ip2long($ip);
        $l_value = ip2long($value);
        
        $wildcard = pow(2, ( 32 - (int)$mask)) - 1;
        $l_mask = ~$wildcard;
        return (($l_value & $l_mask) == ($l_range & $l_mask));
    }
}
