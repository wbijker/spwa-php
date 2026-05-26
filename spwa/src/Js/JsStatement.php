<?php

namespace Spwa\Js;

/**
 * Anything that can be queued by Js and rendered to a JS statement
 * string at dump time. Implemented by JsExpression (single
 * invoke/assign/raw call) and JsDomReadyBlock (a coalesced
 * SPWA.ready wrapper over many statements).
 */
interface JsStatement
{
    public function toJs(): string;
}
