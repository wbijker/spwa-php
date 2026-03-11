<?php

namespace Spwa\UI;

use Spwa\Dom\Levenshtein;
use Spwa\VNode\Patcher;

/**
 * A DOM node optimized for lists with keyed children.
 * Uses Levenshtein algorithm for efficient diffing.
 */
class ListDomNode extends TagDomNode
{
    /** @var KeyedDomNode[] */
    protected array $keyedChildren = [];

    /**
     * Add keyed child nodes.
     */
    public function items(KeyedDomNode ...$children): static
    {
        $this->keyedChildren = array_merge($this->keyedChildren, $children);
        return $this;
    }

    /**
     * Get keyed children.
     * @return KeyedDomNode[]
     */
    public function getKeyedChildren(): array
    {
        return $this->keyedChildren;
    }

    /**
     * Assign paths to child nodes.
     */
    protected function assignChildPaths(): void
    {
        $index = 0;
        foreach ($this->keyedChildren as $keyed) {
            $keyed->node->assignPaths([...$this->path, $index]);
            $index++;
        }
    }

    /**
     * Collect all styles from this node and descendants.
     * @return array<string, array<string, string>>
     */
    public function collectStyles(): array
    {
        $allStyles = $this->styles;

        foreach ($this->keyedChildren as $keyed) {
            $allStyles = array_merge($allStyles, $keyed->node->collectStyles());
        }

        return $allStyles;
    }

    /**
     * Render to HTML string.
     */
    public function toHtml(): string
    {
        // Build class attribute
        $allClasses = $this->classes;
        if (isset($this->attributes['class'])) {
            $allClasses = array_merge(explode(' ', $this->attributes['class']), $allClasses);
        }

        // Build attributes
        $attrHtml = '';

        // Add data-path attribute
        $attrHtml .= ' data-path="' . implode(',', $this->path) . '"';

        if (!empty($allClasses)) {
            $attrHtml .= ' class="' . htmlspecialchars(implode(' ', array_unique($allClasses))) . '"';
        }
        foreach ($this->attributes as $name => $value) {
            if ($name === 'class') continue;
            $attrHtml .= ' ' . $name . '="' . htmlspecialchars($value) . '"';
        }

        // Build children
        $childrenHtml = '';
        foreach ($this->keyedChildren as $keyed) {
            $childrenHtml .= $keyed->node->toHtml();
        }

        return "<{$this->tag}{$attrHtml}>{$childrenHtml}</{$this->tag}>";
    }

    /**
     * Find a node by its path.
     * @param int[] $targetPath
     * @return DomNode|null
     */
    public function findByPath(array $targetPath): ?DomNode
    {
        if ($this->path === $targetPath) {
            return $this;
        }

        foreach ($this->keyedChildren as $keyed) {
            $found = $keyed->node->findByPath($targetPath);
            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    /**
     * Compare this node with another and generate patches.
     * Uses Levenshtein algorithm for efficient list diffing.
     */
    public function compare(DomNode $other, Patcher $patcher): void
    {
        // If other is not a ListDomNode or tag differs, replace entirely
        if (!$other instanceof ListDomNode || $this->tag !== $other->tag) {
            $patcher->replaceNode($this->path, $this);
            return;
        }

        // Compare attributes
        $thisAttrs = $this->attributes;
        $otherAttrs = $other->attributes;

        foreach ($thisAttrs as $name => $value) {
            if (!isset($otherAttrs[$name]) || $otherAttrs[$name] !== $value) {
                $patcher->setAttribute($this->path, $name, $value);
            }
        }

        foreach ($otherAttrs as $name => $value) {
            if (!isset($thisAttrs[$name])) {
                $patcher->removeAttribute($this->path, $name);
            }
        }

        // Compare classes
        if ($this->classes !== $other->classes) {
            $patcher->setAttribute($this->path, 'class', implode(' ', array_unique($this->classes)));
        }

        // Use Levenshtein for keyed children comparison
        $keyFn = fn(KeyedDomNode $item) => $item->key;

        $diff = Levenshtein::diff($other->keyedChildren, $this->keyedChildren, $keyFn);

        // Process diff operations in reverse order (as Levenshtein returns them backwards)
        $diff = array_reverse($diff);

        $oldIndex = 0;
        $newIndex = 0;

        foreach ($diff as [$action, $oldItem, $newItem]) {
            $childPath = [...$this->path, $newIndex];

            switch ($action) {
                case Levenshtein::SKIP:
                    // Same key - compare the nodes recursively
                    if ($oldItem !== null && $newItem !== null) {
                        $newItem->node->compare($oldItem->node, $patcher);
                    }
                    $oldIndex++;
                    $newIndex++;
                    break;

                case Levenshtein::SUBSTITUTE:
                    // Key changed - replace node
                    if ($newItem !== null) {
                        $patcher->replaceNode($childPath, $newItem->node);
                    }
                    $oldIndex++;
                    $newIndex++;
                    break;

                case Levenshtein::INSERT:
                    // New item inserted
                    if ($newItem !== null) {
                        $patcher->insertNode($childPath, $newItem->node);
                    }
                    $newIndex++;
                    break;

                case Levenshtein::DELETE:
                    // Item removed
                    $deletePath = [...$this->path, $oldIndex];
                    $patcher->deleteNode($deletePath);
                    $oldIndex++;
                    break;
            }
        }
    }
}
