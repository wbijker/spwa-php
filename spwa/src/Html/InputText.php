<?php

namespace Spwa\Html;

use Spwa\Js\JsFunction;
use Spwa\Js\JsLiteral;
use Spwa\Nodes\HtmlContentNode;


class InputText extends HtmlContentNode
{
    /**
     * @param mixed|null $key
     * @param string|null $class
     * @param string|null $id
     * @param array|null $style
     * @param array|null $data
     * @param MouseEvents|null $mouse
     * @param string|null $value
     * @param string|null $bind
     * @param (callable(String $value): void)|null $onChange
     * @param (callable(String $value): void)|null $onInput
     * @param (callable(String $value): void)|null $onFocus
     * @param (callable(String $value): void)|null $onBlur
     */
    public function __construct(
        mixed           $key = null,
        ?string         $class = null,
        ?string         $id = null,
        ?array          $style = null,
        ?array          $data = null,
        ?MouseEvents    $mouse = null,

        ?string         $value = null,
//        private ?string &$bind = null,

                        $onChange = null,
                        $onInput = null,
                        $onFocus = null,
                        $onBlur = null
    )
    {
        parent::__construct($key, $class, $id, $style, $data, $mouse);

        // 1. remove events from state
        // 2. Store events in node itself.

        $this->setEvents([
            "onChange" => $onChange,
            "onInput" => $onInput,
            "onFocus" => $onFocus,
            "onBlur" => $onBlur,
        ]);

//        if ($bind !== null) {
//            $this->bindings = &$bind;
//            $func = new JsFunction("handleInput", new JsLiteral('event'));
//            $this->attrs['onInput'] = $func->dump();
//            $this->attrs['value'] = $bind;
//        }
    }

    function tag(): string
    {
        return "input";
    }
}