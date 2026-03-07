<?php

namespace Spwa\UI;

/**
 * Flex direction property.
 *
 * Usage:
 *   Direction::row()
 *   Direction::column()->md()
 */
class Direction extends Property
{
    public function __construct(
        protected DirectionValue $value
    ) {
    }

    protected function base(): string
    {
        return match ($this->value) {
            DirectionValue::Row => 'flex-row',
            DirectionValue::RowReverse => 'flex-row-reverse',
            DirectionValue::Column => 'flex-col',
            DirectionValue::ColumnReverse => 'flex-col-reverse',
        };
    }

    /**
     * Get the CSS value for this direction.
     */
    public function getCssValue(): string
    {
        return match ($this->value) {
            DirectionValue::Row => 'row',
            DirectionValue::RowReverse => 'row-reverse',
            DirectionValue::Column => 'column',
            DirectionValue::ColumnReverse => 'column-reverse',
        };
    }

    public static function row(): static
    {
        return new static(DirectionValue::Row);
    }

    public static function rowReverse(): static
    {
        return new static(DirectionValue::RowReverse);
    }

    public static function column(): static
    {
        return new static(DirectionValue::Column);
    }

    public static function columnReverse(): static
    {
        return new static(DirectionValue::ColumnReverse);
    }
}

enum DirectionValue: string
{
    case Row = 'row';
    case RowReverse = 'row-reverse';
    case Column = 'col';
    case ColumnReverse = 'col-reverse';
}
