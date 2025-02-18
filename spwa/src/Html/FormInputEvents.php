<?php

namespace Spwa\Html;

class FormInputEvents
{
    /**
     * @param (callable(): void)|null $onChange
     * @param (callable(): void)|null $onInput
     * @param (callable(): void)|null $onFocus
     * @param (callable(): void)|null $onBlur
     * @param (callable(): void)|null $onReset
     * @param (callable(): void)|null $onSubmit
     * @param (callable(): void)|null $onInvalid
     * @param (callable(): void)|null $onSelect
     */
    public function __construct(
        public $onChange = null,
        public $onInput = null,
        public $onFocus = null,
        public $onBlur = null,
        public $onReset = null,
        public $onSubmit = null,
        public $onInvalid = null,
        public $onSelect = null
    )
    {
    }
}