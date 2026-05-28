<?php

namespace Spwa\Events;

/**
 * Lifecycle hook for an event whose client-side listener is wired
 * imperatively in JS rather than via an inline `on<event>` attribute. Every
 * event carries one — native DOM events use NativeEventRegistration, custom
 * events (e.g. Leaflet's map click) supply their own.
 *
 * A registration owns the event name it dispatches (eventName()) and knows
 * how to wire/unwire its own client listener — callers never thread the
 * name or the attachment through; they just hand over the registration.
 *
 * An event is only ever added or removed — there is no separate "node
 * deleted" case (a listener leaving with its node is just a removal) and no
 * update (a re-render with the listener still in place emits nothing; the
 * binding from add() persists and the server resolves the current callback
 * by event name when it fires). So the diff calls exactly one of:
 *
 *   - add()    — the listener was materialised: initial render, insert,
 *                replace, or newly attached to a surviving node.
 *   - remove() — the listener went away: handler dropped from a surviving
 *                node, or the node itself left the tree.
 *
 * $path is the target node's path in the rendered tree. Implementations
 * typically queue JS via Js::run/ready.
 */
interface EventRegistration
{
    /**
     * Logical event name this registration dispatches — the key the server
     * uses to find the handler, and the name baked into the emitted JS.
     */
    public function eventName(): string;

    /** @param int[] $path */
    public function add(array $path): void;

    /** @param int[] $path */
    public function remove(array $path): void;
}
