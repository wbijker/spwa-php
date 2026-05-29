<?php

namespace Spwa\UI;

use Spwa\Js\History;
use Spwa\State\StateManager;
use Spwa\VNode\App;
use Spwa\VNode\Component;
use Spwa\VNode\RenderPhase;
use Spwa\VNode\VNode;

/**
 * Static facade plus router Component.
 *
 *   Router::router()                         start a builder
 *       ->register(HomeRoute::class, fn(HomeRoute $r)        => new HomePage())
 *       ->register(ArticleRoute::class, fn(ArticleRoute $r)  => new Article($r->slug))
 *       ->fallback(new NotFoundPage())
 *
 *   Router::link(new ArticleRoute('hello'))        SPA-handled <a href>
 *   Router::link(new ExternalRoute(...), external: true)   browser-handled <a>
 *
 *   Router::navigate(new ArticleRoute('hello'))    call from any event handler
 *   Router::navigate('/some-path')                 string also accepted
 *
 * On render the Router walks its registered route classes in order, asks each
 * `handle($uri)` to claim the URL, and invokes the matching handler with the
 * parsed route instance.
 */
class Router extends Component
{
    private static ?string $navigateUri = null;
    private string $uri = '/';

    /** @var array<class-string<BaseRoute>, callable(BaseRoute): mixed> */
    private array $routes = [];
    private ?VNode $fallback = null;

    // ============================================================
    // Static facade
    // ============================================================

    /**
     * Start a router builder. Chain ->register(...) calls and (optionally)
     * ->fallback(...) on the returned instance, then embed it in your tree.
     */
    public static function router(): static
    {
        return new static();
    }

    /**
     * Build an anchor element for a route.
     *
     *   Router::link(new ArticleRoute('hi'))->content('Read more');
     *
     * With external=false (default) the click is intercepted client-side and
     * the navigation is routed through SPWA — no full-page reload. With
     * external=true the anchor is rendered as-is and the browser handles it.
     */
    public static function link(BaseRoute $route, bool $external = false): Link
    {
        return new Link($route, external: $external);
    }

    /**
     * Register the Router's client-side assets on the given App. Call from
     * your App::registerAssets() override:
     *
     *   protected function registerAssets(App $app): void {
     *       Router::registerAssets($app);
     *   }
     *
     * The emitted script wires the browser's History API to SPWA: any
     * back/forward navigation fires SPWA.refresh(), which POSTs an empty
     * event to the new URL. The server renders the route matched by the
     * new REQUEST_URI and diff-patches the page in place.
     *
     * Only popstate is hooked — pushState navigations are already issued
     * by the framework with patches in the same response, so they don't
     * need an extra refresh.
     */
    public static function registerAssets(App $app): void
    {
        $app->addScriptInline(<<<'JS'
(function () {
    if (window.__spwaRouterAttached) return;
    window.__spwaRouterAttached = true;

    // We manage scroll ourselves so the browser doesn't fight us on back/forward.
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    // Persist scroll positions across full page reloads via sessionStorage —
    // the in-memory map alone is wiped on refresh. Keyed per-URL so navigating
    // away and back (whether by SPA or by reload) lands at the same scroll.
    var STORAGE_KEY = '__spwaScrollPositions';
    var scrollPositions = {};
    try {
        var raw = sessionStorage.getItem(STORAGE_KEY);
        if (raw) scrollPositions = JSON.parse(raw) || {};
    } catch (e) { /* quota / disabled — start with an empty map */ }

    function persist() {
        try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(scrollPositions)); }
        catch (e) { /* ignore — best-effort */ }
    }

    var currentKey = location.pathname + location.search;

    function keyOf() { return location.pathname + location.search; }

    // Continuously snapshot scrollY for the active URL. By keeping this fresh,
    // popstate (which fires AFTER location has already changed) can still save
    // the correct position under the OLD key — currentKey hasn't moved yet.
    var pendingFrame = false;
    window.addEventListener('scroll', function () {
        if (pendingFrame) return;
        pendingFrame = true;
        requestAnimationFrame(function () {
            pendingFrame = false;
            scrollPositions[currentKey] = window.scrollY;
            persist();
        });
    }, { passive: true });

    // pagehide is the reliable cross-browser "user is leaving" hook (mobile
    // Safari notably skips beforeunload). Fires on reload, navigation away,
    // and tab close — a final guaranteed write before the page is gone.
    window.addEventListener('pagehide', function () {
        scrollPositions[currentKey] = window.scrollY;
        persist();
    });

    // Restore on a rAF loop — the new DOM may not be tall enough yet when
    // navigation fires (patches apply after pushState, asynchronously after
    // popstate, and the body may still be parsing on initial page load).
    // Bail when scrollY matches target or we've waited long enough.
    function restoreFor(key) {
        var target = scrollPositions[key] || 0;
        var attempts = 0;
        function step() {
            var maxScroll = Math.max(0,
                document.documentElement.scrollHeight - window.innerHeight);
            var clamped = Math.min(target, maxScroll);
            window.scrollTo(0, clamped);
            attempts++;
            if (attempts < 30 && window.scrollY < target && maxScroll < target) {
                requestAnimationFrame(step);
            }
        }
        requestAnimationFrame(step);
    }

    // Navigation triggered by Router::navigate() reaches the browser via
    // history.pushState in `data.js`. The new page's height isn't settled
    // until patches are applied and laid out, so instead of scrolling inside
    // the pushState override we stash the target key and restore in response
    // to spwa:patched, fired by spwa.js once patches are applied.
    var pendingRestoreKey = null;
    window.addEventListener('spwa:patched', function () {
        if (pendingRestoreKey !== null) {
            var key = pendingRestoreKey;
            pendingRestoreKey = null;
            restoreFor(key);
        }
    });

    // Initial restore — reload/direct hit on a URL we've seen before should
    // land back at the saved scroll position. The rAF loop tolerates the
    // body still being parsed.
    restoreFor(currentKey);

    // pushState changes location synchronously, but the runtime applies DOM
    // patches BEFORE running this (it's emitted in data.js and executed after
    // applyPatches). By now the new — often shorter — page is already on
    // screen and the browser has clamped window.scrollY, so reading it here
    // would save a corrupted position for the page we're leaving. The
    // outgoing scroll was already captured by the rAF snapshot above while the
    // old DOM was still on screen, so here we only roll currentKey forward and
    // queue the restore for the new URL.
    var origPush = history.pushState;
    history.pushState = function () {
        var ret = origPush.apply(this, arguments);
        var newKey = keyOf();
        if (newKey !== currentKey) {
            currentKey = newKey;
            pendingRestoreKey = newKey;
        }
        return ret;
    };

    // replaceState shouldn't be treated as navigation — just keep currentKey
    // in sync so subsequent scroll events save under the right URL.
    var origReplace = history.replaceState;
    history.replaceState = function () {
        var ret = origReplace.apply(this, arguments);
        currentKey = keyOf();
        return ret;
    };

    window.addEventListener('popstate', function () {
        // location has already changed; currentKey is still the OLD URL.
        scrollPositions[currentKey] = window.scrollY;
        persist();
        currentKey = keyOf();
        if (window.SPWA && typeof SPWA.refresh === 'function') {
            // SPWA.refresh POSTs and the new DOM arrives asynchronously —
            // defer the restore so it lands after patches are applied.
            pendingRestoreKey = currentKey;
            SPWA.refresh();
        } else {
            // No runtime to refresh against — restore immediately against
            // whatever DOM is already on the page.
            restoreFor(currentKey);
        }
    });
})();
JS);
    }

    /**
     * Push a URL into history and queue a re-render of the active Router. May
     * be called with a BaseRoute (typed, preferred) or a raw URL string.
     */
    public static function navigate(BaseRoute|string $target): void
    {
        $url = $target instanceof BaseRoute ? $target->toUrl() : $target;
        self::$navigateUri = $url;
        History::pushState(null, '', $url);
    }

    // ============================================================
    // Builder
    // ============================================================

    /**
     * Register a route class + handler. The handler receives a parsed
     * instance of the route class when its `handle()` claims the URL.
     *
     * @template T of BaseRoute
     * @param class-string<T> $routeClass
     * @param callable(T): mixed $handler  Returns a VNode/UIElement/string.
     */
    public function register(string $routeClass, callable $handler): static
    {
        $this->routes[$routeClass] = $handler;
        return $this;
    }

    /**
     * Optional fallback when no registered route claims the URI.
     */
    public function fallback(VNode|string $content): static
    {
        $this->fallback = is_string($content) ? UI::text($content) : $content;
        return $this;
    }

    // ============================================================
    // Component lifecycle
    // ============================================================

    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        // Read REQUEST_URI on every render (POSTs go to window.location.href,
        // so this is the source of truth for "where the browser is"). An
        // in-flight navigation from a click handler overrides it via
        // restored() below.
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $this->uri = parse_url($path, PHP_URL_PATH) ?? '/';

        return parent::render($state, $parent, $phase);
    }

    protected function restored(): void
    {
        if (self::$navigateUri !== null) {
            $this->uri = self::$navigateUri;
            self::$navigateUri = null;
        }
    }

    protected function build(): VNode
    {
        foreach ($this->routes as $routeClass => $handler) {
            $route = $routeClass::handle($this->uri);
            if ($route !== null) {
                return self::normalize($handler($route));
            }
        }

        return $this->fallback ?? UI::text('404 — Not Found');
    }

    private static function normalize(mixed $value): VNode
    {
        if ($value instanceof VNode) {
            return $value;
        }
        return UI::text((string)$value);
    }
}
