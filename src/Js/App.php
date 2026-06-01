<?php

namespace BrickPHP\Js;

class App
{
    static function refresh(): void
    {
        Js::run(Js::invoke(Js::obj('Brick', 'refresh')));
    }
}
