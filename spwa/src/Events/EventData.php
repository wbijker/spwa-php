<?php

namespace Spwa\Events;

class EventData
{
    private const MOUSE_EVENTS = [
        Events::CLICK, Events::DBL_CLICK, Events::MOUSE_DOWN, Events::MOUSE_UP,
        Events::MOUSE_OVER, Events::MOUSE_OUT, Events::MOUSE_ENTER, Events::MOUSE_LEAVE,
        Events::MOUSE_MOVE, Events::CONTEXT_MENU,
    ];

    private const KEYBOARD_EVENTS = [
        Events::KEY_DOWN, Events::KEY_UP, Events::KEY_PRESS,
    ];

    private const INPUT_EVENTS = [
        Events::CHANGE, Events::INPUT, Events::SELECT, Events::INVALID,
    ];

    private const FOCUS_EVENTS = [
        Events::FOCUS, Events::BLUR, Events::FOCUS_IN, Events::FOCUS_OUT,
    ];

    private const TOUCH_EVENTS = [
        Events::TOUCH_START, Events::TOUCH_END, Events::TOUCH_MOVE, Events::TOUCH_CANCEL,
    ];

    private const POINTER_EVENTS = [
        Events::POINTER_DOWN, Events::POINTER_UP, Events::POINTER_MOVE,
        Events::POINTER_OVER, Events::POINTER_OUT, Events::POINTER_ENTER, Events::POINTER_LEAVE,
        Events::POINTER_CANCEL, Events::GOT_POINTER_CAPTURE, Events::LOST_POINTER_CAPTURE,
    ];

    private const DRAG_EVENTS = [
        Events::DRAG_START, Events::DRAG, Events::DRAG_END,
        Events::DRAG_ENTER, Events::DRAG_LEAVE, Events::DRAG_OVER, Events::DROP,
    ];

    private const CLIPBOARD_EVENTS = [
        Events::COPY, Events::CUT, Events::PASTE,
    ];

    private const TRANSITION_EVENTS = [
        Events::TRANSITION_END, Events::TRANSITION_START, Events::TRANSITION_CANCEL, Events::TRANSITION_RUN,
    ];

    private const ANIMATION_EVENTS = [
        Events::ANIMATION_END, Events::ANIMATION_START, Events::ANIMATION_ITERATION, Events::ANIMATION_CANCEL,
    ];

    private const MEDIA_EVENTS = [
        Events::PLAY, Events::PAUSE, Events::ENDED, Events::TIME_UPDATE,
        Events::VOLUME_CHANGE, Events::SEEKING, Events::SEEKED,
        Events::LOADED_DATA, Events::LOADED_METADATA, Events::CAN_PLAY, Events::CAN_PLAY_THROUGH,
        Events::WAITING, Events::PLAYING, Events::RATE_CHANGE, Events::DURATION_CHANGE,
        Events::PROGRESS, Events::STALLED, Events::SUSPEND, Events::EMPTIED, Events::ABORT,
    ];

    /**
     * @var array<string, callable(mixed): mixed> Hydrators registered for
     *   custom event names by framework extensions (Leaflet wrapper, etc.).
     */
    private static array $custom = [];

    /**
     * Register a hydrator for a custom event name. Wrappers that
     * dispatch via `SPWA.dispatch(event, …)` call this so the server
     * knows how to turn the raw value array into a strongly typed
     * event object. Namespace the event name (`"leaflet:click"`) so
     * it can't collide with DOM events.
     *
     * @param callable(mixed): mixed $hydrator
     */
    public static function register(string $event, callable $hydrator): void
    {
        self::$custom[$event] = $hydrator;
    }

    /**
     * Hydrate raw event data into a strongly typed event object.
     */
    public static function hydrate(string $event, mixed $raw): mixed
    {
        if (isset(self::$custom[$event])) {
            return (self::$custom[$event])($raw);
        }

        if ($event === Events::UPLOAD) return FileEvent::from($raw);
        if (in_array($event, self::MOUSE_EVENTS)) return MouseEvent::from($raw);
        if (in_array($event, self::KEYBOARD_EVENTS)) return KeyboardEvent::from($raw);
        if (in_array($event, self::INPUT_EVENTS)) return InputEvent::from($raw);
        if (in_array($event, self::TOUCH_EVENTS)) return TouchEvent::from($raw);
        if (in_array($event, self::POINTER_EVENTS)) return PointerEvent::from($raw);
        if (in_array($event, self::DRAG_EVENTS)) return DragEvent::from($raw);
        if (in_array($event, self::CLIPBOARD_EVENTS)) return ClipboardEvent::from($raw);
        if ($event === Events::WHEEL) return WheelEvent::from($raw);
        if ($event === Events::SCROLL) return ScrollEvent::from($raw);
        if ($event === Events::RESIZE) return ResizeEvent::from($raw);
        if (in_array($event, self::TRANSITION_EVENTS)) return TransitionEvent::from($raw);
        if (in_array($event, self::ANIMATION_EVENTS)) return AnimationEvent::from($raw);
        if (in_array($event, self::MEDIA_EVENTS)) return MediaEvent::from($raw);
        if (in_array($event, self::FOCUS_EVENTS)) return null;

        // submit, reset, load, error, and unknown events — pass raw
        return $raw;
    }
}
