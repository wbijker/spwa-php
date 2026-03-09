<?php

namespace Spwa\State;

/**
 * Simple in-memory state manager implementation.
 */
class InMemoryStateManager implements StateManager
{
    /** @var array<string, array> */
    private array $states = [];

    /**
     * Get state for a given path.
     * @param string $path
     * @return array
     */
    public function getState(string $path): array
    {
        return $this->states[$path] ?? [];
    }

    /**
     * Save state for a given path.
     * @param string $path
     * @param array $state
     */
    public function saveState(string $path, array $state): void
    {
        $this->states[$path] = $state;
    }

    /**
     * Get all state.
     * @return array<string, array>
     */
    public function getAll(): array
    {
        return $this->states;
    }
}
