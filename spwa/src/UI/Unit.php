<?php

namespace Spwa\UI;

class Unit extends Style
{
    public static function none(): Unit
    {
        return new Unit();
    }

    static function sm(): Unit
    {
        return new Unit();
    }

    static function md(): Unit
    {
        return new Unit();
    }

    static function lg(): Unit
    {
        return new Unit();
    }

    static function xl(): Unit
    {
        return new Unit();

    }

    static function value(int $value): Unit
    {
        return new Unit();
    }

    public static function single(): Unit
    {
        return new Unit();
    }
}