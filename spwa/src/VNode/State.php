<?php

namespace Spwa\VNode;

enum State
{
    /** State is destroyed when the component is destroyed. */
    case Bound;

    /** State persists even after the component is destroyed. */
    case Unbound;
}
