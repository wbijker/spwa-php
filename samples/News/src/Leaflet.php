<?php

namespace Samples\News;

use Spwa\Js\Js;
use Spwa\Js\JsLiteral;
use Spwa\UI\Color;
use Spwa\UI\DomNode;
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
    /** @var array{coordinates: array{0: float, 1: float}, zoom: int}|null */
    private ?array $pendingView = null;

    public function __construct(private string $key) {}

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

        Js::domReady(
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
        );
    }

    /**
     * Record the desired view. We don't emit Js here — calling setView
     * eagerly (e.g. from a body() that's evaluated twice because a
     * router stores both a route closure and a fallback) would queue
     * duplicate or order-wrong statements. Instead the actual
     * Js::invoke happens in rendered(), which fires only for the
     * Leaflet that's truly in the rendered tree AND queues after
     * created() so the map is constructed first.
     *
     * @param array{0: float, 1: float} $coordinates
     */
    public function setView(array $coordinates, int $zoom): void
    {
        $this->pendingView = ['coordinates' => $coordinates, 'zoom' => $zoom];
    }

    /**
     * Flush any pending setView intent. Runs after build() and after
     * created()'s queued statements, so the resulting JS lands inside
     * the shared SPWA.ready block in the correct order:
     *
     *   SPWA.ready(function () {
     *       window.leafLet["<key>"] = L.map("<key>");         // from created
     *       L.tileLayer(...).addTo(window.leafLet["<key>"]);  // from created
     *       window.leafLet["<key>"].setView([c1, c2], zoom);  // from here
     *   });
     */
    protected function rendered(DomNode $dom): void
    {
        if ($this->pendingView === null) {
            return;
        }

        Js::domReady(
            Js::invoke(
                [$this->mapRef(), 'setView'],
                [$this->pendingView['coordinates'], $this->pendingView['zoom']],
            ),
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
