<?php

namespace Spwa\Js;

class Js
{
    /** @var JsStatement[] Pending statements to flush to the client */
    static array $calls = [];

    /**
     * Queue a function call. Returns a JsExpression that can be nested
     * inside another invoke()/assign() to build call-chain syntax — when
     * a nested expression is embedded, it's removed from the pending
     * queue so it doesn't fire as a separate statement.
     *
     *   Js::invoke([Js::invoke(['L', 'map'], ['map']), 'setView'],
     *              [[51.505, -0.09], 13]);
     *   // → L.map("map").setView([51.505,-0.09],13)
     */
    static function invoke(array $path, array $args): JsExpression
    {
        self::pluckEmbedded($path);
        self::pluckEmbedded($args);
        $expr = new JsExpression('invoke', $path, $args);
        self::$calls[] = $expr;
        return $expr;
    }

    /**
     * Wrap statements so they run after DOMContentLoaded (synchronously
     * if it's already fired). Each inner statement is plucked from the
     * pending queue and appended to the single shared JsDomReadyBlock —
     * every call across this request coalesces into ONE wrapper:
     *
     *   SPWA.ready(function () { s1; s2; s3; ... });
     *
     * The first call positions the block in the queue at its call site;
     * subsequent calls only append to that block without reordering.
     *
     *   Js::domReady(
     *       Js::assign(['window', 'foo'], Js::invoke(['Bar', 'make'], [])),
     *       Js::invoke(['window', 'foo', 'init'], []),
     *   );
     */
    static function domReady(JsExpression ...$statements): void
    {
        foreach ($statements as $stmt) {
            $i = array_search($stmt, self::$calls, true);
            if ($i !== false) {
                array_splice(self::$calls, $i, 1);
            }
        }

        $block = self::findDomReadyBlock();
        if ($block === null) {
            $block = new JsDomReadyBlock();
            self::$calls[] = $block;
        }

        foreach ($statements as $stmt) {
            $block->add($stmt);
        }
    }

    /** Locate the shared ready-block in the current queue, or null if none exists yet. */
    private static function findDomReadyBlock(): ?JsDomReadyBlock
    {
        foreach (self::$calls as $call) {
            if ($call instanceof JsDomReadyBlock) {
                return $call;
            }
        }
        return null;
    }

    /** Queue a property assignment. The value may itself be a nested JsExpression. */
    static function assign(array $obj, mixed $value): JsExpression
    {
        self::pluckEmbedded($obj);
        self::pluckEmbedded($value);
        $expr = new JsExpression('assign', $obj, $value);
        self::$calls[] = $expr;
        return $expr;
    }

    /**
     * Walk a path/value (recursively into arrays) and yank any
     * JsExpressions out of the pending queue — they're being embedded
     * inside a new outer expression and shouldn't run as standalone
     * statements.
     */
    private static function pluckEmbedded(mixed $node): void
    {
        if ($node instanceof JsExpression) {
            $i = array_search($node, self::$calls, true);
            if ($i !== false) {
                array_splice(self::$calls, $i, 1);
            }
            return;
        }
        if (is_array($node)) {
            foreach ($node as $item) {
                self::pluckEmbedded($item);
            }
        }
    }

    /** Re-insert statements at the front of the queue. Used to reorder when emitting the response. */
    static function prepend(array $calls): void
    {
        array_unshift(self::$calls, ...$calls);
    }

    /** @return JsStatement[] */
    static function drain(): array
    {
        $calls = self::$calls;
        self::$calls = [];
        return $calls;
    }

    /**
     * Wire format for the client: each pending statement rendered to a
     * standalone JS string. Client evaluates them in order.
     *
     * @return string[]
     */
    static function dump(): array
    {
        return array_map(fn(JsStatement $s) => $s->toJs(), self::$calls);
    }
}
