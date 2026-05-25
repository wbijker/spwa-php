<?php

namespace Spwa\Js;

class Document
{
    static function setTitle(string $title): void
    {
        Js::assign(["document", "title"], $title);
    }
}