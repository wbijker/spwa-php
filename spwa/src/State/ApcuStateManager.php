<?php

namespace Spwa\State;

/**
 * State manager backed by APCu (PHP's shared in-process user cache).
 *
 * State is stored as a single array under one APCu key per user/session, so
 * each request does one apcu_fetch on load and one apcu_store on save. APCu
 * lives in the PHP-FPM worker pool's shared memory — it's per-host and
 * volatile (wiped on pool restart or expiry), so prefer it for ephemeral
 * working state and fall back to a durable manager (session, DB) for data
 * that must survive a deploy.
 *
 * Per-user isolation is achieved by namespacing the APCu key with a token
 * (the PHP session id by default). Override the token if you need a stable
 * identifier independent of PHP sessions.
 */
class ApcuStateManager extends StateManager
{
    private const KEY_PREFIX = 'spwa_state:';

    private string $key;

    /** @var array<string, array>|null In-process cache to avoid repeated apcu_fetch in one request */
    private ?array $cache = null;

    public function __construct(?string $userToken = null, private int $ttl = 86400)
    {
        if (!function_exists('apcu_fetch') || !apcu_enabled()) {
            throw new \RuntimeException(
                'APCu is not available. Install the `apcu` PECL extension and set apc.enabled=1.'
            );
        }

        if ($userToken === null) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $userToken = session_id() ?: 'anon';
        }

        $this->key = self::KEY_PREFIX . $userToken;
    }

    public function getState(string $path): array
    {
        return $this->load()[$path] ?? [];
    }

    public function saveState(string $path, array $state): void
    {
        $all = $this->load();
        $all[$path] = $state;
        $this->store($all);
    }

    public function removeState(string $path): void
    {
        $all = $this->load();
        unset($all[$path]);
        $this->store($all);
    }

    public function clearAll(): void
    {
        apcu_delete($this->key);
        $this->cache = [];
    }

    public function getAll(): array
    {
        return $this->load();
    }

    public function getClientState(): ?array
    {
        return null;
    }

    public function getClientJs(): ?string
    {
        return null;
    }

    /**
     * @return array<string, array>
     */
    private function load(): array
    {
        if ($this->cache === null) {
            $value = apcu_fetch($this->key, $success);
            $this->cache = $success && is_array($value) ? $value : [];
        }
        return $this->cache;
    }

    /**
     * @param array<string, array> $state
     */
    private function store(array $state): void
    {
        apcu_store($this->key, $state, $this->ttl);
        $this->cache = $state;
    }
}
