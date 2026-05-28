<?php

namespace Spwa\Events;

use Spwa\Js\Js;

/**
 * Wires a native DOM listener through the diff via addEventListener, so
 * native events flow through the same add/remove/delete lifecycle as custom
 * ones — no inline `on<event>` attributes.
 *
 * add() binds `SPWA.bindEvent(path, domType, event, capture)`; remove()
 * unbinds it. When remove() fires because the handler was dropped from a
 * surviving element it does the real detach; when it fires because the node
 * itself left the tree the element is already gone, so the emitted unbind is
 * a harmless no-op (the browser drops the listeners with the node and the
 * client's patch handler scrubs them as it removes the element).
 *
 * `$domType` is the real DOM event name passed to addEventListener (e.g.
 * 'change' for the logical 'upload'); the logical event name is forwarded
 * separately so the dispatched server event keeps its identity.
 */
final class NativeEventRegistration implements EventRegistration
{
    public function __construct(
        private string     $domType,
        private EventPhase $phase,
    ) {
    }

    public function add(array $path, string $event): void
    {
        Js::run(Js::invoke(
            Js::obj('SPWA', 'bindEvent'),
            $path,
            Js::str($this->domType),
            Js::str($event),
            $this->phase->useCapture(),
        ));
    }

    public function remove(array $path, string $event): void
    {
        Js::run(Js::invoke(
            Js::obj('SPWA', 'unbindEvent'),
            $path,
            Js::str($this->domType),
            $this->phase->useCapture(),
        ));
    }
}
