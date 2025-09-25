<?php

namespace Spwa\UI;

class FlexElement extends Element
{
    public function __construct()
    {
        parent::__construct();
        $this->classes[] = "flex";
    }


    function alignCenter(): static
    {
        $this->classes[] = "justify-center";
        return $this;
    }

    function alignMiddle(): static
    {
        $this->classes[] = "items-center";
        return $this;
    }

    function alignTop(): static
    {
        $this->classes[] = "items-start";
        return $this;
    }

    function alignBottom(): static
    {
        $this->classes[] = "items-end";
        return $this;
    }


}