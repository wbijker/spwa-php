<?php

namespace Spwa\State;

interface StateManager
{
    /**
     * Get state for a given path.
     * @param string $path
     * @return array
     */
    public function getState(string $path): array;

    /**
     * Save state for a given path.
     * @param string $path
     * @param array $state
     */
    public function saveState(string $path, array $state): void;

    /**
     * Get all state.
     * @return array<string, array>
     */
    public function getAll(): array;
}
