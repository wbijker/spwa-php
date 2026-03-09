<?php

namespace Spwa\State;

/**
 * State manager that persists state in PHP session.
 */
class SessionStateManager implements StateManager
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

    /**
     * Get state for a given path.
     * @param string $path
     * @return array
     */
    public function getState(string $path): array
    {
        return $_SESSION[self::SESSION_KEY][$path] ?? [];
    }

    /**
     * Save state for a given path.
     * @param string $path
     * @param array $state
     */
    public function saveState(string $path, array $state): void
    {

        $_SESSION[self::SESSION_KEY][$path] = $state;
    }

    /**
     * Get all state.
     * @return array<string, array>
     */
    public function getAll(): array
    {
        return $_SESSION[self::SESSION_KEY];
    }
}
