<?php

namespace Spwa\Debug;

use Spwa\Js\Console;
use Spwa\State\StateManager;
use Spwa\UI\DomNode;

class DebugPanel
{
    private int $nodeCount;

    /** @var array{name: string, bytes: int, all: array<string, array>}[] */
    private array $stateInfo;

    /**
     * @param StateManager[] $states
     */
    public function __construct(DomNode $renderedUi, array $states)
    {
        $this->nodeCount = $renderedUi->countNodes();
        $this->stateInfo = array_map(fn(StateManager $s) => [
            'name' => $s->name(),
            'bytes' => $s->bytes(),
            'all' => $s->getAll(),
        ], $states);
    }

    public function emit(): void
    {
        Console::clear();
        Console::group('%cSPWA Debug', 'color:#facc15;font-weight:bold;font-size:11px');

        Console::log(
            '%cNodes%c ' . $this->nodeCount,
            'color:#94a3b8',
            'color:#e2e8f0;font-weight:600',
        );

        foreach ($this->stateInfo as $info) {
            Console::log(
                '%c' . $info['name'] . '%c ' . self::formatBytes($info['bytes']),
                'color:#94a3b8',
                'color:#e2e8f0;font-weight:600',
            );

            if (!empty($info['all'])) {
                foreach ($info['all'] as $pathKey => $stateData) {
                    $parts = explode(':', $pathKey);
                    $className = array_pop($parts);
                    $shortName = self::shortClassName($className);
                    $path = $parts[0] ?? '';

                    Console::group(
                        '%c' . $shortName . '%c [' . $path . ']',
                        'color:#67e8f9;font-weight:600',
                        'color:#64748b;font-weight:400',
                    );

                    Console::dir($stateData);

                    Console::groupEnd();
                }
            }
        }

        Console::groupEnd();
    }

    private static function shortClassName(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);
        return end($parts);
    }

    private static function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}
