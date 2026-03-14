<?php

namespace Spwa\State;

abstract class StateManager
{
    /**
     * Get state for a given path.
     * @param string $path
     * @return array
     */
    abstract public function getState(string $path): array;

    /**
     * Save state for a given path.
     * @param string $path
     * @param array $state
     */
    abstract public function saveState(string $path, array $state): void;

    /**
     * Get all state.
     * @return array<string, array>
     */
    abstract public function getAll(): array;

    /**
     * Get state to send to client (for client-side storage).
     * Returns null if state is managed server-side only.
     * @return array|null
     */
    abstract public function getClientState(): ?array;

    /**
     * Get JavaScript to include on the page for client-side state handling.
     * Returns null if no client-side JS is needed.
     */
    abstract public function getClientJs(): ?string;

    /**
     * Display name for this state manager.
     */
    public function name(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * Size of the current state in bytes (serialized).
     */
    public function bytes(): int
    {
        return strlen(serialize($this->getAll()));
    }
}
