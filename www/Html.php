<?php

namespace SPWA;

use HtmlTemplateNode;
use TemplateNode;

class AttrValue
{
    public string $name;
    /**
     * @var mixed
     */
    public $value;

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

}


/**
 * Attr class for dynamically creating HTML attribute objects.
 *
 * Utilizes __callStatic to allow flexible creation of HTML attributes without predefined methods.
 * Call static methods with names corresponding to HTML attributes, passing the attribute value as an argument.
 *
 * Global Attributes:
 * @method static AttrValue id(string $value)
 * @method static AttrValue class(string ...$value)
 * @method static AttrValue style(string $value)
 * @method static AttrValue title(string $value)
 * @method static AttrValue lang(string $value)
 * @method static AttrValue dir(string $value)
 * @method static AttrValue tabindex(string $value)
 * @method static AttrValue contenteditable(string $value)
 * @method static AttrValue hidden(string $value)
 *
 * Event Attributes:
 * @method static AttrValue onclick(callable $event)
 * @method static AttrValue onmouseover(callable $event)
 * @method static AttrValue onmouseout(callable $event)
 * @method static AttrValue onsubmit(callable $value)
 * @method static AttrValue onload(callable $value)
 * @method static AttrValue onchange(callable $value)
 * @method static AttrValue onkeydown(callable $value)
 * @method static AttrValue onkeyup(callable $value)
 *
 * Form Attributes:
 * @method static AttrValue action(string $value)
 * @method static AttrValue method(string $value)
 * @method static AttrValue name(string $value)
 * @method static AttrValue value(string $value)
 * @method static AttrValue placeholder(string $value)
 * @method static AttrValue required(string $value = "required")
 * @method static AttrValue readonly(string $value = "readonly")
 * @method static AttrValue disabled(string $value = "disabled")
 * @method static AttrValue autofocus(string $value = "autofocus")
 * @method static AttrValue checked(string $value = "checked")
 * @method static AttrValue selected(string $value = "selected")
 * @method static AttrValue multiple(string $value = "multiple")
 *
 * Image and Media Attributes:
 * @method static AttrValue src(string $value)
 * @method static AttrValue alt(string $value)
 * @method static AttrValue autoplay(string $value)
 * @method static AttrValue controls(string $value)
 * @method static AttrValue loop(string $value)
 * @method static AttrValue muted(string $value)
 * @method static AttrValue poster(string $value)
 *
 * Link and Area Attributes:
 * @method static AttrValue href(string $value)
 * @method static AttrValue target(string $value)
 * @method static AttrValue download(string $value)
 * @method static AttrValue hreflang(string $value)
 * @method static AttrValue rel(string $value)
 * @method static AttrValue type(string $value)
 *
 */
class Attr
{
    public static function __callStatic(string $name, array $arguments): AttrValue
    {
        $value = $arguments[0] ?? '';
        return new AttrValue($name, $value);
    }
}

/**
 * HTML helper class for dynamically creating HTML elements.
 *
 * @method static HtmlTemplateNode a(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode abbr(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode address(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode area(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode article(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode aside(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode audio(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode b(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode base(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode bdi(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode bdo(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode blockquote(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode body(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode br(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode button(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode canvas(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode caption(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode cite(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode code(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode col(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode colgroup(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode data(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode datalist(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode dd(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode del(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode details(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode dfn(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode dialog(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode div(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode dl(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode dt(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode em(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode embed(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode fieldset(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode figcaption(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode figure(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode footer(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode form(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode h1(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode h2(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode h3(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode h4(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode h5(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode h6(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode head(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode header(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode hgroup(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode hr(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode html(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode i(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode iframe(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode img(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode input(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode ins(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode kbd(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode label(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode legend(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode li(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode link(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode main(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode map(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode mark(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode meta(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode meter(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode nav(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode noscript(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode object(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode ol(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode optgroup(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode option(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode output(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode p(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode param(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode picture(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode pre(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode progress(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode q(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode rp(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode rt(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode ruby(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode s(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode samp(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode script(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode section(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode select(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode small(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode source(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode span(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode strong(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode style(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode sub(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode summary(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode sup(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode svg(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode table(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode tbody(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode td(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode template(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode textarea(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode tfoot(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode th(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode thead(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode time(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode title(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode tr(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode track(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode u(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode ul(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode var(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode video(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 * @method static HtmlTemplateNode wbr(AttrValue[]|TemplateNode|string|string[] $attrs, TemplateNode|string|string[] $content = null)
 */
class Tag
{

    /**
     * Magic method to handle static method calls for creating HTML elements.
     *
     * @param string $name The name of the method being called, which corresponds to the HTML element.
     * @param array $arguments An array of arguments passed to the method. Expected to contain:
     *                         - $attrs (array): Attributes for the HTML element.
     *                         - $content (array): Content or children of the HTML element.
     * @return HtmlTemplateNode Returns an instance of HtmlTemplateNode for the requested element.
     */
    public static function __callStatic(string $name, array $arguments): HtmlTemplateNode
    {
        // Expecting two arguments: $attrs and $content
        [$attrs, $content] = $arguments;

        // Create a new HtmlTemplateNode object for the given element
        return new HtmlTemplateNode($name, $attrs, $content);
    }
}


