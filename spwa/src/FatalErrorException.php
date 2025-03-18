<?php

namespace Spwa;

use Error;

class FatalErrorException extends Error
{
    public function __construct(array $error)
    {
        parent::__construct($error['message'] . $error['file'] . $error['line']);
    }
}