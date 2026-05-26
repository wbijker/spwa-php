<?php

namespace Samples\News;

use Spwa\Js\Js;
use Spwa\Js\JsLiteral;
use Spwa\UI\Color;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\App;
use Spwa\VNode\StatelessComponent;
use Spwa\VNode\VNode;

/**
 * A Leaflet map. Emits a sized <div id="$key">; the L.map instance is
 * created the first time this component renders (BaseComponent's
 * `created` hook) and cached at window.leafLet[$key] so subsequent
 * setView() calls can drive it directly.
 *
 *   $this->leaflet = new Leaflet('story-map');
 *   $this->leaflet->setView([51.505, -0.09], 13);
 */
class Leaflet extends StatelessComponent
{
    /**
     * @param array{0: float, 1: float}|null $initialCoords Initial map
     *   center. Applied once inside the created() hook, so subsequent
     *   setView() calls (from event handlers, etc.) take over without
     *   the initial view re-asserting itself on every render.
     */
    public function __construct(
        private string $key,
        private ?array $initialCoords = null,
        private int $initialZoom = 14,
    ) {}

    /**
     * First-time-only setup for this map. Wrapped in SPWA.ready so the
     * L.map(<key>) call sees the rendered <div id="<key>"> — on initial
     * GET the inline head-script runs before <body> parses, so without
     * the ready gate L.map() would error.
     *
     *   SPWA.ready(function () {
     *       window.leafLet["<key>"] = L.map("<key>");
     *       L.tileLayer(url, opts).addTo(window.leafLet["<key>"]);
     *   });
     */
    protected function created(): void
    {
        $ref = $this->mapRef();

        $statements = [
            // window.leafLet[<key>] = L.map(<key>)
            Js::assign([$ref], Js::invoke(['L', 'map'], [$this->key])),

            // L.tileLayer(url, opts).addTo(window.leafLet[<key>])
            Js::invoke(
                [Js::invoke(['L', 'tileLayer'], [
                    'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    [
                        'maxZoom'     => 19,
                        'attribution' => '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    ],
                ]), 'addTo'],
                [$ref],
            ),
        ];

        // Optional initial view — fires once on first appearance.
        // Subsequent renders skip created() so the view is not reset
        // and any post-click setView stays in effect.
        if ($this->initialCoords !== null) {
            $statements[] = Js::invoke(
                [$ref, 'setView'],
                [$this->initialCoords, $this->initialZoom],
            );
        }

        Js::domReady(...$statements);
    }

    /**
     * Drive this map's view immediately by queueing a Js::invoke into
     * the shared SPWA.ready block. Intended to be called from event
     * handlers (e.g. a "click to pan to this landmark") rather than
     * from render-time code: events fire once per user action so
     * there's no duplication risk, and the resulting setView statement
     * lands in the response that follows that one event.
     *
     *   SPWA.ready(function () {
     *       window.leafLet["<key>"].setView([c1, c2], zoom);
     *   });
     *
     * @param array{0: float, 1: float} $coordinates
     */
    public function setView(array $coordinates, int $zoom): void
    {
        Js::domReady(
            Js::invoke([$this->mapRef(), 'setView'], [$coordinates, $zoom]),
        );
    }

    /** Bracket-form reference to window.leafLet[<key>] — safe for any string key. */
    private function mapRef(): JsLiteral
    {
        return new JsLiteral('window.leafLet[' . json_encode($this->key) . ']');
    }

    protected function build(): VNode
    {
        return UI::div()
            ->width(Unit::full())
            ->height(Unit::px(320))
            ->background(Color::gray(100))
            ->attr('id', $this->key);
    }

    /**
     * Pulls Leaflet's CSS + JS from the unpkg CDN and seeds
     * `window.leafLet` as an empty object — every Leaflet instance
     * stores its live L.map at `window.leafLet[<key>]`, so the
     * container needs to exist before any created() statements run.
     * Emitted once per page from <head>, so a POST event response
     * never resets it and destroys existing maps.
     */
    public static function registerAssets(App $app): void
    {
        $app->addStyle('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        $app->addScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
        $app->addScriptInline('window.leafLet = {};');
    }
}
