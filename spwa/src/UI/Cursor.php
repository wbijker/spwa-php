<?php

namespace Spwa\UI;

enum Cursor: string
{
    case Auto = 'auto';
    case Default = 'default';
    case Pointer = 'pointer';
    case Wait = 'wait';
    case Text = 'text';
    case Move = 'move';
    case Help = 'help';
    case NotAllowed = 'not-allowed';
    case None = 'none';
    case ContextMenu = 'context-menu';
    case Progress = 'progress';
    case Cell = 'cell';
    case Crosshair = 'crosshair';
    case VerticalText = 'vertical-text';
    case Alias = 'alias';
    case Copy = 'copy';
    case NoDrop = 'no-drop';
    case Grab = 'grab';
    case Grabbing = 'grabbing';
    case AllScroll = 'all-scroll';
    case ColResize = 'col-resize';
    case RowResize = 'row-resize';
    case NResize = 'n-resize';
    case EResize = 'e-resize';
    case SResize = 's-resize';
    case WResize = 'w-resize';
    case NeResize = 'ne-resize';
    case NwResize = 'nw-resize';
    case SeResize = 'se-resize';
    case SwResize = 'sw-resize';
    case EwResize = 'ew-resize';
    case NsResize = 'ns-resize';
    case NeswResize = 'nesw-resize';
    case NwseResize = 'nwse-resize';
    case ZoomIn = 'zoom-in';
    case ZoomOut = 'zoom-out';

    public function toClass(): string
    {
        return 'cursor-' . $this->value;
    }

    public function getCssValue(): string
    {
        return $this->value;
    }
}
