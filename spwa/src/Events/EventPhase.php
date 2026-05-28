<?php

namespace Spwa\Events;

/**
 * Which phase a native DOM listener fires in — maps directly to the
 * `useCapture` argument of addEventListener. Bubble (the default) fires as
 * the event bubbles up from the target; Capture fires on the way down.
 */
enum EventPhase
{
    case Bubble;
    case Capture;

    /** The addEventListener useCapture flag for this phase. */
    public function useCapture(): bool
    {
        return $this === self::Capture;
    }
}
