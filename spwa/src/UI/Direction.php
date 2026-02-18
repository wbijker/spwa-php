<?php

namespace Spwa\UI;

/**
 * Flex direction with stateful breakpoint support.
 *
 * Usage:
 *   Direction::row()              // flex-row
 *   Direction::column()->sm()     // sm:flex-col
 *   UI::flex(Direction::row()->lg()->column())  // flex-col lg:flex-row
 */
enum Direction: string
{
    case Row = 'row';
    case RowReverse = 'row-reverse';
    case Column = 'col';
    case ColumnReverse = 'col-reverse';

    public function toClass(): string
    {
        return 'flex-' . $this->value;
    }
}

/**
 * Stateful direction value that can change at breakpoints.
 */
class DirectionValue extends StateValue
{
    public function __construct(
        protected Direction $direction
    ) {
    }

    protected function getBaseClass(): string
    {
        return $this->direction->toClass();
    }

    public static function row(): static
    {
        return new static(Direction::Row);
    }

    public static function rowReverse(): static
    {
        return new static(Direction::RowReverse);
    }

    public static function column(): static
    {
        return new static(Direction::Column);
    }

    public static function col(): static
    {
        return new static(Direction::Column);
    }

    public static function columnReverse(): static
    {
        return new static(Direction::ColumnReverse);
    }

    public static function colReverse(): static
    {
        return new static(Direction::ColumnReverse);
    }
}
