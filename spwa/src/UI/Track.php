<?php

namespace Spwa\UI;

/**
 * Track element for video/audio captions/subtitles.
 */
class Track
{
    protected ?string $kind = null;
    protected ?string $label = null;
    protected ?string $srclang = null;
    protected bool $default = false;

    public function __construct(protected string $src)
    {
    }

    public function kind(string $kind): static
    {
        $this->kind = $kind;
        return $this;
    }

    public function subtitles(): static
    {
        return $this->kind('subtitles');
    }

    public function captions(): static
    {
        return $this->kind('captions');
    }

    public function descriptions(): static
    {
        return $this->kind('descriptions');
    }

    public function chapters(): static
    {
        return $this->kind('chapters');
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function srclang(string $srclang): static
    {
        $this->srclang = $srclang;
        return $this;
    }

    public function default(bool $default = true): static
    {
        $this->default = $default;
        return $this;
    }

    public function toNode(): Node
    {
        $node = Node::el('track')->attr('src', $this->src);

        if ($this->kind !== null) {
            $node->attr('kind', $this->kind);
        }

        if ($this->label !== null) {
            $node->attr('label', $this->label);
        }

        if ($this->srclang !== null) {
            $node->attr('srclang', $this->srclang);
        }

        if ($this->default) {
            $node->attr('default', 'default');
        }

        return $node;
    }
}
