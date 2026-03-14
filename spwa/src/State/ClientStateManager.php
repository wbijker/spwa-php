<?php

namespace Spwa\State;

/**
 * State manager for client-side storage (localStorage or sessionStorage).
 * State is sent from client in request payload and returned in response.
 */
class ClientStateManager implements StateManager
{
    private array $state = [];

    public function __construct(
        private ClientStorage $storage = ClientStorage::LocalStorage,
        ?array $clientState = null,
    )
    {
        if ($clientState !== null) {
            $this->state = $clientState;
        }
    }

    public static function fromRequest(ClientStorage $storage = ClientStorage::LocalStorage): self
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $clientState = $payload['state'] ?? null;
        return new self($storage, $clientState);
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
        $storage = $this->storage->value;
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
