<?php

namespace Spwa;

use Spwa\Html\Div;
use Spwa\Http\HttpRequest;
use Spwa\Http\HttpResponse;
use Spwa\Http\MiddlewareHandler;
use Spwa\Js\Console;
use Spwa\Js\JsRuntime;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\Page;
use Spwa\Nodes\PatchBuilder;
use Spwa\Nodes\PathInfo;
use Spwa\Nodes\StateManager;
use Throwable;


function joinPath(string ...$segments): string
{
    return implode(DIRECTORY_SEPARATOR, $segments);
}


session_start();

class SpwMiddleware implements MiddlewareHandler
{
    /**
     * @param callable(): Page $render
     */
    public function __construct(private $render)
    {
    }

    private function finalize(Component $component, StateManager $manager): void
    {
        $component->finalize($manager);
        $_SESSION['state'] = $manager->serialize();
    }

    function serveAsset(HttpRequest $request): HttpResponse
    {
        $path = joinPath(dirname(__DIR__), ...$request->path->getSegments());
        if (file_exists($path)) {
            return HttpResponse::file($path, "text/javascript");
        }
        return HttpResponse::notFound();
    }

    function handle(HttpRequest $request, callable $next): HttpResponse
    {
        if ($request->path->startWithSegment(['', 'assets'])) {
            return $this->serveAsset($request);
        }

        $p = ($this->render)();
        $ret = $this->safeInvoke($p, fn() => $this->innerHandle($request));

        if ($ret instanceof Throwable) {
            $template = $p->build($p->error($ret));

            if ($request->isGet()) {
                return HttpResponse::html($template->renderHtml());
            }

            $patch = new PatchBuilder();
            // dummy div to create node with path []
            $patch->replace($template, new Div());

            return HttpResponse::json([
                'p' => $patch->patches,
                'j' => JsRuntime::dump()
            ]);
        }

        return $ret;
    }

    private function processOutput(): void
    {
        $clean = ob_get_clean();
        if ($clean !== false && $clean !== "")
            Console::log($clean);
    }

    function safeInvoke(Page $page, callable $callback): mixed
    {
        $error = null;
        // Warnings and notices
        // script will continue executing at the line after the one where the error occurred.
        set_error_handler(function (int $errno, string $message, string $file, int $line) {
            $msg = "$message in $file on line $line";
            switch ($errno) {
                // Run-time warnings (non-fatal errors). Execution of the script is not halted.
                case E_WARNING:
                case E_USER_WARNING:
                    Console::warn("Warning: " . $msg);
                    break;

                // Run-time notices. Indicate that the script encountered something that
                // could indicate an error, but could also happen in the normal course of
                // running a script.
                case E_NOTICE:
                case E_USER_NOTICE:
                    Console::warn("Notice: " . $msg);
                    break;

                default:
                    Console::warn("Other Error [$errno]: " . $msg);
                    break;
            }
        });

        // Fatal errors require register_shutdown_function() with error_get_last().
        register_shutdown_function(function () use ($page) {
            // some errors are not throwable and will terminate execution
//            ob_end_clean(); // Discard PHP's default error message

            $lastError = error_get_last();
            if ($lastError) {
                $n = $page->build($page->error(new FatalErrorException($lastError)));
                echo $n->renderHtml();
            }
        });

        try {
            $error = $callback();
        } catch (Throwable $ex) {
            $error = $ex;
        }

        restore_error_handler();
        restore_exception_handler();

        return $error;
    }


    private function innerHandle(HttpRequest $request): HttpResponse
    {
        ob_start();

        $manager = new StateManager();
        $data = $_SESSION['state'] ?? null;
        $manager->unserialize($data);

        $component = ($this->render)();

        $component->initialize(null, PathInfo::root(), $manager);
        $node = $component->node;
        $manager->clear();

        if ($request->isGet()) {
            $this->finalize($component, $manager);

            $this->processOutput();
            $html = $node->renderHtml();
            return HttpResponse::html(fn() => $html);
        }

        // http post, read JSON body
        $json = $request->readJson(true);
        $this->processPayload($node, $json);

        // save the state after the events fired
        $this->finalize($component, $manager);

        $patch = new PatchBuilder();

        $new = ($this->render)();
        $new->initializeAndCompare(null, PathInfo::root(), $manager, $component, $patch);

        $this->processOutput();

        return HttpResponse::json([
            'p' => $patch->patches,
            'j' => JsRuntime::dump()
        ]);
    }

    private function processPayload(Node $node, ?array $json): void
    {
        if ($json == null)
            return;

        $event = $json['event'] ?? null;
        if ($event != null) {
            [$path, $event, $args] = $event;
            // find event from frontend.
            // execute event that will likely change the dom

            $found = $node->find($path);
            if ($found instanceof HtmlNode) {
                $found->triggerEvent($event, $args);
            }
        }

        //  $inputs = $json["inputs"] ?? null;
        /*foreach ($inputs as $path => $value) {
            $found = $node->find(json_decode($path));
            if ($found instanceof HtmlNode) {
                if ($found->bindings != null) {
                    $found->bindings = $value;
                }
            }
            JS::log("Binding: $path = $value", $found?->renderHtml());
        }*/
    }
}