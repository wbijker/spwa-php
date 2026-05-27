<?php

namespace Spwa\Js;

class App
{
    static function refresh(): void
    {
        Js::run(Js::invoke(Js::obj('spwa', 'refresh')));
    }
}
