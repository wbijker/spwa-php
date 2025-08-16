<?php

namespace CodeQuery\Expressions;

class Frame
{
    const UNIT_ROWS = 'ROWS';
    const UNIT_RANGE = 'RANGE';
    const UNIT_GROUPS = 'GROUPS';


    public function __construct($unit, ?FrameBound $start, ?FrameBound $end)
    {
    }

    public static function rows(?FrameBound $start, ?FrameBound $end): Frame
    {
        return new Frame(self::UNIT_ROWS, $start, $end);
    }

    public static function range(?FrameBound $start, ?FrameBound $end): Frame
    {
        return new Frame(self::UNIT_RANGE, $start, $end);
    }

    public static function groups(?FrameBound $start, ?FrameBound $end): Frame
    {
        return new Frame(self::UNIT_ROWS, $start, $end);
    }
}

class FrameBound
{
    const BOUND_UNBOUNDED = 'UNBOUNDED';
    const BOUND_CURRENT_ROW = 'CURRENT_ROW';
    const BOUND_VALUE = "VALUE";

    public function __construct(public $bondType, public ?int $value = null)
    {
        // $bondType can be UNBOUNDED, CURRENT_ROW or value
        // $value is only used if bondType is value
    }

    public static function unbounded()
    {
        return new FrameBound(self::BOUND_UNBOUNDED);
    }

    public static function value(int $value)
    {
        return new FrameBound(self::BOUND_VALUE, $value);
    }

    public static function currentRow()
    {
        return new FrameBound(self::currentRow());
    }
}

class OverExpression implements SqlExpression
{
    // $base should be a valid Window function base expression
    // dedicated window functions like ROW_NUMBER(), RANK(), etc.
    // and some aggregate functions can accept an OVER clause
    public function __construct(private SqlExpression  $base,
                                private ?SqlExpression $oderBy = null,
                                private ?SqlExpression $partitionBy = null,
                                private ?Frame         $frame = null)
    {
    }

    function toSql(): string
    {
        $q = "";
        if ($this->oderBy != null)
            $q .= "ORDER BY " . $this->oderBy->toSql();
        if ($this->partitionBy != null)
            $q .= "PARTITION BY" . $this->partitionBy->toSql();

        if ($this->frame != null) {
            // ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING
        }

        return $this->base->toSql() . " OVER ($q)";
    }
}