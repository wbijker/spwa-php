<?php

namespace Spwa\Template;

class SessionStateHandler implements StateHandler
{
    function initialize()
    {
        session_name("state");
        session_start();
    }

    function save(string $state)
    {
        $_SESSION['state'] = $state;
    }

    function restore(): ?string
    {
        if ($_SESSION['state']) {
            return $_SESSION['state'];
        }
        return null;
    }
}