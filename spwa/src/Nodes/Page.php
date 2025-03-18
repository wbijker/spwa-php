<?php

namespace Spwa\Nodes;

use Spwa\Html\Div;
use Spwa\Html\ExternalScript;
use Spwa\Html\HtmlDocument;

abstract class Page extends Component
{
    function error(\Throwable $error): Node
    {
        $stackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $traceHtml = [];

        foreach ($stackTrace as $index => $trace) {
            $file = $trace['file'] ?? '[internal]';
            $line = $trace['line'] ?? 'N/A';
            $function = $trace['function'] ?? 'unknown';

            $traceHtml[] = new Div(class: "p-2 border-b border-gray-700", children: [
                new HtmlText("#{$index} {$file} (line {$line}): {$function}()")
            ]);
        }

        return new Div(class: "bg-red-700 text-white min-h-screen flex flex-col items-center justify-center p-10", children: [
            new Div(class: "bg-red-800 p-6 rounded shadow-lg w-full max-w-2xl", children: [
                new Div(class: "text-xl font-bold mb-4", children: [
                    new HtmlText("Fatal Error: " . $error->getCode())
                ]),
                new Div(class: "text-lg mb-2", children: [
                    new HtmlText($error->getMessage())
                ]),
                new Div(class: "text-sm opacity-75", children: [
                    new HtmlText("File: " . $error->getFile() . " (Line " . $error->getLine() . ")")
                ]),
                new Div(class: "mt-4 p-4 bg-gray-900 rounded text-gray-300 text-xs overflow-auto", children: $traceHtml)
            ])
        ]);

//        return new Div(class: "bg-red-600 text-white", children: [
//            new HtmlText("Error" . $error->type . $error->message . " in file " . $error->file . " on line " . $error->line)
//        ]);
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

