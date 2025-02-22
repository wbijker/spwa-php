<?php

namespace Spwa\Html;

class TextArea extends HtmlContentNode
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
        mixed        $key = null,
        ?string      $class = null,
        ?string      $id = null,
        ?array       $style = null,
        ?array       $data = null,
        ?MouseEvents $mouse = null,
        ?int         $rows = null,
        ?int         $cols = null,

        ?string      $value = null,

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
            'rows' => $rows,
            'cols' => $cols,
        ]);
        $this->children = [new HtmlText($value ?? "")];
    }

    function initialize(?Node $parent, PathInfo $current, StateManager $manager): void
    {
        $this->path = $current->setKey($this->key);
        // child contains the same path
        $this->children[0]->initialize($this, $this->path, $manager);
    }

    function tag(): string
    {
        return "textarea";
    }
}