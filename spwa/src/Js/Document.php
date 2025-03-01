<?php

namespace Spwa\Js;

class Document
{
    static function setTitle(string $title): void
    {
        JsRuntime::assign(["document", "title"], $title);
    }
}