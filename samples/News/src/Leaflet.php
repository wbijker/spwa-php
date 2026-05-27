<?php

namespace Samples\News;

use Spwa\Events\EventData;
use Spwa\Js\Js;
use Spwa\UI\Color;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\App;
use Spwa\VNode\StatelessComponent;
use Spwa\VNode\VNode;

/**
 * Leaflet map wrapper. Mirrors the most common one-way operations on
 * Leaflet's `L.map` (`setView`, `panTo`, `setZoom`, `zoomIn/Out`,
 * `flyTo`, `addMarker/Circle/Polygon/Popup`, `clearLayers`) and one
 * server-side event (`onClick`).
 *
 *   $this->leaflet = new Leaflet('story-map', [51.505, -0.09], 13);
 *   $this->leaflet->setView([48.8584, 2.2945], 14);
 *   $this->leaflet->onClick(fn(LeafletMouseEvent $e) =>
 *       Console::log('Map click at', $e->latlng->lat, $e->latlng->lng));
 *
 * Each instance is cached in JS at `window.leafLet[<key>]`. The L.map
 * itself is constructed once inside the `created()` ready-callback;
 * subsequent renders only emit method calls against the cached map.
 */
class Leaflet extends StatelessComponent
{
    /** @var array<string, callable> Server-side handlers keyed by `leaflet:<event>` name */
    private array $handlers = [];

    /**
     * Decorative `addMarker` / `addCircle` / `addPolygon` / `addPopup`
     * calls buffer their rendered JS-expression strings here. The
     * setup `SPWA.ready` block in created() drains the buffer so the
     * additions land on the client AFTER the map exists, exactly once
     * per map (subsequent renders re-fill the buffer but created()
     * doesn't fire again, so nothing duplicates).
     *
     * @var string[]
     */
    private array $staged = [];

    /**
     * @param array{0: float, 1: float}|null $initialCoords Initial view
     *   center — applied once in `created()`. Subsequent renders skip
     *   the setView so post-click state isn't reset.
     */
    public function __construct(
        private string $key,
        private ?array $initialCoords = null,
        private int $initialZoom = 14,
    ) {}

    // ============================================================
    // Server-side events (forwarded via SPWA.dispatch)
    // ============================================================

    /**
     * Fires when the user clicks the map. The callback receives a
     * `LeafletMouseEvent` carrying the geographic coordinate that was
     * clicked (forwarded from Leaflet's own click event).
     *
     * @param callable(LeafletMouseEvent): void $callback
     */
    public function onClick(callable $callback): self
    {
        $this->handlers['leaflet:click'] = $callback;
        return $this;
    }

    // ============================================================
    // One-way map operations (PHP → JS)
    // ============================================================

    /** @param array{0: float, 1: float} $coordinates */
    public function setView(array $coordinates, int $zoom): void
    {
        $this->emit(Js::invoke(Js::obj($this->mapRef(), 'setView'), $coordinates, $zoom));
    }

    /** @param array{0: float, 1: float} $coordinates */
    public function panTo(array $coordinates): void
    {
        $this->emit(Js::invoke(Js::obj($this->mapRef(), 'panTo'), $coordinates));
    }

    public function setZoom(int $zoom): void
    {
        $this->emit(Js::invoke(Js::obj($this->mapRef(), 'setZoom'), $zoom));
    }

    public function zoomIn(int $delta = 1): void
    {
        $this->emit(Js::invoke(Js::obj($this->mapRef(), 'zoomIn'), $delta));
    }

    public function zoomOut(int $delta = 1): void
    {
        $this->emit(Js::invoke(Js::obj($this->mapRef(), 'zoomOut'), $delta));
    }

    /** @param array{0: float, 1: float} $coordinates */
    public function flyTo(array $coordinates, ?int $zoom = null): void
    {
        $args = $zoom === null ? [$coordinates] : [$coordinates, $zoom];
        $this->emit(Js::invoke(Js::obj($this->mapRef(), 'flyTo'), ...$args));
    }

    /** Remove every Layer from the map. */
    public function clearLayers(): void
    {
        // map.eachLayer(function(l) { map.removeLayer(l) })
        $ref = $this->mapRef();
        Js::ready("$ref.eachLayer(function(l){{$ref}.removeLayer(l)})");
    }

    /**
     * Drop a marker — `L.marker([lat,lng]).addTo(map).bindPopup(html)`.
     * Popup is bound (revealed on click) but not auto-opened. Empty
     * `$html` skips the bind. Staged; emitted once inside created().
     */
    public function addMarker(float $lat, float $lng, string $html = ''): void
    {
        $marker  = Js::invoke(Js::obj('L', 'marker'), [$lat, $lng]);
        $withMap = Js::invoke(Js::obj($marker, 'addTo'), $this->mapRef());
        $this->staged[] = $html === ''
            ? $withMap
            : Js::invoke(Js::obj($withMap, 'bindPopup'), Js::str($html));
    }

    /** `L.circle([lat,lng], opts).addTo(map)`. Staged for created(). */
    public function addCircle(float $lat, float $lng, Circle $circle): void
    {
        $expr           = Js::invoke(Js::obj('L', 'circle'), [$lat, $lng], $circle->toArray());
        $this->staged[] = Js::invoke(Js::obj($expr, 'addTo'), $this->mapRef());
    }

    /**
     * `L.polygon([[lat,lng], …]).addTo(map)`. Staged for created().
     * @param array<array{0: float, 1: float}> $coords
     */
    public function addPolygon(array $coords): void
    {
        $poly           = Js::invoke(Js::obj('L', 'polygon'), $coords);
        $this->staged[] = Js::invoke(Js::obj($poly, 'addTo'), $this->mapRef());
    }

    /**
     * `L.popup().setLatLng([lat,lng]).setContent(content).openOn(map)`.
     * Staged for created().
     */
    public function addPopup(float $lat, float $lng, string $content): void
    {
        $popup       = Js::invoke(Js::obj('L', 'popup'));
        $withLatLng  = Js::invoke(Js::obj($popup, 'setLatLng'), [$lat, $lng]);
        $withContent = Js::invoke(Js::obj($withLatLng, 'setContent'), Js::str($content));
        $this->staged[] = Js::invoke(Js::obj($withContent, 'openOn'), $this->mapRef());
    }

    // ============================================================
    // Lifecycle + DOM
    // ============================================================

    /**
     * First-render setup inside a single `SPWA.ready` block:
     *
     *   var d = document.getElementById("<key>");
     *   window.leafLet["<key>"] = L.map("<key>");
     *   L.tileLayer(…).addTo(window.leafLet["<key>"]);
     *   (optional) window.leafLet["<key>"].setView([lat,lng], zoom);
     *   window.leafLet["<key>"].on('click', e => SPWA.dispatch('leaflet:click', d, {…}));
     */
    protected function created(): void
    {
        $ref = $this->mapRef();
        $divVar = '__d';

        $lines = [
            // var __d = document.getElementById("<key>");
            "var $divVar=" . Js::invoke(Js::obj('document', 'getElementById'), Js::str($this->key)),

            // window.leafLet[<key>] = L.map("<key>");
            Js::assign($ref, Js::invoke(Js::obj('L', 'map'), Js::str($this->key))),

            // L.tileLayer(url, opts).addTo(map);
            Js::invoke(
                Js::obj(
                    Js::invoke(Js::obj('L', 'tileLayer'),
                        Js::str('https://tile.openstreetmap.org/{z}/{x}/{y}.png'),
                        [
                            'maxZoom'     => 19,
                            'attribution' => '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        ],
                    ),
                    'addTo',
                ),
                $ref,
            ),
        ];

        if ($this->initialCoords !== null) {
            $lines[] = Js::invoke(Js::obj($ref, 'setView'), $this->initialCoords, $this->initialZoom);
        }

        // Wire every registered server-side handler.
        // map.on('click', function(e) {
        //     SPWA.dispatch('leaflet:click', __d, { latlng: { lat: e.latlng.lat, lng: e.latlng.lng } });
        // });
        foreach (array_keys($this->handlers) as $serverEvent) {
            $leafletEvent = substr($serverEvent, strlen('leaflet:')); // strip namespace
            $lines[] = "{$ref}.on(" . Js::str($leafletEvent)
                . ",function(e){SPWA.dispatch(" . Js::str($serverEvent) . ","
                . "$divVar,{latlng:{lat:e.latlng.lat,lng:e.latlng.lng}})})";
        }

        // Drain staged additions (markers, circles, polygons, popups)
        // into the same SPWA.ready block, after setup and event wiring.
        foreach ($this->staged as $stmt) {
            $lines[] = $stmt;
        }

        Js::ready(...$lines);
    }

    /** Bracket-form `window.leafLet[<key>]` reference — safe for any string key. */
    private function mapRef(): string
    {
        return Js::obj('window', 'leafLet') . Js::index($this->key);
    }

    /** Emit a single map operation wrapped in SPWA.ready. */
    private function emit(string $stmt): void
    {
        Js::ready($stmt);
    }

    protected function build(): VNode
    {
        $div = UI::div()
            ->width(Unit::full())
            ->height(Unit::px(320))
            ->background(Color::gray(100))
            ->attr('id', $this->key);

        // Attach each registered handler to the underlying div under
        // its `leaflet:<name>` event key — SPWA.dispatch on the client
        // posts events with that name to this div's path, and the
        // framework's executeEvent dispatches to these callbacks.
        foreach ($this->handlers as $event => $cb) {
            $div->dom()->on($event, $cb);
        }

        return $div;
    }

    /**
     * Pulls Leaflet's CSS + JS from the unpkg CDN, seeds
     * `window.leafLet`, and registers event-data hydrators for the
     * Leaflet-namespaced events so the server can turn the raw value
     * arrays into strongly typed event objects when handlers fire.
     */
    public static function registerAssets(App $app): void
    {
        $app->addStyle('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        $app->addScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
        $app->addScriptInline('window.leafLet = {};');
        EventData::register('leaflet:click', LeafletMouseEvent::from(...));
    }
}
