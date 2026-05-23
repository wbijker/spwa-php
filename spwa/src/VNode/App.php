<?php

namespace Spwa\VNode;

use Spwa\Error\DefaultErrorPage;
use Spwa\Error\ErrorInfo;
use Spwa\State\StateManager;
use Spwa\UI\UIElement;

abstract class App extends Component
{
    /** @var string[] Custom JS snippets registered by components */
    private array $customJs = [];

    /** @var string[] Custom CSS snippets registered by components */
    private array $customCss = [];

    abstract public function title(): string;

    abstract protected function view(): VNode;

    /**
     * Override to register custom JS/CSS assets on this App.
     *
     * Called once by the framework on the initial GET, before the first
     * render. The strings collected via $app->addJs() / $app->addCss() are
     * inlined into <head> as a single <script> and <style> block.
     *
     *   protected function registerAssets(App $app): void {
     *       $app->addCss('.brand { color: tomato }');
     *       $app->addJs('window.MyApp = { ready: true };');
     *   }
     *
     * Not called on POST event responses — assets persist from the initial
     * page load, so register everything you might need up front.
     */
    protected function registerAssets(App $app): void
    {
    }

    /**
     * Framework entry point — runs the registerAssets() hook with this App
     * as both the receiver and the argument. Called by Spwa::run() on the
     * initial GET before rendering. Not part of the public override surface
     * — subclasses override registerAssets() instead.
     */
    public function runRegisterAssets(): void
    {
        $this->registerAssets($this);
    }

    /**
     * Optional overlay element shown while a server request is in flight.
     *
     * Rendered once on the initial page load as a sibling of the main app
     * root, hidden with `display: none`. The frontend toggles it to
     * `display: block` when a request is pending and back to `display: none`
     * once the response has been applied. Return `null` to disable.
     */
    protected function loader(): ?VNode
    {
        return null;
    }

    /**
     * Framework runtime accessor for the loader hook.
     */
    public function getLoader(): ?VNode
    {
        return $this->loader();
    }

    /**
     * Return the state manager used by this app.
     */
    abstract public function state(): StateManager;

    /**
     * Add a custom JavaScript snippet.
     */
    public function addJs(string $js): void
    {
        $this->customJs[] = $js;
    }

    /**
     * Add a custom CSS snippet.
     */
    public function addCss(string $css): void
    {
        $this->customCss[] = $css;
    }

    /**
     * Get all registered custom JS snippets.
     * @return string[]
     */
    public function getCustomJs(): array
    {
        return $this->customJs;
    }

    /**
     * Get all registered custom CSS snippets.
     * @return string[]
     */
    public function getCustomCss(): array
    {
        return $this->customCss;
    }

    protected function build(): VNode
    {
        return $this->view();
    }

    /**
     * Render an error page when an unrecoverable error occurs during a
     * request. Override to customize the look or to log/notify before
     * returning a view. The default returns a centered modern card with
     * the type, message, location, and (if available) stack trace.
     */
    public function error(ErrorInfo $info): VNode
    {
        return new DefaultErrorPage($info);
    }
}
