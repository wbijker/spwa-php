<?php

namespace Spwa\UI;

/**
 * Source element for video/audio/picture.
 */
class Source
{
    protected ?string $type = null;
    protected ?string $media = null;
    protected ?string $sizes = null;
    protected ?string $srcset = null;

    public function __construct(protected string $src)
    {
    }

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function media(string $media): static
    {
        $this->media = $media;
        return $this;
    }

    public function sizes(string $sizes): static
    {
        $this->sizes = $sizes;
        return $this;
    }

    public function srcset(string $srcset): static
    {
        $this->srcset = $srcset;
        return $this;
    }

    public function toNode(): Node
    {
        $node = Node::el('source')->attr('src', $this->src);

        if ($this->type !== null) {
            $node->attr('type', $this->type);
        }

        if ($this->media !== null) {
            $node->attr('media', $this->media);
        }

        if ($this->sizes !== null) {
            $node->attr('sizes', $this->sizes);
        }

        if ($this->srcset !== null) {
            $node->attr('srcset', $this->srcset);
        }

        return $node;
    }
}
