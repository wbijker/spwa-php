<?php

namespace Spwa\VNode;

enum RenderPhase
{
    /** First-time page render. Full HTML output is required. */
    case Initial;

    /** OLD tree during an event/refresh POST — used as the basis for diffing. */
    case DiffOld;

    /** NEW tree during an event/refresh POST — compared against OLD. */
    case Patch;
}
