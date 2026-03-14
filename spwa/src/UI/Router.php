<?php

namespace Spwa\UI;

use Spwa\Js\History;
use Spwa\VNode\Component;
use Spwa\VNode\RenderPhase;
use Spwa\VNode\VNode;
use Spwa\State\StateManager;

class Router extends Component
{
    private static ?string $navigateUri = null;

    /** @var array<int, array{path: string, content: VNode|string}> */
    private array $routes = [];
    private ?VNode $fallback = null;
    private string $uri = '/';

    public function route(string $path, VNode|string $content): static
    {
        $this->routes[] = ['path' => $path, 'content' => $content];
        return $this;
    }

    public function fallback(VNode|string $content): static
    {
        if (is_string($content)) {
            $this->fallback = UI::text($content);
        } else {
            $this->fallback = $content;
        }
        return $this;
    }

    public static function navigate(string $uri): void
    {
        self::$navigateUri = $uri;
        History::pushState(null, '', $uri);
    }

    protected function initialize(): void
    {
        $this->useState($this->uri);
    }

    protected function restored(): void
    {
        if (self::$navigateUri !== null) {
            $this->uri = self::$navigateUri;
            self::$navigateUri = null;
        }
    }

    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        if ($phase === RenderPhase::Initial) {
            $this->uri = getallheaders()['Url'] ?? $_SERVER['REQUEST_URI'] ?? '/';
        }

        return parent::render($state, $parent, $phase);
    }

    protected function build(): VNode
    {
        $currentPath = parse_url($this->uri, PHP_URL_PATH) ?? '/';

        foreach ($this->routes as $route) {
            if ($route['path'] === $currentPath) {
                $content = $route['content'];
                if (is_string($content)) {
                    return UI::text($content);
                }
                return $content;
            }
        }

        return $this->fallback ?? UI::text('404 Not Found');
    }
}
