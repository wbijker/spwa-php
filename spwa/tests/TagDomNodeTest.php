<?php

namespace Spwa\Tests;

use PHPUnit\Framework\TestCase;
use Spwa\UI\TagDomNode;
use Spwa\UI\TextDomNode;
use Spwa\VNode\Patcher;

class TagDomNodeTest extends TestCase
{
    public function testBasicHtmlGeneration(): void
    {
        $node = new TagDomNode('div');
        $node->assignPaths([]);

        $this->assertEquals('<div data-path=""></div>', $node->toHtml());
    }

    public function testHtmlWithAttributes(): void
    {
        $node = new TagDomNode('input');
        $node->attr('type', 'text');
        $node->attr('placeholder', 'Enter...');
        $node->assignPaths([0]);

        $html = $node->toHtml();
        $this->assertStringContainsString('type="text"', $html);
        $this->assertStringContainsString('placeholder="Enter..."', $html);
    }

    public function testHtmlWithClasses(): void
    {
        $node = new TagDomNode('div');
        $node->class('foo', 'bar');
        $node->assignPaths([0]);

        $html = $node->toHtml();
        $this->assertStringContainsString('class="foo bar"', $html);
    }

    public function testStringChildrenWrappedInSpan(): void
    {
        $node = new TagDomNode('div');
        $node->content('hello', 'world');
        $node->assignPaths([]);

        $html = $node->toHtml();
        $this->assertStringContainsString('<span data-path="0">hello</span>', $html);
        $this->assertStringContainsString('<span data-path="1">world</span>', $html);
    }

    public function testEmptyStringChildRendersAsEmptySpan(): void
    {
        $node = new TagDomNode('div');
        $node->content('');
        $node->assignPaths([]);

        $html = $node->toHtml();
        $this->assertStringContainsString('<span data-path="0"></span>', $html);
    }

    public function testNestedChildren(): void
    {
        $parent = new TagDomNode('div');
        $child = new TagDomNode('span');
        $child->content('text');
        $parent->content($child);
        $parent->assignPaths([]);

        $html = $parent->toHtml();
        $this->assertStringContainsString('<span data-path="0">', $html);
        $this->assertStringContainsString('<span data-path="0,0">text</span>', $html);
    }
}
