<?php

namespace FlexibleApp\Panel\Components;

use Closure;
use Illuminate\Support\Facades\Route;
use FlexibleApp\Panel\Panel;

class Form extends Component
{
    public string $method = 'POST';
    public ?string $action = null;
    public array $schema = [];
    public bool $shouldRegisterSubmitRoute = false;

    protected ?Closure $submitHandler = null;

    // Stores [formName => ['form' => Form, 'callback' => Closure]]
    public static array $handlers = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function schema(array $schema): static
    {
        $this->schema = $schema;
        return $this;
    }

    public function onSubmit(Closure $callback): static
    {
        $this->submitHandler = $callback;
        $this->shouldRegisterSubmitRoute = true;
        $this->action = $this->name;

        // Store form and handler together
        static::$handlers[$this->name] = [
            'form' => $this,
            'callback' => $callback,
        ];

        return $this;
    }

    public function registerSubmitRoute(Panel $panel, string $slug): void
    {
        if (!$this->shouldRegisterSubmitRoute || !$this->name) {
            return;
        }

        Route::post("$slug/forms/{name}", function ($name) {
            $entry = static::$handlers[$name] ?? null;

            if (!$entry || !isset($entry['form'], $entry['callback'])) {
                abort(404, 'Form handler not found.');
            }

            /** @var Form $form */
            $form = $entry['form'];
            $callback = $entry['callback'];

            // Build validation rules
            $rules = [];
            foreach ($form->schema as $field) {
                if ($field instanceof Field && !empty($field->rules)) {
                    $rules[$field->name] = $field->rules;
                }
            }

            // Validate request
            $validated = request()->validate($rules);

            // Merge with all other request data (even those without rules)
            $data = array_merge($validated, request()->only(
                collect($form->schema)
                    ->filter(fn ($field) => $field instanceof Field)
                    ->map(fn ($field) => $field->name)
                    ->toArray()
            ));

            return call_user_func($callback, $data);
        })->name("panel.form.submit.{$this->name}");
    }

    public function registerSchemaRoute(Panel $panel, string $slug): void
    {
        if (!$this->shouldRegisterSubmitRoute || !$this->name) {
            return;
        }

        Route::post("$slug/forms/{$this->name}/schema", function () {
            $entry = static::$handlers[$this->name] ?? null;

            if (!$entry || !isset($entry['form'])) {
                abort(404, 'Form handler not found.');
            }

            /** @var Form $form */
            $form = $entry['form'];
            $form->fill(request()->all());

            return back()->with('schema', array_map(fn($component) => $component->toArray(), $form->schema));

            return [
                'schema' => array_map(fn($component) => $component->toArray(), $form->schema),
            ];
        })->name("panel.form.schema.{$this->name}");
    }

    public function fill(array $data): void
    {
        foreach ($this->schema as $field) {
            if ($field instanceof Field && array_key_exists($field->name, $data)) {
                $field->default($data[$field->name]);
            }
        }
    }

    public function toArray(): array
    {
        return [
            'type' => 'Form',
            'method' => $this->method,
            'action' => $this->action,
            'schema' => array_map(fn($component) => $component->toArray(), $this->schema),
            'visible' => $this->visible,
        ];
    }
}
