<?php

namespace Spwa\State;

/**
 * Static registry of all available state managers.
 * Components reference these directly: StateManagers::$session, StateManagers::$localStorage, etc.
 */
class StateManagers
{
    public static SessionStateManager $session;
    public static ClientStateManager $localStorage;
    public static ClientStateManager $sessionStorage;
    public static CookieStateManager $cookie;

    public static function init(): void
    {
        self::$session = new SessionStateManager();
        self::$localStorage = new ClientStateManager(ClientStorage::LocalStorage);
        self::$sessionStorage = new ClientStateManager(ClientStorage::SessionStorage);
        self::$cookie = new CookieStateManager();
    }

    /**
     * @return StateManager[]
     */
    private static function all(): array
    {
        return [self::$session, self::$localStorage, self::$sessionStorage, self::$cookie];
    }

    public static function getClientJs(): ?string
    {
        $js = '';
        foreach (self::all() as $manager) {
            $managerJs = $manager->getClientJs();
            if ($managerJs !== null) {
                $js .= $managerJs . "\n";
            }
        }
        return $js === '' ? null : $js;
    }

    public static function getClientState(): ?array
    {
        $states = [];
        foreach (self::all() as $manager) {
            $clientState = $manager->getClientState();
            if ($clientState !== null) {
                $states[] = $clientState;
            }
        }
        return empty($states) ? null : array_merge(...$states);
    }
}
