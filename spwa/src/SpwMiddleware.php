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

class SpwMiddleware implements MiddlewareHandler
{

    public function __construct(private Component $component)
    {
    }

    function handle(HttpRequest $request, callable $next): HttpResponse
    {
        if ($request->startWithSegment(['assets'])) {
            $path = joinPath(dirname(__DIR__), ...$request->segments());
            if (file_exists($path)) {
                return HttpResponse::file($path, "text/javascript");
            }
            return HttpResponse::notFound();
        }

        $manager = new StateManager();
        $this->component->initialize(null, PathInfo::root(), $manager);
        $html = $this->component->renderHtml();
        return HttpResponse::html(fn() => $html."<script>executeJsDump(". json_encode(JsRuntime::dump()). ")</script>");


        ob_start();
        session_start();
        $data = $_SESSION['state'] ?? null;

        $manager = new StateManager();
        $manager->unserialize($data);

        $this->component->initialize(null, PathInfo::root(get_class($this->component)), $manager);
        $node = $this->component->node;


        if ($request->isGet()) {

            $ret = HttpResponse::html(fn() => $node->renderHtml()."<script>executeJsDump(". json_encode(JsRuntime::dump()). ")</script>");
            $this->component->finalize($manager);
            $_SESSION['state'] = $manager->serialize();

            JS::log("State", $_SESSION['state']);

            return $ret;
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

        $ret = HttpResponse::json([
            'p' => $patch->patches,
            'j' => JsRuntime::dump()
        ]);

        $this->component->finalize($manager);
        $_SESSION['state'] = $manager->serialize();

        return $ret;
    }

}