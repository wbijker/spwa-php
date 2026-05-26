<?php

namespace Spwa\Js;

/**
 * Accumulator that wraps any number of statements in a single
 * `SPWA.ready(function () { ... })` call. Every Js::domReady(...)
 * invocation appends to the same block, so the final dump emits
 * exactly one ready wrapper regardless of how many call sites
 * registered work — the inner statements run in source order under
 * one DOMContentLoaded gate.
 */
class JsDomReadyBlock implements JsStatement
{
    /** @var JsExpression[] */
    private array $statements = [];

    public function add(JsExpression $stmt): void
    {
        $this->statements[] = $stmt;
    }

    public function toJs(): string
    {
        $body = implode(';', array_map(fn(JsExpression $e) => $e->toJs(), $this->statements));
        return "SPWA.ready(function(){{$body}})";
    }
}
