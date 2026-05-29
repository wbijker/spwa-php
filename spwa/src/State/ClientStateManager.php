<?php

namespace Spwa\State;

/**
 * State manager for client-side storage (localStorage or sessionStorage).
 * State is sent from client in request payload and returned in response.
 */
class ClientStateManager extends StateManager
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
        // A full-page (client=false) event posts a urlencoded form, not the
        // usual JSON body, with the state blob in a hidden _spwaState field.
        // Read it from there so client-side state survives the navigation;
        // otherwise fall back to the JSON body the AJAX path sends.
        if (isset($_POST['_spwaState'])) {
            $clientState = json_decode($_POST['_spwaState'], true);
        } else {
            $payload = json_decode(file_get_contents('php://input'), true);
            $clientState = $payload['state'] ?? null;
        }
        return new self($storage, is_array($clientState) ? $clientState : null);
    }

    public function getState(string $path): array
    {
        return $this->state[$path] ?? [];
    }

    public function saveState(string $path, array $state): void
    {
        $this->state[$path] = $state;
    }

    public function removeState(string $path): void
    {
        unset($this->state[$path]);
    }

    public function clearAll(): void
    {
        $this->state = [];
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
