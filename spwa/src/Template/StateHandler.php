<?php

namespace Spwa\Template;

interface StateHandler
{
    function initialize();

    function save(string $state);

    function restore(): ?string;
}

