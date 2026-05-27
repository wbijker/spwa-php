<?php

namespace Samples\News;

use Spwa\Js\Js;
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
     */
    protected function created(): void
    {
        $ref = $this->mapRef();

        // window.leafLet[<key>] = L.map("<key>")
        $createMap = Js::assign($ref, Js::invoke(Js::obj('L', 'map'), Js::str($this->key)));

        // L.tileLayer(url, opts).addTo(window.leafLet[<key>])
        $tileLayer = Js::invoke(
            Js::obj(
                Js::invoke(
                    Js::obj('L', 'tileLayer'),
                    Js::str('https://tile.openstreetmap.org/{z}/{x}/{y}.png'),
                    [
                        'maxZoom'     => 19,
                        'attribution' => '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    ],
                ),
                'addTo',
            ),
            $ref,
        );

        $body = "$createMap;$tileLayer";

        // Optional one-time setView — fires only inside created() so it
        // doesn't reassert on every subsequent render.
        if ($this->initialCoords !== null) {
            $body .= ';' . Js::invoke(
                Js::obj($ref, 'setView'),
                $this->initialCoords,
                $this->initialZoom,
            );
        }

        Js::run("SPWA.ready(function(){{$body}})");
    }

    /**
     * Drive this map's view immediately. Wrapped in SPWA.ready so calls
     * issued from inline head scripts on the initial GET still wait
     * for the map (created inside its own ready callback).
     *
     * @param array{0: float, 1: float} $coordinates
     */
    public function setView(array $coordinates, int $zoom): void
    {
        $call = Js::invoke(Js::obj($this->mapRef(), 'setView'), $coordinates, $zoom);
        Js::run("SPWA.ready(function(){{$call}})");
    }

    /** Bracket-form `window.leafLet[<key>]` reference — safe for any string key. */
    private function mapRef(): string
    {
        return Js::obj('window', 'leafLet') . Js::index($this->key);
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
     */
    public static function registerAssets(App $app): void
    {
        $app->addStyle('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        $app->addScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
        $app->addScriptInline('window.leafLet = {};');
    }
}
