<?php

namespace BrickPHP\Error;

use ReflectionClass;
use Throwable;

/**
 * A unified description of a PHP error, regardless of whether it came
 * from an uncaught Throwable, a `set_error_handler` callback, or
 * `error_get_last()` during shutdown.
 */
final class ErrorInfo
{
    public function __construct(
        public readonly string $type,
        public readonly string $message,
        public readonly string $file,
        public readonly int    $line,
        public readonly ?string $trace = null,
    ) {}

    public static function fromThrowable(Throwable $e): self
    {
        return new self(
            type: (new ReflectionClass($e))->getShortName(),
            message: $e->getMessage(),
            file: $e->getFile(),
            line: $e->getLine(),
            trace: $e->getTraceAsString(),
        );
    }

    /**
     * Build from the array returned by error_get_last().
     * @param array{type:int,message:string,file:string,line:int} $err
     */
    public static function fromLastError(array $err): self
    {
        return new self(
            type: self::typeLabel($err['type']),
            message: $err['message'],
            file: $err['file'],
            line: $err['line'],
            trace: null,
        );
    }

    /**
     * Whether a given errno from error_get_last() indicates an
     * unrecoverable error worth rendering an error page for.
     */
    public static function isFatal(int $errno): bool
    {
        return in_array($errno, [
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_CORE_WARNING,
            E_COMPILE_ERROR,
            E_COMPILE_WARNING,
            E_USER_ERROR,
        ], true);
    }

    private static function typeLabel(int $type): string
    {
        return match ($type) {
            E_ERROR             => 'Fatal Error',
            E_PARSE             => 'Parse Error',
            E_CORE_ERROR        => 'Core Error',
            E_CORE_WARNING      => 'Core Warning',
            E_COMPILE_ERROR     => 'Compile Error',
            E_COMPILE_WARNING   => 'Compile Warning',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_WARNING           => 'Warning',
            E_NOTICE            => 'Notice',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'User Deprecated',
            E_STRICT            => 'Strict',
            default             => 'Error',
        };
    }
}
