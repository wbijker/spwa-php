<?php

namespace Spwa\UI;

/**
 * Builder for CSS sub-selectors used inside :has(), :not(), etc.
 *
 * Can be called both statically (to start a chain) and on instances (to extend):
 *   Selector::child()->lastChild()->nthChild('4n-1')->not(Selector::nthChild('3n'))
 *   // produces: >:last-child:nth-child(4n-1):not(:nth-child(3n))
 *
 * @method static static lastChild()
 * @method static static firstChild()
 * @method static static onlyChild()
 * @method static static nthChild(string $expr)
 * @method static static nthLastChild(string $expr)
 * @method static static not(Selector $inner)
 * @method Selector lastChild()
 * @method Selector firstChild()
 * @method Selector onlyChild()
 * @method Selector nthChild(string $expr)
 * @method Selector nthLastChild(string $expr)
 * @method Selector not(Selector $inner)
 */
class Selector
{
    private bool $directChild = false;
    /** @var string[] */
    private array $parts = [];

    /**
     * Direct child combinator (>).
     */
    public static function child(): static
    {
        $s = new static();
        $s->directChild = true;
        return $s;
    }

    public function __call(string $name, array $args): static
    {
        return $this->apply($name, ...$args);
    }

    public static function __callStatic(string $name, array $args): static
    {
        $s = new static();
        return $s->apply($name, ...$args);
    }

    private function apply(string $name, mixed ...$args): static
    {
        return match ($name) {
            'lastChild' => $this->addPart(':last-child'),
            'firstChild' => $this->addPart(':first-child'),
            'onlyChild' => $this->addPart(':only-child'),
            'nthChild' => $this->addPart(':nth-child(' . $args[0] . ')'),
            'nthLastChild' => $this->addPart(':nth-last-child(' . $args[0] . ')'),
            'not' => $this->addPart(':not(' . $args[0]->toSelector() . ')'),
            default => throw new \BadMethodCallException("Method $name does not exist on Selector"),
        };
    }

    private function addPart(string $part): static
    {
        $this->parts[] = $part;
        return $this;
    }

    public function toSelector(): string
    {
        return ($this->directChild ? '>' : '') . implode('', $this->parts);
    }

    public function __toString(): string
    {
        return $this->toSelector();
    }
}
