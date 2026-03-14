<?php

namespace Samples\Site\Pages;

use Samples\Site\Components\CodeBlock;
use Samples\Site\Components\DemoCard;
use Samples\Site\Components\SectionHeading;
use Spwa\Events\InputEvent;
use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

class FormsPage extends Component
{
    private string $name = '';
    private string $email = '';
    private string $role = 'developer';
    private bool $newsletter = false;
    private bool $submitted = false;

    protected function initialize(): void
    {
        $this->useState($this->name);
        $this->useState($this->email);
        $this->useState($this->role);
        $this->useState($this->newsletter);
        $this->useState($this->submitted);
    }

    protected function build(): VNode
    {
        return UI::column()
            ->maxWidth(Unit::px(900))
            ->marginHorizontal(Unit::auto())
            ->padding(Unit::extraLarge())
            ->gap(Unit::extraLarge())
            ->content(
                new SectionHeading(
                    'Forms & Inputs',
                    'Server-side form handling with real-time validation — no JavaScript.'
                ),
                $this->liveFormDemo(),
                $this->inputTypesDemo(),
                $this->validationDemo(),
            );
    }

    private function liveFormDemo(): VNode
    {
        return new DemoCard(
            title: 'Live Form (Interactive)',
            description: 'Fill in the form — every change is sent to PHP for processing.',
            demo: UI::column()->gap(Unit::medium())->content(
                // Name field
                UI::column()->gap(Unit::xs())->content(
                    UI::label('Full Name')
                        ->fontSize(FontSize::Small)
                        ->weight(FontWeight::Medium)
                        ->color(Color::slate(700)),
                    UI::input()
                        ->text()
                        ->placeholder('Enter your name')
                        ->value($this->name)
                        ->extendHorizontal()
                        ->padding(Unit::small())
                        ->bordered()
                        ->borderColor(Color::slate(300))
                        ->rounded(Unit::roundedLg())
                        ->on('change', fn(InputEvent $e) => $this->name = $e->value ?? ''),
                ),
                // Email field
                UI::column()->gap(Unit::xs())->content(
                    UI::label('Email')
                        ->fontSize(FontSize::Small)
                        ->weight(FontWeight::Medium)
                        ->color(Color::slate(700)),
                    UI::input()
                        ->email()
                        ->placeholder('you@example.com')
                        ->value($this->email)
                        ->extendHorizontal()
                        ->padding(Unit::small())
                        ->bordered()
                        ->borderColor(Color::slate(300))
                        ->rounded(Unit::roundedLg())
                        ->on('change', fn(InputEvent $e) => $this->email = $e->value ?? ''),
                ),
                // Role select
                UI::column()->gap(Unit::xs())->content(
                    UI::label('Role')
                        ->fontSize(FontSize::Small)
                        ->weight(FontWeight::Medium)
                        ->color(Color::slate(700)),
                    UI::select()
                        ->extendHorizontal()
                        ->padding(Unit::small())
                        ->bordered()
                        ->borderColor(Color::slate(300))
                        ->rounded(Unit::roundedLg())
                        ->on('change', fn(InputEvent $e) => $this->role = $e->value ?? 'developer')
                        ->content(
                            UI::option('Developer', 'developer'),
                            UI::option('Designer', 'designer'),
                            UI::option('Product Manager', 'pm'),
                            UI::option('Engineering Manager', 'em'),
                        ),
                ),
                // Submit
                UI::row()->gap(Unit::medium())->alignMiddle()->content(
                    UI::button('Submit')
                        ->primary()
                        ->on('click', fn() => $this->submitted = $this->name !== '' && $this->email !== ''),
                    UI::button('Reset')
                        ->ghost()
                        ->on('click', function () {
                            $this->name = '';
                            $this->email = '';
                            $this->role = 'developer';
                            $this->newsletter = false;
                            $this->submitted = false;
                        }),
                ),
                // Result
                ...($this->submitted
                    ? [UI::column()
                        ->background(Color::emerald(50))
                        ->padding(Unit::medium())
                        ->rounded(Unit::roundedLg())
                        ->bordered()
                        ->borderColor(Color::emerald(200))
                        ->gap(Unit::xs())
                        ->content(
                            UI::text('Submitted (server-side):')
                                ->weight(FontWeight::SemiBold)
                                ->color(Color::emerald(700))
                                ->fontSize(FontSize::Small),
                            UI::text("Name: {$this->name}")
                                ->mono()->fontSize(FontSize::Small)->color(Color::slate(600)),
                            UI::text("Email: {$this->email}")
                                ->mono()->fontSize(FontSize::Small)->color(Color::slate(600)),
                            UI::text("Role: {$this->role}")
                                ->mono()->fontSize(FontSize::Small)->color(Color::slate(600)),
                        )]
                    : []),
            ),
            code: <<<'PHP'
private string $name = '';
private string $email = '';
private string $role = 'developer';

protected function initialize(): void
{
    $this->useState($this->name);
    $this->useState($this->email);
    $this->useState($this->role);
}

protected function build(): VNode
{
    return UI::column()->gap(Unit::medium())->content(
        UI::label('Full Name'),
        UI::input()
            ->text()
            ->placeholder('Enter your name')
            ->value($this->name)
            ->on('change', fn(InputEvent $e) => $this->name = $e->value ?? ''),

        UI::label('Role'),
        UI::select()
            ->on('change', fn(InputEvent $e) => $this->role = $e->value ?? '')
            ->content(
                UI::option('Developer', 'developer'),
                UI::option('Designer', 'designer'),
            ),

        UI::button('Submit')
            ->primary()
            ->on('click', fn() => $this->handleSubmit()),
    );
}
PHP
        );
    }

    private function inputTypesDemo(): VNode
    {
        return new DemoCard(
            title: 'Input Types',
            description: 'All standard HTML input types, via semantic PHP methods.',
            demo: UI::grid(2)->gap(Unit::medium())->content(
                $this->inputDemo('Text', UI::input()->text()->placeholder('Text input')),
                $this->inputDemo('Email', UI::input()->email()->placeholder('email@example.com')),
                $this->inputDemo('Password', UI::input()->password()->placeholder('••••••••')),
                $this->inputDemo('Number', UI::input()->number()->placeholder('42')),
                $this->inputDemo('Date', UI::input()->date()),
                $this->inputDemo('Search', UI::input()->search()->placeholder('Search...')),
                $this->inputDemo('URL', UI::input()->url()->placeholder('https://...')),
                $this->inputDemo('Range', UI::input()->range()->min(0)->max(100)),
            ),
            code: <<<'PHP'
UI::input()->text()->placeholder('...')
UI::input()->email()
UI::input()->password()
UI::input()->number()->min(0)->max(100)
UI::input()->date()
UI::input()->search()
UI::input()->range()->min(0)->max(100)
UI::input()->checkbox()->checked()
UI::input()->radio()->name('group')
UI::input()->file()
UI::input()->colorInput()
UI::textarea()->placeholder('Long text...')
PHP
        );
    }

    private function validationDemo(): VNode
    {
        return new DemoCard(
            title: 'Server-Side Validation',
            description: 'Validation logic runs in PHP — no client-side JavaScript validation needed.',
            demo: UI::text('All validation happens on the server. When you submit the form above with empty fields, PHP checks and returns errors. No duplicated validation logic.')
                ->color(Color::slate(500))
                ->fontSize(FontSize::Small),
            code: <<<'PHP'
// Validation runs on the server, in PHP
private function handleSubmit(): void
{
    $errors = [];

    if (empty($this->name)) {
        $errors[] = 'Name is required';
    }

    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    }

    if (strlen($this->name) < 2) {
        $errors[] = 'Name must be at least 2 characters';
    }

    if (empty($errors)) {
        // Save to database, send email, etc.
        $db->insert('users', [
            'name' => $this->name,
            'email' => $this->email,
        ]);
        $this->submitted = true;
    }

    $this->errors = $errors;
}
PHP
        );
    }

    private function inputDemo(string $label, VNode $input): VNode
    {
        return UI::column()->gap(Unit::xs())->content(
            UI::text($label)
                ->fontSize(FontSize::ExtraSmall)
                ->weight(FontWeight::Medium)
                ->color(Color::slate(500)),
            $input
                ->extendHorizontal()
                ->padding(Unit::small())
                ->bordered()
                ->borderColor(Color::slate(300))
                ->rounded(Unit::roundedLg()),
        );
    }
}
