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
        return new Div(class: "bg-red-600 text-white", children: [
            new HtmlText("Error" . $error->type . $error->message . " in file " . $error->file . " on line " . $error->line)
        ]);
    }

    abstract function renderBody(): Node;

    function header(): array
    {
        return [];
    }

    function build(Node $body): HtmlNode
    {
        $headers = $this->header();
        $headers[] = new ExternalScript(src: "/assets/spwa.js");

        return new HtmlDocument(
            lang: "en",
            head: $headers,
            body: $body
        );
    }


    function render(): HtmlNode
    {
        return $this->build($this->renderBody());
    }
}

