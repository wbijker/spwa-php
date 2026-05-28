<?php

namespace Spwa\Events;

/**
 * Lifecycle hook for an event whose client-side listener is wired
 * imperatively in JS rather than via an inline `on<event>` attribute. Every
 * event carries one — native DOM events use NativeEventRegistration, custom
 * events (e.g. Leaflet's map click) supply their own.
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
 * $path is the target node's path in the rendered tree; $event is the
 * logical event name. Implementations typically queue JS via Js::run/ready.
 */
interface EventRegistration
{
    /** @param int[] $path */
    public function add(array $path, string $event): void;

    /** @param int[] $path */
    public function remove(array $path, string $event): void;
}
