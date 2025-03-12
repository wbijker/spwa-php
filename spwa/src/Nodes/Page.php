<?php

namespace Spwa\Nodes;

use Spwa\FatalError;
use Spwa\Html\Div;
use Spwa\Html\ExternalScript;
use Spwa\Html\HtmlDocument;
use Spwa\Html\InlineScript;
use Spwa\Js\JsFunction;
use Spwa\Js\JsRuntime;

abstract class Page extends Component
{
    function error(FatalError $error): Node
    {
        return new Div(children: [new HtmlText("Error")]);
    }

    abstract function renderBody(): Node;

    function header(): array
    {
        return [];
    }

    function render(): HtmlNode
    {
        $headers = $this->header();
        $headers[] = new ExternalScript(src: "/assets/spwa.js");
        $headers[] = new InlineScript(JsFunction::create("executeJsDump", JsRuntime::dump()), ignore: true);

        return new HtmlDocument(
            lang: "en",
            head: $headers,
            body: $this->renderBody()
        );
    }
}

