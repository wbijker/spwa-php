<?php

namespace BrickPHP\Js;

class Document
{
    static function setTitle(string $title): void
    {
        Js::run(Js::assign(Js::obj('document', 'title'), Js::str($title)));
    }
}
