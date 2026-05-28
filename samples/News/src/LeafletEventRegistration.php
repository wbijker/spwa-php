<?php

namespace Samples\News;

use Spwa\Events\EventRegistration;
use Spwa\Js\Js;

/**
 * Drives one Leaflet `map.on(...)` / `map.off(...)` listener through the
 * diff. add() attaches the listener when the map node is materialised;
 * remove() detaches it when the handler is dropped or the map node leaves
 * the tree. A re-render that keeps the handler emits nothing and the
 * listener persists on the cached map (`window.leafLet[<key>]`). The diff
 * path is unused here because the map is addressed by its cached key, not
 * its DOM path.
 */
final class LeafletEventRegistration implements EventRegistration
{
    /**
     * @param string $event   Server event this dispatches, e.g. 'leaflet:click'
     * @param string $mapRef  JS reference to the cached map, e.g. window.leafLet["story-map"]
     * @param string $onStmt  `map.on('<event>', function(event){…})` expression
     * @param string $offStmt `map.off('<event>')` expression
     */
    public function __construct(
        private string $event,
        private string $mapRef,
        private string $onStmt,
        private string $offStmt,
    ) {
    }

    public function eventName(): string
    {
        return $this->event;
    }

    public function add(array $path): void
    {
        $this->ready($this->onStmt);
    }

    public function remove(array $path): void
    {
        $this->ready($this->offStmt);
    }

    /** Run a statement against the cached map inside SPWA.ready. */
    private function ready(string $stmt): void
    {
        Js::ready(
            Js::assign('var map ', $this->mapRef),
            $stmt,
        );
    }
}
