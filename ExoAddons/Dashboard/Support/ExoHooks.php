<?php

namespace ExoAddons\Dashboard\Support;

/**
 * ExoHooks — Lightweight event system for addon communication.
 *
 * Usage:
 *   ExoHooks::listen('item.sold', fn($data) => ...);
 *   ExoHooks::fire('item.sold', ['item' => $item, 'buyer' => $buyer]);
 */
class ExoHooks
{
    /** @var array<string, callable[]> */
    protected static array $listeners = [];

    /**
     * Register a listener for a hook.
     */
    public static function listen(string $hook, callable $callback): void
    {
        static::$listeners[$hook][] = $callback;
    }

    /**
     * Fire a hook, calling all registered listeners.
     *
     * @return array Results from each listener
     */
    public static function fire(string $hook, array $data = []): array
    {
        $results = [];

        foreach (static::$listeners[$hook] ?? [] as $callback) {
            try {
                $results[] = $callback($data);
            } catch (\Throwable $e) {
                // One bad listener shouldn't break others
                \Illuminate\Support\Facades\Log::warning("[ExoHooks] Listener for '{$hook}' threw: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Check if a hook has any listeners.
     */
    public static function has(string $hook): bool
    {
        return !empty(static::$listeners[$hook]);
    }

    /**
     * Remove all listeners for a hook (e.g. on addon uninstall).
     */
    public static function forget(string $hook): void
    {
        unset(static::$listeners[$hook]);
    }

    /**
     * Remove all listeners for all hooks.
     */
    public static function reset(): void
    {
        static::$listeners = [];
    }
}
