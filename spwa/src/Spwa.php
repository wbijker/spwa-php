<?php

namespace Spwa;

use Spwa\Debug\DebugPanel;
use Spwa\Debug\WireframeRenderer;
use Spwa\Debug\Timings;
use Spwa\Error\DefaultErrorPage;
use Spwa\Error\ErrorInfo;
use Spwa\Js\Js;
use Spwa\State\InMemoryStateManager;
use Spwa\State\StateManager;
use Spwa\UI\StyleGenerator;
use Spwa\UI\TagDomNode;
use Spwa\UI\UIElement;
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
     * Inline stylesheet for wireframe mode. Loaded only when the page is
     * rendered with ?wireframe=true. Keeps the original element box (so
     * margins/paddings/sizing stay intact) and overlays a dashed outline +
     * a small top-left tag with the construct/component name. The image
     * placeholder uses two CSS gradients to draw the classic crossed-out
     * rectangle so a sized <img> turns into a same-sized X box.
     */
    private const WIREFRAME_CSS = <<<'CSS'
.spwa-wf{outline:1px dashed rgba(60,80,120,0.45);outline-offset:-1px;position:relative;}
.spwa-wf-label{position:absolute;top:0;left:0;z-index:9999;font:10px/1 ui-monospace,SFMono-Regular,Menlo,monospace;background:#ffec99;color:#222;padding:2px 5px;pointer-events:none;border-bottom-right-radius:3px;white-space:nowrap;letter-spacing:.02em;}
.spwa-wf-img{background:
  linear-gradient(to top right,transparent calc(50% - 1px),rgba(60,80,120,0.4) 50%,transparent calc(50% + 1px)),
  linear-gradient(to top left,transparent calc(50% - 1px),rgba(60,80,120,0.4) 50%,transparent calc(50% + 1px));min-height:32px;}
.spwa-wf-hover{background-color:rgba(255,235,100,0.45) !important;}
CSS;

    /**
     * Inline IIFE injected on every dev-mode page (development=true). Plain
     * clicks pass through to the app; ctrl/cmd-click on a tagged element
     * logs its construct/component label + source file:line to the console
     * and navigates to the editor URL built from window.__SPWA_EDITOR_URL,
     * substituting {file}/{line}/{col}. If the template is empty (no
     * config.editor.url) the navigation step is skipped and only the
     * console line is emitted.
     *
     * Also installs a "w" keybind that flips ?wireframe= on/off via a
     * full page reload (the wireframe transform runs server-side, so a
     * fresh GET is the right way to toggle). Ignored when focus is in an
     * editable field, or when a modifier key is held.
     */
    private const INSPECT_JS = <<<'JS'
(function () {
  function buildHref(file, line, col) {
    var tpl = window.__SPWA_EDITOR_URL;
    if (!tpl || !file) return null;
    return tpl
      .split('{file}').join(file)
      .split('{line}').join(line || '1')
      .split('{col}').join(col || '1');
  }
  document.addEventListener('click', function (e) {
    if (!e.ctrlKey && !e.metaKey) return;
    var el = e.target && e.target.closest ? e.target.closest('[data-wf-label]') : null;
    if (!el) return;
    e.preventDefault();
    e.stopPropagation();
    var label = el.getAttribute('data-wf-label') || '?';
    var file = el.getAttribute('data-wf-file');
    var line = el.getAttribute('data-wf-line');
    var loc = file ? (file + (line ? ':' + line : '')) : '(unknown)';
    console.log('%c' + label, 'font-weight:bold;color:#a06010', '@', loc);
    var href = buildHref(file, line, 1);
    if (href) window.location.href = href;
  }, true);
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'w' && e.key !== 'W') return;
    if (e.ctrlKey || e.metaKey || e.altKey) return;
    var t = document.activeElement;
    if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT' || t.isContentEditable)) return;
    e.preventDefault();
    var url = new URL(window.location.href);
    if (url.searchParams.get('wireframe') === 'true') {
      url.searchParams.delete('wireframe');
    } else {
      url.searchParams.set('wireframe', 'true');
    }
    window.location.href = url.toString();
  });
})();
JS;

    /**
     * Inline IIFE injected only when ?wireframe=true (and dev mode is on).
     * Tracks the innermost tagged element under the cursor and toggles
     * .spwa-wf-hover for a translucent background highlight. Plain
     * (non-modifier) clicks are swallowed too — wireframe view is read-only.
     * Ctrl/cmd-click stays unhandled here; the INSPECT_JS handler logs it.
     */
    private const WIREFRAME_JS = <<<'JS'
(function () {
  var active = null;
  function clear() { if (active) { active.classList.remove('spwa-wf-hover'); active = null; } }
  document.addEventListener('mousemove', function (e) {
    var el = e.target && e.target.closest ? e.target.closest('[data-wf-label]') : null;
    if (el === active) return;
    clear();
    if (el) { el.classList.add('spwa-wf-hover'); active = el; }
  }, true);
  document.addEventListener('mouseleave', clear, true);
  document.addEventListener('click', function (e) {
    if (e.ctrlKey || e.metaKey) return;
    var el = e.target && e.target.closest ? e.target.closest('[data-wf-label]') : null;
    if (!el) return;
    e.preventDefault();
    e.stopPropagation();
  }, true);
})();
JS;

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

        // Dev mode flips the capture flag for the whole request — every
        // UIElement::__construct walks the call stack once to stamp file:line
        // on its DOM node so ctrl+click in the page can log "this came from
        // News.php:75". Done at the very top so it covers POST replays too
        // (handlePost rebuilds the app twice for OLD/NEW). Captured paths
        // are rewritten through host_root so they survive the container
        // boundary when the dev's editor opens the link.
        $isDev = self::isDevelopment();
        UIElement::$captureSource = $isDev;
        if ($isDev) {
            UIElement::$sourceRoot = rtrim(dirname($_SERVER['SCRIPT_FILENAME'] ?? '', 2), '/');
            UIElement::$hostRoot = rtrim(self::editorHostRoot() ?? UIElement::$sourceRoot, '/');
        }

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
     * long-poll (both the IIFE in spwa.js, via window.__SPWA_DEV, the
     * inlined script in error pages, and the /hmr.php endpoint itself).
     */
    public static function isDevelopment(): bool
    {
        return (bool)(self::config()['development'] ?? false);
    }

    /**
     * URL template (config.editor.url) used by ctrl+click to jump into the
     * dev's editor. Empty when unconfigured — the inspector falls back to
     * console.log.
     */
    public static function editorUrlTemplate(): string
    {
        return (string)(self::config()['editor']['url'] ?? '');
    }

    /**
     * Host-side absolute path of this project (config.editor.host_root) —
     * the prefix the editor link needs so the OS can find the file when
     * PHP runs under a different mount path (Docker, VM, etc.). Returns
     * null when unset — UIElement falls back to the auto-detected server
     * root and the rewrite is a no-op.
     */
    public static function editorHostRoot(): ?string
    {
        $v = self::config()['editor']['host_root'] ?? null;
        return is_string($v) && $v !== '' ? $v : null;
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
     * Cheap source fingerprint: newest mtime + file count, joined with a
     * colon. Doubles as HMR change signal and /style.css cache-buster — an
     * edit bumps mtime, an add/remove bumps count. Walks .php and .css under
     * the directory named by config()['source']['dir'], pruning basenames
     * listed in config()['source']['exclude'] (matches both directories and
     * files).
     */
    public static function sourceHash(): string
    {
        $src = self::config()['source'] ?? [];
        $configDir = dirname($_SERVER['SCRIPT_FILENAME'] ?? '');
        $root = $src['dir'] ?? '..';
        if ($root === '' || $root[0] !== '/') {
            $root = $configDir . '/' . $root;
        }
        $skip = array_flip($src['exclude'] ?? []);

        $dir = new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS);
        $filter = new \RecursiveCallbackFilterIterator($dir, function ($f) use ($skip) {
            if (isset($skip[$f->getFilename()])) return false;
            if ($f->isFile()) {
                $ext = $f->getExtension();
                return $ext === 'php' || $ext === 'css';
            }
            return true;
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
        // Empty path = the root element itself. explode(',', '') returns
        // [''] which intvals to [0] (== the first child), so guard.
        $path = $pathStr === '' ? [] : array_map('intval', explode(',', $pathStr));
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
            Js::invoke(['console', 'log'], [$output]);
        }

        $newHash = self::computeStateHash($state);
        $t->mark('compute_hash');

        // Debug panel → console (prepended so it appears first)
        $appCalls = Js::drain();
        (new DebugPanel($newUi, $state, $t))->emit();
        $debugCalls = Js::drain();
        Js::prepend(array_merge($debugCalls, $appCalls));

        $response = [
            'success' => true,
            'js' => Js::dump(),
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

        // Wireframe is a dev-only tool — production renders ignore
        // ?wireframe= entirely. Source capture (UIElement::$captureSource) is
        // already on for the whole request when isDevelopment, set in run().
        $isDev = self::isDevelopment();
        $wireframe = $isDev && (bool)filter_input(INPUT_GET, 'wireframe', FILTER_VALIDATE_BOOLEAN);

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

        // Wireframe transform after the real render so the original styles
        // (margins, paddings, sizing) are preserved — the wireframe only
        // overlays a dashed outline + label and substitutes leaf content.
        if ($wireframe) {
            $ui = WireframeRenderer::transform($ui);
            $t->mark('wireframe');
        }

        // Render the optional loader overlay so the DOM tree is complete.
        $loaderVNode = $entry->getLoader();
        $loaderDom = $loaderVNode?->render($state, null, RenderPhase::Initial);
        $t->mark('render_loader');

        $stateJs = $state->getClientJs();

        // Collect custom JS / inline CSS registered by components.
        $inlineScripts = implode("\n", $entry->getScriptsInline());
        $inlineStyles = implode("\n", $entry->getStylesInline());

        $stateHash = self::computeStateHash($state);
        // Same source-mtime hash that HMR watches; bumping it on any PHP/CSS
        // change forces the browser to refetch /style.css.
        $styleHash = self::sourceHash();
        $t->mark('compute_hash');

        // Debug panel → inline script for initial render. Construct AFTER
        // timings so far so they appear in the debug output.
        (new DebugPanel($ui, $state, $t))->emit();
        $debugJs = self::callsToJs(Js::drain());

        $bootJs = 'window.__SPWA_HASH=' . json_encode($stateHash) . ';'
                . 'window.__SPWA_DEV=' . json_encode($isDev) . ';';
        if ($isDev) {
            $tpl = self::editorUrlTemplate();
            if ($tpl !== '') {
                $bootJs .= 'window.__SPWA_EDITOR_URL=' . json_encode($tpl) . ';';
            }
        }

        $head = (new TagDomNode('head'))
            ->content(
                (new TagDomNode('meta'))->attr('charset', 'UTF-8'),
                (new TagDomNode('meta'))->attr('name', 'viewport')->attr('content', 'width=device-width, initial-scale=1.0'),
                (new TagDomNode('title'))->rawContent(htmlspecialchars($entry->title())),
                (new TagDomNode('script'))->rawContent($bootJs),
                // /style.css bundles preflight + the extracted utility rules
                // (in that order, so application rules win).
                (new TagDomNode('link'))->attr('rel', 'stylesheet')->attr('href', '/style.css?h=' . $styleHash),
            );

        // User-registered external stylesheets — placed after /style.css so
        // their rules can override framework defaults.
        foreach ($entry->getStyles() as $href) {
            $head->content((new TagDomNode('link'))->attr('rel', 'stylesheet')->attr('href', $href));
        }
        // User inline styles after externals, so they override.
        if ($inlineStyles !== '') {
            $head->content((new TagDomNode('style'))->rawContent($inlineStyles));
        }

        // Framework runtime first so SPWA.* is available to user scripts.
        $head->content((new TagDomNode('script'))->attr('src', '/spwa.js')->rawContent(''));
        foreach ($entry->getScripts() as $script) {
            $tag = (new TagDomNode('script'))->attr('src', $script['src']);
            if ($script['defer']) {
                $tag->attr('defer', 'defer');
            }
            $head->content($tag->rawContent(''));
        }
        if ($inlineScripts !== '') {
            $head->content((new TagDomNode('script'))->rawContent($inlineScripts));
        }

        if ($wireframe) {
            $head->content((new TagDomNode('style'))->rawContent(self::WIREFRAME_CSS));
        }

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

        // Inspect logger goes on every dev page so ctrl+click + the "w"
        // keybind work without having to switch into wireframe mode first.
        // Wireframe mode then adds the hover-highlight + plain-click swallow
        // on top.
        if ($isDev) {
            $body->content((new TagDomNode('script'))->rawContent(self::INSPECT_JS));
        }
        if ($wireframe) {
            $body->content((new TagDomNode('script'))->rawContent(self::WIREFRAME_JS));
        }

        $document = (new TagDomNode('html'))
            ->attr('lang', 'en')
            ->content($head, $body);

        echo '<!DOCTYPE html>' . $document->toHtml();
    }

    /**
     * Concatenate queued JsStatements into inline JavaScript for the
     * initial GET response. Each statement renders itself (JsExpression
     * for direct invoke/assign/raw, JsDomReadyBlock for the coalesced
     * SPWA.ready wrapper), then we join with `;`.
     *
     * @param \Spwa\Js\JsStatement[] $calls
     */
    private static function callsToJs(array $calls): string
    {
        $js = '';
        foreach ($calls as $stmt) {
            $js .= $stmt->toJs() . ';';
        }
        return $js;
    }
}
