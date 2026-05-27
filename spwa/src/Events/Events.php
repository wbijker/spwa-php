<?php

namespace Spwa\Events;

/**
 * Canonical event-name strings shared between the UIElement onXxx
 * wrappers (which register them on the underlying TagDomNode) and
 * EventData::hydrate (which dispatches the matching typed event
 * class). The constant *names* are PSR-12 UPPER_SNAKE_CASE; the
 * *values* are the literal DOM event strings the browser fires.
 *
 * Sharing one source of truth keeps the two sides from drifting —
 * a typo in UIElement::onClick would otherwise silently misroute
 * the hydration lookup in EventData.
 */
final class Events
{
    // Mouse
    public const CLICK         = 'click';
    public const DBL_CLICK     = 'dblclick';
    public const MOUSE_DOWN    = 'mousedown';
    public const MOUSE_UP      = 'mouseup';
    public const MOUSE_OVER    = 'mouseover';
    public const MOUSE_OUT     = 'mouseout';
    public const MOUSE_ENTER   = 'mouseenter';
    public const MOUSE_LEAVE   = 'mouseleave';
    public const MOUSE_MOVE    = 'mousemove';
    public const CONTEXT_MENU  = 'contextmenu';

    // Keyboard
    public const KEY_DOWN  = 'keydown';
    public const KEY_UP    = 'keyup';
    public const KEY_PRESS = 'keypress';

    // Input / form
    public const CHANGE  = 'change';
    public const INPUT   = 'input';
    public const SUBMIT  = 'submit';
    public const RESET   = 'reset';
    public const SELECT  = 'select';
    public const INVALID = 'invalid';
    public const UPLOAD  = 'upload';

    // Focus
    public const FOCUS     = 'focus';
    public const BLUR      = 'blur';
    public const FOCUS_IN  = 'focusin';
    public const FOCUS_OUT = 'focusout';

    // Touch
    public const TOUCH_START  = 'touchstart';
    public const TOUCH_END    = 'touchend';
    public const TOUCH_MOVE   = 'touchmove';
    public const TOUCH_CANCEL = 'touchcancel';

    // Pointer
    public const POINTER_DOWN          = 'pointerdown';
    public const POINTER_UP            = 'pointerup';
    public const POINTER_MOVE          = 'pointermove';
    public const POINTER_OVER          = 'pointerover';
    public const POINTER_OUT           = 'pointerout';
    public const POINTER_ENTER         = 'pointerenter';
    public const POINTER_LEAVE         = 'pointerleave';
    public const POINTER_CANCEL        = 'pointercancel';
    public const GOT_POINTER_CAPTURE   = 'gotpointercapture';
    public const LOST_POINTER_CAPTURE  = 'lostpointercapture';

    // Drag and drop
    public const DRAG_START = 'dragstart';
    public const DRAG       = 'drag';
    public const DRAG_END   = 'dragend';
    public const DRAG_ENTER = 'dragenter';
    public const DRAG_LEAVE = 'dragleave';
    public const DRAG_OVER  = 'dragover';
    public const DROP       = 'drop';

    // Clipboard
    public const COPY  = 'copy';
    public const CUT   = 'cut';
    public const PASTE = 'paste';

    // Window / scroll
    public const SCROLL = 'scroll';
    public const WHEEL  = 'wheel';
    public const RESIZE = 'resize';
    public const LOAD   = 'load';
    public const ERROR  = 'error';
    public const ABORT  = 'abort';

    // Transition / animation
    public const TRANSITION_END        = 'transitionend';
    public const TRANSITION_START      = 'transitionstart';
    public const TRANSITION_CANCEL     = 'transitioncancel';
    public const TRANSITION_RUN        = 'transitionrun';
    public const ANIMATION_END         = 'animationend';
    public const ANIMATION_START       = 'animationstart';
    public const ANIMATION_ITERATION   = 'animationiteration';
    public const ANIMATION_CANCEL      = 'animationcancel';

    // Media
    public const PLAY              = 'play';
    public const PAUSE             = 'pause';
    public const ENDED             = 'ended';
    public const TIME_UPDATE       = 'timeupdate';
    public const VOLUME_CHANGE     = 'volumechange';
    public const SEEKING           = 'seeking';
    public const SEEKED            = 'seeked';
    public const LOADED_DATA       = 'loadeddata';
    public const LOADED_METADATA   = 'loadedmetadata';
    public const CAN_PLAY          = 'canplay';
    public const CAN_PLAY_THROUGH  = 'canplaythrough';
    public const WAITING           = 'waiting';
    public const PLAYING           = 'playing';
    public const RATE_CHANGE       = 'ratechange';
    public const DURATION_CHANGE   = 'durationchange';
    public const PROGRESS          = 'progress';
    public const STALLED           = 'stalled';
    public const SUSPEND           = 'suspend';
    public const EMPTIED           = 'emptied';
}
