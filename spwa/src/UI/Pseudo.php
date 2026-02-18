<?php

namespace Spwa\UI;

/**
 * CSS pseudo-classes and pseudo-elements.
 */
enum Pseudo: string
{
    // Interaction
    case Hover = 'hover';
    case Active = 'active';
    case Focus = 'focus';
    case FocusVisible = 'focus-visible';
    case FocusWithin = 'focus-within';
    case Visited = 'visited';

    // State
    case Disabled = 'disabled';
    case Enabled = 'enabled';
    case Checked = 'checked';
    case Required = 'required';
    case Valid = 'valid';
    case Invalid = 'invalid';
    case Placeholder = 'placeholder';

    // Structural
    case FirstChild = 'first';
    case LastChild = 'last';
    case OnlyChild = 'only';
    case Odd = 'odd';
    case Even = 'even';
    case Empty = 'empty';
}
