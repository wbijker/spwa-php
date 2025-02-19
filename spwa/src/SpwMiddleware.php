<?php

namespace Spwa;

use Spwa\Html\HtmlDocument;
use Spwa\Http\HttpRequest;
use Spwa\Http\HttpResponse;
use Spwa\Http\MiddlewareHandler;
use Spwa\Js\JS;
use Spwa\Js\JsRuntime;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PatchBuilder;
use Spwa\Nodes\PathInfo;
use Spwa\Nodes\RenderContext;
use Spwa\Nodes\State;
use Spwa\Nodes\StateManager;

function joinPath(string ...$segments): string
{
    return implode(DIRECTORY_SEPARATOR, $segments);
}


session_start();

class SpwMiddleware implements MiddlewareHandler
{

    public function __construct(private Component $component)
    {
    }

    private function render(StateManager $manager): Nodes\Node
    {
        $this->component->initialize(null, PathInfo::root(), $manager);
        return $this->component->node;
    }

    private function finalize(StateManager $manager): void
    {
        $this->component->finalize($manager);
        $_SESSION['state'] = $manager->serialize();
    }

    function serveAsset(HttpRequest $request): HttpResponse
    {
        $path = joinPath(dirname(__DIR__), ...$request->segments());
        if (file_exists($path)) {
            return HttpResponse::file($path, "text/javascript");
        }
        return HttpResponse::notFound();
    }



    // handle bindings

    function handle(HttpRequest $request, callable $next): HttpResponse
    {
        if ($request->startWithSegment(['assets'])) {
            return $this->serveAsset($request);
        }

        $manager = new StateManager();
        $data = $_SESSION['state'] ?? null;
        $manager->unserialize($data);
        $node = $this->render($manager);

        if ($request->isGet()) {
            $html = $node->renderHtml();
            $this->finalize($manager);
            return HttpResponse::html(fn() => $html . "<script>executeJsDump(" . json_encode(JsRuntime::dump()) . ")</script>");
        }

        // http post, read JSON body
        $json = $request->readJson(true);

        $event = $json['event'];
        $inputs = $json["inputs"];

        /*foreach ($inputs as $path => $value) {

            $found = $node->find(json_decode($path));
            if ($found instanceof HtmlNode) {
                if ($found->bindings != null) {
                    $found->bindings = $value;
                }
            }
            JS::log("Binding: $path = $value", $found?->renderHtml());
        }*/

        [$path, $event, $args] = $event;
        // find event from frontend.
        // execute event that will likely change the dom

        $found = $node->find($path);
        if ($found instanceof HtmlNode) {
            $found->triggerEvent($event, $args);
        }

        // save the state after the events fired
        $this->finalize($manager);

        // force a re-render with the new state
        $new = $this->render($manager);

        $patch = new PatchBuilder();
        $node->compare($new, $patch);

        return HttpResponse::json([
            'p' => $patch->patches,
            'j' => JsRuntime::dump()
        ]);
    }

}