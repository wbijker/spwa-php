<?php

namespace Spwa;

use Spwa\Http\HttpRequest;
use Spwa\Http\HttpResponse;
use Spwa\Http\MiddlewareHandler;
use Spwa\Js\JS;
use Spwa\Js\JsRuntime;
use Spwa\Nodes\Component;
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

    private function initialize(StateManager $manager): Nodes\Node
    {
        $data = $_SESSION['state'] ?? null;
        $manager->unserialize($data);
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

    function handle(HttpRequest $request, callable $next): HttpResponse
    {
        if ($request->startWithSegment(['assets'])) {
            return $this->serveAsset($request);
        }

        $manager = new StateManager();
        $node = $this->initialize($manager);

        if ($request->isGet()) {
            $html = $node->renderHtml();
            $this->finalize($manager);
            return HttpResponse::html(fn() => $html . "<script>executeJsDump(" . json_encode(JsRuntime::dump()) . ")</script>");
        }

        // http post, read JSON body
        $json = $request->readJson(true);

        $event = $json['event'];
        $inputs = $json["inputs"];

        // handle bindings
//        foreach ($inputs as $path => $value) {
//            $pathData = $manager->restoreState(PathInfo::pathString(json_decode($path)));
//            if ($pathData->binding)
//                $pathData->binding->set($value);
//        }

        [$path, $event] = $event;
        // find event from frontend.
        // execute event that will likely change the dom
        $manager->triggerEvent(PathInfo::pathString($path), $event);

        // force a re-render
        $new = $this->component->render();
        $patch = new PatchBuilder();
        $node->compare($new, $patch);

        $this->finalize($manager);
        return HttpResponse::json([
            'p' => $patch->patches,
            'j' => JsRuntime::dump()
        ]);
    }

}