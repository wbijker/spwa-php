<?php

namespace Spwa\VNode;

use Spwa\UI\DomNode;

/**
 * Handles DOM patching operations.
 */
class Patcher
{
    /** @var array Collected patch operations */
    private array $operations = [];

    /**
     * Insert a node at the given path.
     * @param int[] $path
     * @param mixed $node
     */
    public function insertNode(array $path, mixed $node): void
    {
        $this->operations[] = [
            'type' => 'insert_node',
            'path' => $path,
            'node' => $node,
        ];
    }

    /**
     * Replace a node at the given path.
     * @param int[] $path
     * @param mixed $node
     */
    public function replaceNode(array $path, mixed $node): void
    {
        $this->operations[] = [
            'type' => 'replace_node',
            'path' => $path,
            'node' => $node,
        ];
    }

    /**
     * Delete a node at the given path.
     * @param int[] $path
     */
    public function deleteNode(array $path): void
    {
        $this->operations[] = [
            'type' => 'delete_node',
            'path' => $path,
        ];
    }

    /**
     * Replace text content at the given path.
     * @param int[] $path
     * @param string $text
     */
    public function replaceText(array $path, string $text): void
    {
        $this->operations[] = [
            'type' => 'replace_text',
            'path' => $path,
            'text' => $text,
        ];
    }

    /**
     * Set an attribute on a node at the given path.
     * @param int[] $path
     * @param string $name
     * @param string $value
     */
    public function setAttribute(array $path, string $name, string $value): void
    {
        $this->operations[] = [
            'type' => 'set_attribute',
            'path' => $path,
            'name' => $name,
            'value' => $value,
        ];
    }

    /**
     * Remove an attribute from a node at the given path.
     * @param int[] $path
     * @param string $name
     */
    public function removeAttribute(array $path, string $name): void
    {
        $this->operations[] = [
            'type' => 'remove_attribute',
            'path' => $path,
            'name' => $name,
        ];
    }

    /**
     * Insert a child at a specific index in a list.
     * @param int[] $parentPath Path to the list container
     * @param int $index Index to insert at
     * @param DomNode $node Node to insert
     */
    public function insertAt(array $parentPath, int $index, DomNode $node): void
    {
        $this->operations[] = [
            'type' => 'insert_at',
            'path' => $parentPath,
            'index' => $index,
            'node' => $node,
        ];
    }

    /**
     * Remove a child at a specific index in a list.
     * @param int[] $parentPath Path to the list container
     * @param int $index Index to remove
     */
    public function removeAt(array $parentPath, int $index): void
    {
        $this->operations[] = [
            'type' => 'remove_at',
            'path' => $parentPath,
            'index' => $index,
        ];
    }

    /**
     * Update/replace a child at a specific index in a list.
     * @param int[] $parentPath Path to the list container
     * @param int $index Index to update
     * @param DomNode $node New node
     */
    public function updateAt(array $parentPath, int $index, DomNode $node): void
    {
        $this->operations[] = [
            'type' => 'update_at',
            'path' => $parentPath,
            'index' => $index,
            'node' => $node,
        ];
    }

    /**
     * Get all collected operations.
     * Serializes DomNode objects to HTML strings.
     * @return array
     */
    public function getOperations(): array
    {
        return array_map(function ($op) {
            if (isset($op['node']) && $op['node'] instanceof DomNode) {
                $op['html'] = $op['node']->toHtml();
                unset($op['node']);
            }
            return $op;
        }, $this->operations);
    }

    /**
     * Clear all operations.
     */
    public function clear(): void
    {
        $this->operations = [];
    }
}
