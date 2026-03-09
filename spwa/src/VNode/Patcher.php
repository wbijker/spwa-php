<?php

namespace Spwa\VNode;

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
     * Get all collected operations.
     * @return array
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * Clear all operations.
     */
    public function clear(): void
    {
        $this->operations = [];
    }
}
