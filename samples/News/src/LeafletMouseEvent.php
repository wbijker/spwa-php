<?php

namespace Samples\News;

/**
 * Server-side counterpart of Leaflet's `MouseEvent`. Fired by the
 * Leaflet wrapper for `click`, `dblclick`, `mousedown`, `mouseup`,
 * `mouseover`, `mouseout`, `mousemove`, and `contextmenu` map events.
 *
 * Only carries the geographic coordinate (`latlng`); add more fields
 * (layerPoint / containerPoint / originalEvent) if a use case needs
 * them.
 */
class LeafletMouseEvent
{
    public function __construct(
        public readonly LeafletLatLng $latlng,
    ) {}

    /** @param array{latlng: array{lat: float, lng: float}} $raw */
    public static function from(array $raw): self
    {
        return new self(LeafletLatLng::from($raw['latlng']));
    }
}
