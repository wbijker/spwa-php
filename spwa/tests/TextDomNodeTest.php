<?php

namespace Spwa\Tests;

use PHPUnit\Framework\TestCase;
use Spwa\UI\TextDomNode;
use Spwa\UI\TagDomNode;
use Spwa\VNode\Patcher;

class TextDomNodeTest extends TestCase
{
    public function testRendersAsSpan(): void
    {
        $node = new TextDomNode('hello');
        $node->assignPaths([0]);

        $this->assertEquals('<span data-path="0">hello</span>', $node->toHtml());
    }

    public function testRendersEmptyStringAsEmptySpan(): void
    {
        $node = new TextDomNode('');
        $node->assignPaths([1, 2]);

        $this->assertEquals('<span data-path="1,2"></span>', $node->toHtml());
    }

    public function testEscapesHtmlEntities(): void
    {
        $node = new TextDomNode('<script>alert("xss")</script>');
        $node->assignPaths([0]);

        $this->assertStringContainsString('&lt;script&gt;', $node->toHtml());
    }

    public function testCompareIdenticalText(): void
    {
        $a = new TextDomNode('hello');
        $a->assignPaths([0]);
        $b = new TextDomNode('hello');
        $b->assignPaths([0]);

        $patcher = new Patcher();
        $a->compare($b, $patcher);

        $this->assertEmpty($patcher->getOperations());
    }

    public function testCompareDifferentText(): void
    {
        $new = new TextDomNode('world');
        $new->assignPaths([0]);
        $old = new TextDomNode('hello');
        $old->assignPaths([0]);

        $patcher = new Patcher();
        $new->compare($old, $patcher);

        $ops = $patcher->getOperations();
        $this->assertCount(1, $ops);
        $this->assertEquals('replace_text', $ops[0]['type']);
        $this->assertEquals('world', $ops[0]['text']);
    }

    public function testCompareTextVsTag(): void
    {
        $text = new TextDomNode('hello');
        $text->assignPaths([0]);
        $tag = new TagDomNode('div');
        $tag->assignPaths([0]);

        $patcher = new Patcher();
        $text->compare($tag, $patcher);

        $ops = $patcher->getOperations();
        $this->assertCount(1, $ops);
        $this->assertEquals('replace_node', $ops[0]['type']);
    }
}
