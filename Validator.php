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
        
        $flags = FILTER_FLAG_IPV4;        
        if (!$this->allowPrivate)  $flags |= FILTER_FLAG_NO_PRIV_RANGE;
        if (!$this->allowReserved) $flags |= FILTER_FLAG_NO_RES_RANGE;
        
        if (!filter_var($value, FILTER_VALIDATE_IP, $flags)) {
            return ["{attribute} is not a valid IPv4 address", []];
        }
        
        foreach($this->range as $range) {
            if (!$this->valueIsInRange($value, $range)) {
                return ["{attribute} is not in allowed IPv4 range", []];
            }
        }

        return null;
    }
    
    /**
     * @todo Implement
     */
    protected function validateRange($value) {
        return true;
    }

    protected function valueIsInRange($value, $range) {
        return true;
    }
}
