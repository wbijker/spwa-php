<?php

namespace Spwa\Events;

class EventData
{
    private const MOUSE_EVENTS = [
        'click', 'dblclick', 'mousedown', 'mouseup',
        'mouseover', 'mouseout', 'mouseenter', 'mouseleave',
        'mousemove', 'contextmenu',
    ];

    private const KEYBOARD_EVENTS = [
        'keydown', 'keyup', 'keypress',
    ];

    private const INPUT_EVENTS = [
        'change', 'input', 'select', 'invalid',
    ];

    private const FOCUS_EVENTS = [
        'focus', 'blur', 'focusin', 'focusout',
    ];

    private const TOUCH_EVENTS = [
        'touchstart', 'touchend', 'touchmove', 'touchcancel',
    ];

    private const POINTER_EVENTS = [
        'pointerdown', 'pointerup', 'pointermove',
        'pointerover', 'pointerout', 'pointerenter', 'pointerleave',
        'pointercancel', 'gotpointercapture', 'lostpointercapture',
    ];

    private const DRAG_EVENTS = [
        'dragstart', 'drag', 'dragend',
        'dragenter', 'dragleave', 'dragover', 'drop',
    ];

    private const CLIPBOARD_EVENTS = [
        'copy', 'cut', 'paste',
    ];

    private const TRANSITION_EVENTS = [
        'transitionend', 'transitionstart', 'transitioncancel', 'transitionrun',
    ];

    private const ANIMATION_EVENTS = [
        'animationend', 'animationstart', 'animationiteration', 'animationcancel',
    ];

    private const MEDIA_EVENTS = [
        'play', 'pause', 'ended', 'timeupdate',
        'volumechange', 'seeking', 'seeked',
        'loadeddata', 'loadedmetadata', 'canplay', 'canplaythrough',
        'waiting', 'playing', 'ratechange', 'durationchange',
        'progress', 'stalled', 'suspend', 'emptied', 'abort',
    ];

    /**
     * Hydrate raw event data into a strongly typed event object.
     */
    public static function hydrate(string $event, mixed $raw): mixed
    {
        if (in_array($event, self::MOUSE_EVENTS)) return MouseEvent::from($raw);
        if (in_array($event, self::KEYBOARD_EVENTS)) return KeyboardEvent::from($raw);
        if (in_array($event, self::INPUT_EVENTS)) return InputEvent::from($raw);
        if (in_array($event, self::TOUCH_EVENTS)) return TouchEvent::from($raw);
        if (in_array($event, self::POINTER_EVENTS)) return PointerEvent::from($raw);
        if (in_array($event, self::DRAG_EVENTS)) return DragEvent::from($raw);
        if (in_array($event, self::CLIPBOARD_EVENTS)) return ClipboardEvent::from($raw);
        if ($event === 'wheel') return WheelEvent::from($raw);
        if ($event === 'scroll') return ScrollEvent::from($raw);
        if ($event === 'resize') return ResizeEvent::from($raw);
        if (in_array($event, self::TRANSITION_EVENTS)) return TransitionEvent::from($raw);
        if (in_array($event, self::ANIMATION_EVENTS)) return AnimationEvent::from($raw);
        if (in_array($event, self::MEDIA_EVENTS)) return MediaEvent::from($raw);
        if (in_array($event, self::FOCUS_EVENTS)) return null;

        // submit, reset, load, error, and unknown events — pass raw
        return $raw;
    }
}
