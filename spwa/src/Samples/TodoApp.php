<?php

namespace Spwa\Samples;

use Spwa\Events\InputEvent;
use Spwa\State\SessionStateManager;
use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\UIElement;
use Spwa\UI\Unit;
use Spwa\VNode\App;

class TodoApp extends App
{
    /** @var array<int, array{id: int, text: string, completed: bool}> */
    private array $todos = [];
    private int $nextId = 1;
    private string $filter = 'all';

    public function title(): string
    {
        return 'TodoMVC - SPWA';
    }

    public function states(): array
    {
        return [new SessionStateManager()];
    }

    protected function initialize(): void
    {
        $this->useState($this->todos);
        $this->useState($this->nextId);
        $this->useState($this->filter);
    }

    protected function view(): UIElement
    {
        $filtered = $this->getFilteredTodos();
        $activeCount = count(array_filter($this->todos, fn($t) => !$t['completed']));
        $completedCount = count($this->todos) - $activeCount;
        $allCompleted = count($this->todos) > 0 && $activeCount === 0;

        return UI::column()
            ->background(Color::hex('#f5f5f5'))
            ->content(
                // Title
                UI::text('todos')
                    ->center()
                    ->fontSize(FontSize::SixXL)
                    ->weight(FontWeight::Thin)
                    ->color(Color::hex('#b83f45'))
                    ->opacity(20)
                    ->paddingVertical(Unit::rem(1)),

                // Main card
                UI::column()
                    ->maxWidth(Unit::px(550))
                    ->width(Unit::full())
                    ->marginHorizontal(Unit::auto())
                    ->shadow(Shadow::Large)
                    ->content(
                        $this->buildInput($allCompleted),
                        ...(count($this->todos) > 0
                            ? [$this->buildList($filtered), $this->buildFooter($activeCount, $completedCount)]
                            : [])
                    ),

                // Info footer
                UI::column()
                    ->alignCenter()
                    ->padding(Unit::rem(1.5))
                    ->gap(Unit::rem(0.25))
                    ->content(
                        UI::text('Double-click to edit a todo')
                            ->fontSize(FontSize::Small)
                            ->color(Color::hex('#777')),
                        UI::text('Part of TodoMVC')
                            ->fontSize(FontSize::Small)
                            ->color(Color::hex('#777'))
                    )
            );
    }

    private function buildInput(bool $allCompleted): UIElement
    {
        $children = [];

        if (count($this->todos) > 0) {
            $children[] = UI::span()
                ->clickable()
                ->padding(Unit::rem(0.7))
                ->fontSize(FontSize::Large)
                ->color($allCompleted ? Color::hex('#484848') : Color::hex('#e6e6e6'))
                ->on('click', function () {
                    $allActive = count(array_filter($this->todos, fn($t) => !$t['completed'])) > 0;
                    foreach ($this->todos as &$todo) {
                        $todo['completed'] = $allActive;
                    }
                })
                ->content('❯');
        }

        $children[] = UI::input()
            ->text()
            ->placeholder('What needs to be done?')
            ->autofocus()
            ->grow()
            ->padding(Unit::rem(1))
            ->borderNone()
            ->outlineNone()
            ->fontSize(FontSize::TwoXL)
            ->weight(FontWeight::Light)
            ->color(Color::hex('#111'))
            ->on('change', function (InputEvent $e) {
                $text = trim($e->value ?? '');
                if ($text !== '') {
                    $this->todos[] = [
                        'id' => $this->nextId++,
                        'text' => $text,
                        'completed' => false,
                    ];
                }
            });

        return UI::row()
            ->alignMiddle()
            ->background(Color::white())
            ->content(...$children);
    }

    private function buildList(array $todos): UIElement
    {
        $items = [];
        foreach ($todos as $todo) {
            $items[] = new TodoItem(
                id: $todo['id'],
                text: $todo['text'],
                completed: $todo['completed'],
                onToggle: function (int $id) {
                    foreach ($this->todos as &$t) {
                        if ($t['id'] === $id) {
                            $t['completed'] = !$t['completed'];
                            break;
                        }
                    }
                },
                onDestroy: function (int $id) {
                    $this->todos = array_values(array_filter(
                        $this->todos,
                        fn($t) => $t['id'] !== $id
                    ));
                },
            );
        }

        return UI::column()->content(...$items);
    }

    private function buildFooter(int $activeCount, int $completedCount): UIElement
    {
        $itemsLabel = $activeCount === 1 ? '1 item left!' : $activeCount . ' items left!';

        return UI::row()
            ->alignMiddle()
            ->alignBetween()
            ->background(Color::white())
            ->paddingHorizontal(Unit::rem(1))
            ->paddingVertical(Unit::rem(0.6))
            ->borderTop()
            ->borderColor(Color::hex('#ededed'))
            ->fontSize(FontSize::Small)
            ->color(Color::hex('#777'))
            ->content(
                UI::text($itemsLabel)
                    ->minWidth(Unit::rem(8)),

                UI::row()
                    ->gap(Unit::rem(0.25))
                    ->content(
                        $this->buildFilter('All', 'all'),
                        $this->buildFilter('Active', 'active'),
                        $this->buildFilter('Completed', 'completed'),
                    ),

                $completedCount > 0
                    ? UI::button('Clear completed')
                        ->clickable()
                        ->borderNone()
                        ->background(Color::transparent())
                        ->fontSize(FontSize::Small)
                        ->color(Color::hex('#777'))
                        ->minWidth(Unit::rem(8))
                        ->on('click', function () {
                            $this->todos = array_values(array_filter(
                                $this->todos,
                                fn($t) => !$t['completed']
                            ));
                        })
                    : UI::span()->minWidth(Unit::rem(8))
            );
    }

    private function buildFilter(string $label, string $filter): UIElement
    {
        $isActive = $this->filter === $filter;

        $btn = UI::button($label)
            ->clickable()
            ->borderNone()
            ->background(Color::transparent())
            ->padding(Unit::rem(0.2))
            ->paddingHorizontal(Unit::rem(0.45))
            ->rounded(Unit::px(3))
            ->fontSize(FontSize::Small)
            ->color(Color::inherit())
            ->on('click', function () use ($filter) {
                $this->filter = $filter;
            });

        if ($isActive) {
            $btn = $btn
                ->bordered()
                ->borderColor(Color::hex('rgba(175, 47, 47, 0.2)'));
        }

        return $btn;
    }

    /**
     * @return array<int, array{id: int, text: string, completed: bool}>
     */
    private function getFilteredTodos(): array
    {
        return match ($this->filter) {
            'active' => array_values(array_filter($this->todos, fn($t) => !$t['completed'])),
            'completed' => array_values(array_filter($this->todos, fn($t) => $t['completed'])),
            default => $this->todos,
        };
    }
}
