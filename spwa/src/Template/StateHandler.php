<?php

namespace Spwa\Template;

interface StateHandler
{
    function initialize();

    function save(Component $component): void;

    function restore(Component $component): void;
}

