<?php

namespace Spwa\Html;


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

                        $onChange = null,
                        $onInput = null,
                        $onFocus = null,
                        $onBlur = null
    )
    {
        parent::__construct($key, $class, $id, $style, $data, $mouse);


        $this->setEvents([
            "onChange" => $onChange,
            "onInput" => $onInput,
            "onFocus" => $onFocus,
            "onBlur" => $onBlur,
        ]);

        $this->setAttrs([
            'value' => $value,
        ]);
    }

    function tag(): string
    {
        return "input";
    }
}