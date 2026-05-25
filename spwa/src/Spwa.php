<?php

namespace Spwa;

use Spwa\Debug\DebugPanel;
use Spwa\Debug\Timings;
use Spwa\Error\DefaultErrorPage;
use Spwa\Error\ErrorInfo;
use Spwa\Js\JsRuntime;
use Spwa\State\InMemoryStateManager;
use Spwa\State\StateManager;
use Spwa\UI\StyleGenerator;
use Spwa\UI\TagDomNode;
use Spwa\VNode\App;
use Spwa\VNode\Component;
use Spwa\VNode\Patcher;
use Spwa\VNode\PortalTarget;
use Spwa\VNode\RenderPhase;
use Spwa\VNode\VNode;
use Throwable;

class Spwa
{
    /** @var App|null The current app instance, kept so the shutdown handler can call its error() method. */
    private static ?App $current = null;

    /** @var bool Set once an error has been rendered so the shutdown handler doesn't render twice. */
    private static bool $errorRendered = false;

    /**
     * Entry point. Accepts either an instantiated App or its class
     * name. Passing the class name is preferred: it lets Spwa install
     * its error traps BEFORE the App is constructed, so a parse error
     * or constructor crash in the App's own file is caught and shown
     * as an error page instead of leaking xdebug's HTML.
     *
     * @param App|class-string<App> $entry
     */
    public static function run(App|string $entry): void
    {
        self::installErrorTraps();

        try {
            if (is_string($entry)) {
                $entry = new $entry();
            }
            self::$current = $entry;

            // Pull the state manager ONCE; do not call state() again.
            // Many managers are stateful within a request (in-process
            // caches), so two instances would have divergent caches and
            // the request would see inconsistent data.
            $state = $entry->state();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                self::handlePost($entry, $state);
            } else {
                self::handleGet($entry, $state);
            }
        } catch (Throwable $e) {
            self::renderError(ErrorInfo::fromThrowable($e));
        }
    }

    /**
     * Install error/exception/shutdown traps so every flavor of PHP
     * error lands in {@see renderError} instead of xdebug's default
     * HTML or a half-rendered page. Also starts an output buffer so
     * any error markup written by the SAPI (e.g. xdebug) can be
     * discarded before we emit our own.
     */
    private static function installErrorTraps(): void
    {
        // Don't let PHP/xdebug emit its own HTML. We render everything.
        @ini_set('display_errors', '0');
        @ini_set('html_errors', '0');

        // Output buffer so xdebug's pretty error HTML (and any partial
        // app output written before the crash) can be discarded.
        ob_start();

        // Fatal-class errors thrown at runtime become exceptions so
        // the normal catch path in run() / handleGet() / handlePost()
        // picks them up. Non-fatal errors are returned to PHP's
        // default handler so they don't break otherwise-fine pages.
        set_error_handler(function (int $errno, string $msg, string $file, int $line) {
            if ((error_reporting() & $errno) === 0) {
                return false;
            }
            if (in_array($errno, [E_USER_ERROR, E_RECOVERABLE_ERROR], true)) {
                throw new \ErrorException($msg, 0, $errno, $file, $line);
            }
            return false;
        });

        // Backstop for anything that escapes our try/catch.
        set_exception_handler(function (Throwable $e) {
            self::renderError(ErrorInfo::fromThrowable($e));
        });

        // Parse errors, out-of-memory, and other unrecoverables that
        // never reach an exception handler still surface here.
        register_shutdown_function(function () {
            if (self::$errorRendered) {
                return;
            }
            $err = error_get_last();
            if ($err !== null && ErrorInfo::isFatal($err['type'])) {
                self::renderError(ErrorInfo::fromLastError($err));
            }
        });
    }

    /**
     * Render an error page (HTML for GET, JSON reload for POST). Safe
     * to call from any point in the request — clears any buffered
     * partial output first so xdebug's HTML doesn't bleed through.
     */
    private static function renderError(ErrorInfo $info): void
    {
        if (self::$errorRendered) {
            return;
        }
        self::$errorRendered = true;

        // Drop any buffered output (including xdebug's error HTML).
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

        // For a POST, the frontend expects JSON. Asking it to reload
        // bounces us back to GET, which re-renders the error page
        // through the HTML path below.
        if ($isPost) {
            if (!headers_sent()) {
                header('Content-Type: application/json');
                http_response_code(500);
            }
            echo json_encode(['success' => false, 'reload' => true]);
            return;
        }

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
            http_response_code(500);
        }

        // Try the app's error() method; fall back to DefaultErrorPage
        // directly if no app exists yet (e.g. parse error during
        // construction) or if error() itself throws.
        $errorView = null;
        if (self::$current !== null) {
            try {
                $errorView = self::$current->error($info);
            } catch (Throwable) {
                $errorView = null;
            }
        }
        if (!$errorView instanceof VNode) {
            $errorView = new DefaultErrorPage($info);
        }

        try {
            $state = new InMemoryStateManager();
            $dom = $errorView->render($state, null, RenderPhase::Initial);
            $styles = $dom->collectStyles();
            $css = StyleGenerator::from($styles)->toStyle();

            $head = (new TagDomNode('head'))->content(
                (new TagDomNode('meta'))->attr('charset', 'UTF-8'),
                (new TagDomNode('meta'))->attr('name', 'viewport')->attr('content', 'width=device-width, initial-scale=1.0'),
                (new TagDomNode('title'))->rawContent('Error'),
                (new TagDomNode('style'))->rawContent($css),
            );
            $body = (new TagDomNode('body'))
                ->attr('style', 'margin:0')
                ->content($dom);
            if (self::isDevelopment()) {
                $body->content((new TagDomNode('script'))->rawContent(self::hmrScript()));
            }
            $document = (new TagDomNode('html'))
                ->attr('lang', 'en')
                ->content($head, $body);

            echo '<!DOCTYPE html>' . $document->toHtml();
        } catch (Throwable) {
            // Absolute last resort: build the default page directly
            // and emit it without going through render/collectStyles.
            $bare = new DefaultErrorPage($info);
            $dom = $bare->render(new InMemoryStateManager(), null, RenderPhase::Initial);
            $hmr = self::isDevelopment() ? '<script>' . self::hmrScript() . '</script>' : '';
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title></head><body style="margin:0">'
               . $dom->toHtml()
               . $hmr
               . '</body></html>';
        }
    }

    /**
     * Load the project config (returns []) if no config.php exists. Looks
     * next to the entry script (e.g. www/config.php sits beside index.php).
     * Cached for the request.
     *
     * @return array<string, mixed>
     */
    private static function config(): array
    {
        static $config = null;
        if ($config === null) {
            $path = dirname($_SERVER['SCRIPT_FILENAME'] ?? '') . '/config.php';
            $config = is_file($path) ? (array)require $path : [];
        }
        return $config;
    }

    /**
     * True when config.php has `development => true`. Gates the HMR
     * long-poll (both the IIFE in spwa.js, via window.__SPWA_DEV, and
     * the inlined script in error pages).
     */
    private static function isDevelopment(): bool
    {
        return (bool)(self::config()['development'] ?? false);
    }

    /**
     * Inline HMR polling — same semantics as the IIFE at the bottom of
     * spwa.js, but emitted directly into the error page so a fix to
     * the offending file triggers a reload without the user touching
     * the browser. Inlined (rather than `<script src="/spwa.js">`)
     * because spwa.js's bootstrap() runs maybeReplay() which would
     * re-fire the failed POST against the still-broken backend.
     */
    private static function hmrScript(): string
    {
        return <<<'JS'
(function () {
    var ctl;
    function poll() {
        if (ctl) ctl.abort();
        ctl = new AbortController();
        fetch('/hmr.php', { signal: ctl.signal, cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (j) { if (j && j.changed) location.reload(); })
            .catch(function () {});
    }
    window.addEventListener('beforeunload', function () { if (ctl) ctl.abort(); });
    poll();
    setInterval(poll, 60000);
})();
JS;
    }

    /**
     * Fingerprint the stored state. Used as a cheap optimistic-
     * concurrency token: the frontend echoes back the hash from page
     * render, the backend re-hashes the stored state before processing
     * an event, and a mismatch forces a reload.
     */
    private static function computeStateHash(StateManager $state): string
    {
        return sha1(serialize($state->getAll()));
    }

    /**
     * Cheap source fingerprint: newest mtime + file count under the project
     * root, joined with a colon. Doubles as HMR change signal and /style.css
     * cache-buster. Any edit bumps mtime; any add/remove bumps count. Walks
     * .php (extracted into the stylesheet) and .css (preflight prepended to
     * it) so either flavour of edit invalidates the cached sheet. Skips
     * vendor/node_modules/.git.
     */
    public static function sourceHash(string $root): string
    {
        $skip = ['vendor' => 1, 'node_modules' => 1, '.git' => 1];
        $dir = new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS);
        $filter = new \RecursiveCallbackFilterIterator($dir, function ($f) use ($skip) {
            if ($f->isFile()) {
                $ext = $f->getExtension();
                return $ext === 'php' || $ext === 'css';
            }
            return !isset($skip[$f->getFilename()]);
        });
        $max = 0;
        $n = 0;
        foreach (new \RecursiveIteratorIterator($filter) as $f) {
            $m = $f->getMTime();
            if ($m > $max) $max = $m;
            $n++;
        }
        return $max . ':' . $n;
    }

    /**
     * Project root (parent of www/) inferred from the entry script. Used to
     * scope sourceHash() and config().
     */
    private static function projectRoot(): string
    {
        return dirname($_SERVER['SCRIPT_FILENAME'] ?? '', 2);
    }

    private static function handlePost(App $entry, StateManager $state): void
    {
        $t = new Timings();
        ob_start();

        // Parse payload from JSON or multipart form
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'multipart/form-data')) {
            $payload = json_decode($_POST['_spwa'] ?? '{}', true);
        } else {
            $payload = json_decode(file_get_contents('php://input'), true);
        }

        $event = $payload['event'] ?? '';
        $pathStr = $payload['path'] ?? '';
        $path = array_map('intval', explode(',', $pathStr));
        $value = $payload['value'] ?? null;
        $bindings = $payload['bindings'] ?? [];
        $expectedHash = $payload['hash'] ?? null;
        // The path the frontend's DOM was rendered for. Differs from
        // REQUEST_URI after popstate (browser changed URL, server hasn't
        // re-rendered yet). Used to override REQUEST_URI for the OLD render
        // only, so the diff against the NEW render at the current URL
        // produces the correct list↔detail swap.
        $previousPath = $payload['previousPath'] ?? null;
        $t->mark('parse_payload');

        // Optimistic concurrency: the frontend echoes the hash it was
        // rendered against. If the backend's current state hashes differently
        // (e.g. another tab mutated it, or a deploy reshaped it), the frontend
        // is operating on a stale tree — bail out and force a reload.
        if ($expectedHash !== null && $expectedHash !== self::computeStateHash($state)) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'reload' => true]);
            exit;
        }
        $t->mark('verify_hash');

        // Render the old tree, execute event, save state. If the render or
        // event handler crashes because serialized state no longer matches
        // the current code's shape, clear all state and tell the client to
        // reload — the fresh page will start from defaults.
        try {
            PortalTarget::reset();
            $oldApp = new ($entry::class)();
            // OLD must mirror what's actually on the user's screen — the path
            // the frontend last rendered for. After popstate the browser URL
            // (REQUEST_URI) has already moved on; without this override OLD
            // would render the NEW route too, diff would be empty, and the
            // stale DOM would never be patched.
            $origRequestUri = $_SERVER['REQUEST_URI'] ?? null;
            if ($previousPath !== null && $previousPath !== '' && $previousPath !== $origRequestUri) {
                $_SERVER['REQUEST_URI'] = $previousPath;
            }
            $oldUi = $oldApp->render($state, null, RenderPhase::DiffOld);
            if ($origRequestUri !== null) {
                $_SERVER['REQUEST_URI'] = $origRequestUri;
            }
            $t->mark('render_old');

            if (!empty($bindings) && $oldUi instanceof TagDomNode) {
                $oldUi->hydrateBindings($bindings);
            }
            $t->mark('hydrate_bindings');

            $node = $oldUi->findByPath($path);
            if ($node !== null) {
                $node->executeEvent($event, $state, $value);
            }
            $t->mark('execute_event');

            // Finalize every old-tree component, not just the root. An event
            // handler can mutate state on any ancestor component via captured
            // `$this`; without this sweep those mutations never persist.
            Component::finalizeAll($state);
            $t->mark('finalize_old');

            PortalTarget::reset();
            $newApp = new ($entry::class)();
            $newUi = $newApp->render($state, null, RenderPhase::Patch);
            $t->mark('render_new');
        } catch (\Throwable $e) {
            $state->clearAll();
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'reload' => true]);
            exit;
        }

        // Lifecycle: deleted (old tree components not in new tree)
        Component::processDeleted();
        $t->mark('process_deleted');

        // Diff
        $patcher = new Patcher();
        $newUi->compare($oldUi, $patcher);
        $t->mark('diff');

        // Capture buffered output → console.log
        $output = ob_get_clean();
        if ($output !== '' && $output !== false) {
            JsRuntime::invoke(['console', 'log'], [$output]);
        }

        $newHash = self::computeStateHash($state);
        $t->mark('compute_hash');

        // Debug panel → console (prepended so it appears first)
        $appCalls = JsRuntime::drain();
        (new DebugPanel($newUi, $state, $t))->emit();
        $debugCalls = JsRuntime::drain();
        JsRuntime::prepend(array_merge($debugCalls, $appCalls));

        $response = [
            'success' => true,
            'js' => JsRuntime::dump(),
            'patches' => $patcher->getOperations(),
            'hash' => $newHash,
        ];

        $clientState = $state->getClientState();
        if ($clientState !== null) {
            $response['state'] = $clientState;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private static function handleGet(App $entry, StateManager $state): void
    {
        $t = new Timings();

        // If restoring serialized state crashes the render, drop all state
        // and retry from defaults. A second failure is propagated.
        try {
            PortalTarget::reset();
            $entry->runRegisterAssets();
            $ui = $entry->render($state, null, RenderPhase::Initial);
            $entry->finalize($state);
        } catch (\Throwable $e) {
            $state->clearAll();
            PortalTarget::reset();
            $entry = new ($entry::class)();
            $entry->runRegisterAssets();
            $ui = $entry->render($state, null, RenderPhase::Initial);
            $entry->finalize($state);
        }
        $t->mark('render');

        // Render the optional loader overlay so the DOM tree is complete.
        $loaderVNode = $entry->getLoader();
        $loaderDom = $loaderVNode?->render($state, null, RenderPhase::Initial);
        $t->mark('render_loader');

        $stateJs = $state->getClientJs();

        // Collect custom JS registered by components.
        $customJs = implode("\n", $entry->getCustomJs());

        $stateHash = self::computeStateHash($state);
        // Same source-mtime hash that HMR watches; bumping it on any PHP
        // change forces the browser to refetch /style.css.
        $styleHash = self::sourceHash(self::projectRoot());
        $t->mark('compute_hash');

        // Debug panel → inline script for initial render. Construct AFTER
        // timings so far so they appear in the debug output.
        (new DebugPanel($ui, $state, $t))->emit();
        $debugJs = self::callsToJs(JsRuntime::drain());

        $head = (new TagDomNode('head'))
            ->content(
                (new TagDomNode('meta'))->attr('charset', 'UTF-8'),
                (new TagDomNode('meta'))->attr('name', 'viewport')->attr('content', 'width=device-width, initial-scale=1.0'),
                (new TagDomNode('title'))->rawContent(htmlspecialchars($entry->title())),
                (new TagDomNode('script'))->rawContent(
                    'window.__SPWA_HASH=' . json_encode($stateHash) . ';'
                    . 'window.__SPWA_DEV=' . json_encode(self::isDevelopment()) . ';'
                ),
                // /style.css bundles preflight + the extracted utility rules
                // (in that order, so application rules win).
                (new TagDomNode('link'))->attr('rel', 'stylesheet')->attr('href', '/style.css?h=' . $styleHash),
                (new TagDomNode('script'))->attr('src', '/spwa.js')->rawContent(''),
                (new TagDomNode('script'))->rawContent($customJs),
            );

        if ($stateJs !== null) {
            $head->content((new TagDomNode('script'))->rawContent($stateJs));
        }

        $body = (new TagDomNode('body'))
            ->attr('style', 'margin: 0; font-family: system-ui, -apple-system, sans-serif;')
            ->content($ui);

        // Optional loader overlay — sibling of the App root so it isn't part
        // of the diff tree. Hidden by default; the frontend toggles its
        // display while a request is in flight.
        if ($loaderDom !== null) {
            $loaderWrapper = (new TagDomNode('div'))
                ->attr('data-spwa-loader', '')
                ->attr('style', 'display:none')
                ->content($loaderDom);
            $body->content($loaderWrapper);
        }

        $body->content((new TagDomNode('script'))->rawContent($debugJs));

        $document = (new TagDomNode('html'))
            ->attr('lang', 'en')
            ->content($head, $body);

        echo '<!DOCTYPE html>' . $document->toHtml();
    }

    /**
     * Convert raw JsRuntime call entries to inline JavaScript.
     */
    private static function callsToJs(array $calls): string
    {
        $js = '';
        foreach ($calls as [$mode, $path, $args]) {
            $pathStr = implode('.', $path);
            if ($mode === 'invoke') {
                $argsJson = implode(',', array_map(fn($a) => json_encode($a, JSON_UNESCAPED_SLASHES), $args));
                $js .= $pathStr . '(' . $argsJson . ');';
            }
        }
        return $js;
    }
}
