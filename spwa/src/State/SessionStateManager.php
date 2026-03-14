<?php

namespace Spwa\State;

/**
 * State manager that persists state in PHP session.
 * State is stored server-side, no client transport needed.
 */
class SessionStateManager extends StateManager
{
    private const SESSION_KEY = 'spwa_state';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    public function getState(string $path): array
    {
        return $_SESSION[self::SESSION_KEY][$path] ?? [];
    }

    public function saveState(string $path, array $state): void
    {
        $_SESSION[self::SESSION_KEY][$path] = $state;
    }

    public function getAll(): array
    {
        return $_SESSION[self::SESSION_KEY];
    }

    public function getClientState(): ?array
    {
        // Session state is server-side only
        return null;
    }

    public function getClientJs(): ?string
    {
        return null;
    }
}
