<?php

namespace Spwa\Js;

class Window
{
    static function alert($message): void
    {
        $arg = is_string($message) ? Js::str($message) : $message;
        Js::run(Js::invoke(Js::obj('window', 'alert'), $arg));
    }
}
