<?php

namespace Spwa\Template;

class PathData
{
    /**
     * @var Event[]
     */
    public array $events;

    // component instance
    public ?Component $component;

    /**
     * @param Event[] $events
     * @param Component|null $component
     */
    public function __construct(array $events, ?Component $component)
    {
        $this->events = $events;
        $this->component = $component;
    }


}