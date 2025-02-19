<?php

namespace Spwa\Html;

use Spwa\Js\JsFunction;
use Spwa\Js\JsLiteral;
use Spwa\Nodes\HtmlContentNode;
use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PathInfo;
use Spwa\Nodes\StateManager;


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
        mixed        $key = null,
        ?string      $class = null,
        ?string      $id = null,
        ?array       $style = null,
        ?array       $data = null,
        ?MouseEvents $mouse = null,

        ?string      $value = null,
        private ?string      &$bind = null,

                     $onChange = null,
                     $onInput = null,
                     $onFocus = null,
                     $onBlur = null
    )
    {
        parent::__construct($key, $class, $id, $style, $data, $mouse);

        if ($bind !== null) {
            $this->attrs['value'] = $bind;
            /*function flatAttr(Node $owner): array
            {
                $ret = [];
                foreach ($this as $key => $value) {
                    if ($value != null) {
                        $func = new JsFunction("handleEvent", $key, new JsLiteral('event'));
                        $ret[$key] = $func->dump();
                    }
                }
                return $ret;
            }*/

        }
//        $this->attrs['value'] = $value;

    }

    function initialize(?Node $parent, PathInfo $current, StateManager $manager): void
    {
        parent::initialize($parent, $current, $manager);

        if ($this->bind != null) {
            $func = new JsFunction("handleInput", new JsLiteral('event'));
            $this->attrs['onInput'] = $func->dump();
        }
    }


    function tag(): string
    {
        return "input";
    }
}