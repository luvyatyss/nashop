<?php

/**
 * Created by PhpStorm.
 * User: luvyatyss
 * Date: 12/16/2015
 * Time: 12:37 AM
 */
abstract class Enum
{
    protected $value;

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        if ($this->isValidEnumValue($value))
            $this->value = $value;
        else
            throw new Exception("Invalid type specified!");
    }

    public function isValidEnumValue($checkValue)
    {
        $reflector = new ReflectionClass(get_class($this));
        foreach ($reflector->getConstants() as $validValue) {
            if ($validValue == $checkValue) return true;
        }
        return false;
    }

    function __construct($value)
    {
        $this->setValue($value);
    }

    function __get($property)
    {
        return $this->value;
    }

    function __set($property, $value)
    {
        $this->setValue($value);
    }

    function __toString()
    {
        return (string)$this->value;
    }
}

class Controls extends Enum
{
    const
        _default = "0"
    , Insert = "1"
    , Update = "2"
    , Delete = "3"
    , Information = "4";
}