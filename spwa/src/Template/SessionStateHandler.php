<?php

namespace Spwa\Template;

use Spwa\Js\JS;

class SessionStateHandler implements StateHandler
{
    function initialize(): void
    {
        session_name("state");
        session_start();
    }

    /**
     * @throws \Exception
     */
    function save(Component $component): void
    {
        $ser = $component->serialize();
        if ($ser == null)
            return;
        $_SESSION['state'] = gzcompress($ser);
        JS::log("State legth", strlen($_SESSION['state']));
    }

    function restore(Component $component): void
    {
        $ser = $_SESSION['state'] ?? null;
        if ($ser === null) {
            return;
        }
        $component->unserialize(gzuncompress($ser));
    }
}