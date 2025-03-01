<?php

namespace Spwa\Js;

class Window
{
    // A string you want to display in the alert dialog, or, alternatively, an object that is converted into a string and displayed.
    static function alert($message): void
    {
        JsRuntime::invoke(["window", "alert"], [$message]);
    }
}