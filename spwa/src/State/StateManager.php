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

    /**
     * Get state to send to client (for client-side storage).
     * Returns null if state is managed server-side only.
     * @return array|null
     */
    public function getClientState(): ?array;

    /**
     * Get JavaScript to include on the page for client-side state handling.
     * Returns null if no client-side JS is needed.
     */
    public function getClientJs(): ?string;
}
