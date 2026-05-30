<?php

namespace BrickPHP\UI;

use BrickPHP\VNode\VNode;

/**
 * Sample-content factories for wireframes, mockups, and empty/loading
 * states. Each method returns a VNode with representative placeholder
 * text for the named concept — short labels, sentences, contact info,
 * dates, numbers, etc. — so a layout can be filled in before real
 * data is wired up.
 */
class PlaceHolder
{
    public static function label(): VNode
    {
        return UI::text('Label');
    }

    public static function sentence(): VNode
    {
        return UI::text('The quick brown fox jumps over the lazy dog.');
    }

    public static function question(): VNode
    {
        return UI::text('What is your favorite color?');
    }

    public static function paragraph(): VNode
    {
        return UI::text(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. '
            . 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. '
            . 'Ut enim ad minim veniam, quis nostrud exercitation ullamco '
            . 'laboris nisi ut aliquip ex ea commodo consequat.'
        );
    }

    public static function paragraphs(): VNode
    {
        return UI::column()
            ->gap(Unit::rem(0.75))
            ->content(
                self::paragraph(),
                self::paragraph(),
                self::paragraph(),
            );
    }

    public static function email(): VNode
    {
        return UI::text('jane.doe@example.com');
    }

    public static function name(): VNode
    {
        return UI::text('Jane Doe');
    }

    public static function phone(): VNode
    {
        return UI::text('+1 (555) 123-4567');
    }

    public static function number(): VNode
    {
        return UI::text('12,345');
    }

    public static function webpageUrl(): VNode
    {
        return UI::text('https://example.com');
    }

    public static function time(): VNode
    {
        return UI::text('14:30');
    }

    public static function shortDate(): VNode
    {
        return UI::text('2026-05-25');
    }

    public static function longDate(): VNode
    {
        return UI::text('May 25, 2026');
    }

    public static function dateTime(): VNode
    {
        return UI::text('May 25, 2026, 2:30 PM');
    }

    public static function integer(): VNode
    {
        return UI::text('42');
    }

    public static function decimal(): VNode
    {
        return UI::text('3.14');
    }

    public static function percent(): VNode
    {
        return UI::text('75%');
    }
}
