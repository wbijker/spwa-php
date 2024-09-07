<?php

namespace Spwa\Js;

class JsRaw extends JsVar
{

    /**
     * @var int|float|bool|null
     */
    public $value;

    /**
     * @param int|float|bool|null $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    function dump(): string
    {
        if ($this->value === null) {
            return "null";
        }

        return (string)$this->value;
    }
}