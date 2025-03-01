<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;
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

    public static function click(\Closure $param): MouseEvents
    {
        return new MouseEvents(onClick: $param);
    }

    function setEvents(HtmlNode $owner): void
    {
        $owner->setEvents([
            "onClick" => $this->onClick,
            "onDoubleClick" => $this->onDoubleClick,
            "onMouseDown" => $this->onMouseDown,
            "onMouseUp" => $this->onMouseUp,
            "onMouseMove" => $this->onMouseMove,
            "onMouseEnter" => $this->onMouseEnter,
            "onMouseLeave" => $this->onMouseLeave,
            "onMouseOver" => $this->onMouseOver,
            "onMouseOut" => $this->onMouseOut,
            "onContextMenu" => $this->onContextMenu,
        ]);
    }

}