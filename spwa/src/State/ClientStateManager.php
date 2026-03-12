<?php

namespace Spwa\State;

/**
 * State manager for client-side storage (localStorage or sessionStorage).
 * State is sent from client in request payload and returned in response.
 */
class ClientStateManager implements StateManager
{
    private array $state = [];

    /**
     * Create from incoming request payload.
     * @param string $storageType 'localStorage' or 'sessionStorage'
     * @param array|null $clientState State received from client
     */
    public function __construct(
        private string $storageType = 'localStorage',
        ?array $clientState = null,
    )
    {
        if ($clientState !== null) {
            $this->state = $clientState;
        }
    }

    /**
     * Create from the current request (reads from payload).
     */
    public static function fromRequest(string $storageType = 'localStorage'): self
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $clientState = $payload['state'] ?? null;
        return new self($storageType, $clientState);
    }

    public function getState(string $path): array
    {
        return $this->state[$path] ?? [];
    }

    public function saveState(string $path, array $state): void
    {
        $this->state[$path] = $state;
    }

    public function getAll(): array
    {
        return $this->state;
    }

    public function getClientState(): ?array
    {
        // Always return state for client to store
        return $this->state;
    }

    public function getClientJs(): ?string
    {
        $storage = $this->storageType;
        $key = 'spwa_state';
        return <<<JS
SPWA.addStateHandler('$storage', {
    load: function() {
        try {
            var data = $storage.getItem('$key');
            return data ? JSON.parse(data) : {};
        } catch (e) { return {}; }
    },
    save: function(state) {
        try { $storage.setItem('$key', JSON.stringify(state)); } catch (e) {}
    },
    clear: function() {
        $storage.removeItem('$key');
    }
});
JS;
    }
}
