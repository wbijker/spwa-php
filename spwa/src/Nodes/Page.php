<?php

namespace Spwa\Nodes;

use Spwa\Html\Div;
use Spwa\Html\HtmlDocument;
use Spwa\Html\ExternalScript;
use Spwa\Html\InlineScript;
use Spwa\Js\JsFunction;
use Spwa\Js\JsRuntime;

abstract class Page extends Component
{
    function error(): Node
    {
        return new Div(children: [new HtmlText("Error")]);
    }

    abstract function renderBody(): Node;

    function header(): array
    {
        return [];
    }

    // 1. Keep all paths
    // 2. document.body should always be [1, 1]
    // 3. InlineScript compare as JS execute patch
    // 4. Once-off element
    // 5. JS dump special element
    // 6. React like API for state?
    // 7. SkipPatch rename to ShouldUpdate


    function render(): HtmlNode
    {
        // Statemanager::save("specialKey");
//        $state = $context->useGlobalState(new GlobalState());

        $headers = $this->header();
        $headers[] = new ExternalScript(src: "/assets/spwa.js");
        $headers[] = new InlineScript(JsFunction::create("executeJsDump", JsRuntime::dump()));

        return new HtmlDocument(
            lang: "en",
            head: $headers,
            body: $this->renderBody()
        );
    }
}

