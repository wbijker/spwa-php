<?php

namespace Spwa\State;

/**
 * State manager that persists state in cookies.
 * State is stored client-side in a cookie, sent with each request.
 */
class CookieStateManager extends StateManager
{
    private const COOKIE_NAME = 'spwa_state';
    private array $state = [];

    public function __construct(
        private int $expiry = 86400 * 30, // 30 days default
        private string $path = '/',
        private bool $secure = false,
        private bool $httpOnly = true
    ) {
        $this->loadFromCookie();
    }

    private function loadFromCookie(): void
    {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $decoded = base64_decode($_COOKIE[self::COOKIE_NAME], true);
            if ($decoded !== false) {
                $data = json_decode($decoded, true);
                if (is_array($data)) {
                    $this->state = $data;
                }
            }
        }
    }

    private function saveToCookie(): void
    {
        $encoded = base64_encode(json_encode($this->state));
        setcookie(
            self::COOKIE_NAME,
            $encoded,
            time() + $this->expiry,
            $this->path,
            '',
            $this->secure,
            $this->httpOnly
        );
    }

    public function getState(string $path): array
    {
        return $this->state[$path] ?? [];
    }

    public function saveState(string $path, array $state): void
    {
        $this->state[$path] = $state;
        $this->saveToCookie();
    }

    public function getAll(): array
    {
        return $this->state;
    }

    public function getClientState(): ?array
    {
        // Cookie state is handled by browser automatically
        return null;
    }

    public function getClientJs(): ?string
    {
        return null;
    }
}
