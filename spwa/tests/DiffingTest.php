<?php

namespace Spwa\Tests;

use PHPUnit\Framework\TestCase;
use Spwa\UI\TagDomNode;
use Spwa\UI\TextDomNode;
use Spwa\VNode\Patcher;

class DiffingTest extends TestCase
{
    private function makeList(array $items, bool $keyed = false): TagDomNode
    {
        $list = new TagDomNode('ul');
        foreach ($items as $item) {
            $li = new TagDomNode('li');
            $li->content($item);
            if ($keyed) {
                $li->key($item);
            }
            $list->content($li);
        }
        $list->assignPaths([]);
        return $list;
    }

    private function diff(TagDomNode $new, TagDomNode $old): array
    {
        $patcher = new Patcher();
        $new->compare($old, $patcher);
        return $patcher->getOperations();
    }

    // =========================================================
    // Positional diffing
    // =========================================================

    public function testIdenticalTreesProduceNoPatches(): void
    {
        $old = $this->makeList(['A', 'B', 'C']);
        $new = $this->makeList(['A', 'B', 'C']);

        $ops = $this->diff($new, $old);

        $this->assertEmpty($ops);
    }

    public function testTextChangeProducesReplaceText(): void
    {
        $old = $this->makeList(['A', 'B', 'C']);
        $new = $this->makeList(['A', 'X', 'C']);

        $ops = $this->diff($new, $old);

        $replaceTexts = array_filter($ops, fn($o) => $o['type'] === 'replace_text');
        $this->assertCount(1, $replaceTexts);
        $op = array_values($replaceTexts)[0];
        $this->assertEquals('X', $op['text']);
        $this->assertEquals([1, 0], $op['path']);
    }

    public function testAppendChildProducesInsertNode(): void
    {
        $old = $this->makeList(['A', 'B']);
        $new = $this->makeList(['A', 'B', 'C']);

        $ops = $this->diff($new, $old);

        $inserts = array_filter($ops, fn($o) => $o['type'] === 'insert_node');
        $this->assertCount(1, $inserts);
        $op = array_values($inserts)[0];
        $this->assertEquals([2], $op['path']);
    }

    public function testRemoveChildProducesDeleteNode(): void
    {
        $old = $this->makeList(['A', 'B', 'C']);
        $new = $this->makeList(['A', 'B']);

        $ops = $this->diff($new, $old);

        $deletes = array_filter($ops, fn($o) => $o['type'] === 'delete_node');
        $this->assertCount(1, $deletes);
        $op = array_values($deletes)[0];
        $this->assertEquals([2], $op['path']);
    }

    public function testRemoveMultipleChildrenDeletesInReverseOrder(): void
    {
        $old = $this->makeList(['A', 'B', 'C', 'D', 'E']);
        $new = $this->makeList(['A', 'B']);

        $ops = $this->diff($new, $old);

        $deletes = array_values(array_filter($ops, fn($o) => $o['type'] === 'delete_node'));
        $this->assertCount(3, $deletes);
        // Must be in reverse order for correct DOM manipulation
        $this->assertEquals([4], $deletes[0]['path']);
        $this->assertEquals([3], $deletes[1]['path']);
        $this->assertEquals([2], $deletes[2]['path']);
    }

    public function testTagChangeProducesReplaceNode(): void
    {
        $old = new TagDomNode('div');
        $old->assignPaths([]);
        $new = new TagDomNode('span');
        $new->assignPaths([]);

        $ops = $this->diff($new, $old);

        $this->assertCount(1, $ops);
        $this->assertEquals('replace_node', $ops[0]['type']);
    }

    public function testAttributeAddProducesSetAttribute(): void
    {
        $old = new TagDomNode('div');
        $old->assignPaths([]);
        $new = new TagDomNode('div');
        $new->attr('id', 'main');
        $new->assignPaths([]);

        $ops = $this->diff($new, $old);

        $this->assertCount(1, $ops);
        $this->assertEquals('set_attribute', $ops[0]['type']);
        $this->assertEquals('id', $ops[0]['name']);
        $this->assertEquals('main', $ops[0]['value']);
    }

    public function testAttributeRemoveProducesRemoveAttribute(): void
    {
        $old = new TagDomNode('div');
        $old->attr('id', 'main');
        $old->assignPaths([]);
        $new = new TagDomNode('div');
        $new->assignPaths([]);

        $ops = $this->diff($new, $old);

        $this->assertCount(1, $ops);
        $this->assertEquals('remove_attribute', $ops[0]['type']);
        $this->assertEquals('id', $ops[0]['name']);
    }

    public function testClassChangeProducesSetAttribute(): void
    {
        $old = new TagDomNode('div');
        $old->class('foo');
        $old->assignPaths([]);
        $new = new TagDomNode('div');
        $new->class('bar');
        $new->assignPaths([]);

        $ops = $this->diff($new, $old);

        $sets = array_filter($ops, fn($o) => $o['type'] === 'set_attribute' && $o['name'] === 'class');
        $this->assertCount(1, $sets);
        $this->assertEquals('bar', array_values($sets)[0]['value']);
    }

    // =========================================================
    // Keyed diffing
    // =========================================================

    public function testKeyedIdenticalTreesProduceNoPatches(): void
    {
        $old = $this->makeList(['A', 'B', 'C'], keyed: true);
        $new = $this->makeList(['A', 'B', 'C'], keyed: true);

        $ops = $this->diff($new, $old);

        $this->assertEmpty($ops);
    }

    public function testKeyedRemoveMiddleItem(): void
    {
        $old = $this->makeList(['A', 'B', 'C'], keyed: true);
        $new = $this->makeList(['A', 'C'], keyed: true);

        $ops = $this->diff($new, $old);

        // Should remove B at index 1
        $removes = array_filter($ops, fn($o) => $o['type'] === 'remove_at');
        $this->assertCount(1, $removes);
        $op = array_values($removes)[0];
        $this->assertEquals(1, $op['index']);
    }

    public function testKeyedRemoveFirstItem(): void
    {
        $old = $this->makeList(['A', 'B', 'C'], keyed: true);
        $new = $this->makeList(['B', 'C'], keyed: true);

        $ops = $this->diff($new, $old);

        $removes = array_filter($ops, fn($o) => $o['type'] === 'remove_at');
        $this->assertCount(1, $removes);
        $op = array_values($removes)[0];
        $this->assertEquals(0, $op['index']);
    }

    public function testKeyedRemoveLastItem(): void
    {
        $old = $this->makeList(['A', 'B', 'C'], keyed: true);
        $new = $this->makeList(['A', 'B'], keyed: true);

        $ops = $this->diff($new, $old);

        $removes = array_filter($ops, fn($o) => $o['type'] === 'remove_at');
        $this->assertCount(1, $removes);
        $op = array_values($removes)[0];
        $this->assertEquals(2, $op['index']);
    }

    public function testKeyedInsertMiddleItem(): void
    {
        $old = $this->makeList(['A', 'C'], keyed: true);
        $new = $this->makeList(['A', 'B', 'C'], keyed: true);

        $ops = $this->diff($new, $old);

        $inserts = array_filter($ops, fn($o) => $o['type'] === 'insert_at');
        $this->assertCount(1, $inserts);
        $op = array_values($inserts)[0];
        $this->assertEquals(1, $op['index']);
    }

    public function testKeyedInsertAtStart(): void
    {
        $old = $this->makeList(['B', 'C'], keyed: true);
        $new = $this->makeList(['A', 'B', 'C'], keyed: true);

        $ops = $this->diff($new, $old);

        $inserts = array_filter($ops, fn($o) => $o['type'] === 'insert_at');
        $this->assertCount(1, $inserts);
        $op = array_values($inserts)[0];
        $this->assertEquals(0, $op['index']);
    }

    public function testKeyedFilterFromFiveToTwo(): void
    {
        // Simulates filtering: 5 items → 2 items (keeping items B and D)
        $old = $this->makeList(['A', 'B', 'C', 'D', 'E'], keyed: true);
        $new = $this->makeList(['B', 'D'], keyed: true);

        $ops = $this->diff($new, $old);

        // Should remove A(0), C(2), E(4) — 3 removals
        $removes = array_filter($ops, fn($o) => $o['type'] === 'remove_at');
        $this->assertCount(3, $removes);

        // No inserts — B and D already existed
        $inserts = array_filter($ops, fn($o) => $o['type'] === 'insert_at');
        $this->assertEmpty($inserts);

        // No replace_node or replace_text — items are unchanged
        $replaces = array_filter($ops, fn($o) => in_array($o['type'], ['replace_node', 'replace_text']));
        $this->assertEmpty($replaces);
    }

    public function testKeyedRemoveMultipleInReverseOrder(): void
    {
        $old = $this->makeList(['A', 'B', 'C', 'D', 'E'], keyed: true);
        $new = $this->makeList(['A', 'E'], keyed: true);

        $ops = $this->diff($new, $old);

        $removes = array_values(array_filter($ops, fn($o) => $o['type'] === 'remove_at'));
        $this->assertCount(3, $removes);

        // Must be in reverse order for correct DOM manipulation
        $indices = array_map(fn($o) => $o['index'], $removes);
        $this->assertEquals([3, 2, 1], $indices);
    }

    public function testKeyedCompleteReplacement(): void
    {
        $old = $this->makeList(['A', 'B', 'C'], keyed: true);
        $new = $this->makeList(['X', 'Y'], keyed: true);

        $ops = $this->diff($new, $old);

        // All old removed, all new inserted
        $removes = array_filter($ops, fn($o) => $o['type'] === 'remove_at');
        $inserts = array_filter($ops, fn($o) => $o['type'] === 'insert_at');
        $this->assertCount(3, $removes);
        $this->assertCount(2, $inserts);
    }

    public function testKeyedEmptyToPopulated(): void
    {
        $old = new TagDomNode('ul');
        $old->assignPaths([]);

        $new = $this->makeList(['A', 'B'], keyed: true);

        $ops = $this->diff($new, $old);

        // Falls back to positional since old has no keyed children
        $inserts = array_filter($ops, fn($o) => $o['type'] === 'insert_node');
        $this->assertCount(2, $inserts);
    }

    public function testKeyedPopulatedToEmpty(): void
    {
        $old = $this->makeList(['A', 'B', 'C'], keyed: true);

        $new = new TagDomNode('ul');
        $new->assignPaths([]);

        $ops = $this->diff($new, $old);

        // Falls back to positional since new has no keyed children
        $deletes = array_filter($ops, fn($o) => $o['type'] === 'delete_node');
        $this->assertCount(3, $deletes);
    }
}
