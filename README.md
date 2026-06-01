# BrickPHP

> Server-powered web applications, built brick by brick — entirely in PHP.

BrickPHP is a server-driven UI framework for PHP. Routing, state, components,
events, styling, DOM diffing, and CSS extraction all live in one PHP process
— no JavaScript framework, no build pipeline, no Node.

```bash
composer require brickphp/brickphp
```

## Hello, BrickPHP

```php
<?php

use BrickPHP\Brick;
use App\App;

require 'vendor/autoload.php';

Brick::run(App::class);
```

```php
namespace App;

use BrickPHP\State\SessionStateManager;
use BrickPHP\State\StateManager;
use BrickPHP\UI\UI;
use BrickPHP\VNode\App as BrickApp;
use BrickPHP\VNode\Component;
use BrickPHP\VNode\VNode;

class App extends BrickApp
{
    public function title(): string { return 'Counter'; }

    public function state(): StateManager
    {
        return new SessionStateManager();
    }

    protected function view(): VNode
    {
        return new Counter();
    }
}

class Counter extends Component
{
    private int $count = 0;

    protected function initialize(): void
    {
        $this->useState($this->count);
    }

    protected function build(): VNode
    {
        return UI::column()->content(
            UI::text("Count: {$this->count}"),
            UI::button('+')->onClick(fn() => $this->count++),
        );
    }
}
```

## Highlights

- **All in one place** — routing, state, components, styling all in PHP.
- **No glue** — your UI talks to your data directly; no API layer, no serialization.
- **Hot module reloading** — save a file, watch the browser update without losing state.
- **Utility CSS out of the box** — semantic methods like `padding(Unit::large())` generate CSS at build time.
- **Wireframe inspector** — overlay the rendered tree with source locations.
- **Debugging made easy** — Xdebug works out of the box; DOM patches surface in the console.
- **UI elements, not JS + CSS** — typed `UIElement` primitives, no className strings, no JSX, no template DSL.

## Requirements

- PHP 8.1+

## Samples & docs

See [github.com/wbijker/brickphp-samples](https://github.com/wbijker/brickphp-samples)
for the documentation site, runnable sample apps (counter, news, todo, docs),
and the Docker dev stack.

## License

[MIT](LICENSE) © Willem Bijker
