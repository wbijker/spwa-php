<?php

namespace Spwa\Nodes;

use Spwa\FatalError;
use Spwa\Html\Div;
use Spwa\Html\ExternalScript;
use Spwa\Html\HtmlDocument;

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

        return new HtmlDocument(
            lang: "en",
            head: $headers,
            body: $this->renderBody()
        );
    }
}

