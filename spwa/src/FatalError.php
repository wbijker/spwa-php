<?php

namespace Spwa;

/*
 * Common error structure for fatal- and exception based errors
 */

class FatalError
{
    function __construct(
        public int    $type,
        public string $message,
        public string $file,
        public int    $line,
        ?\Throwable   $throwable = null)
    {
    }

    static function fromThrowable(\Throwable $throwable): FatalError
    {
        return new FatalError(
            $throwable->getCode(),
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine(),
            $throwable
        );
    }

    static function fromError(array $error): FatalError
    {
        return new FatalError(
            $error['type'],
            $error['message'],
            $error['file'],
            $error['line']
        );
    }
}