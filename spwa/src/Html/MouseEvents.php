<?php

namespace Spwa\Html;

use Spwa\Js\JsFunction;
use Spwa\Js\JsLiteral;
use Spwa\Nodes\Node;

class MouseEvents
{
    /**
     * @param (callable(): void)|null $onClick
     * @param (callable(): void)|null $onDoubleClick
     * @param (callable(): void)|null $onMouseDown
     * @param (callable(): void)|null $onMouseUp
     * @param (callable(): void)|null $onMouseMove
     * @param (callable(): void)|null $onMouseEnter
     * @param (callable(): void)|null $onMouseLeave
     * @param (callable(): void)|null $onMouseOver
     * @param (callable(): void)|null $onMouseOut
     * @param (callable(): void)|null $onContextMenu
     */
    public function __construct(
        public $onClick = null,
        public $onDoubleClick = null,
        public $onMouseDown = null,
        public $onMouseUp = null,
        public $onMouseMove = null,
        public $onMouseEnter = null,
        public $onMouseLeave = null,
        public $onMouseOver = null,
        public $onMouseOut = null,
        public $onContextMenu = null
    )
    {
    }

    function flatAttr(Node $owner): array
    {
        $ret = [];
        foreach ($this as $key => $value) {
            if ($value != null) {
                $func = new JsFunction("handleEvent", $key, new JsLiteral('event'));
                $ret[$key] = $func->dump();
            }
        }
        return $ret;
    }
}