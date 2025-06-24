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

    public function registerRoutes(string $slug, Panel $panel): void
    {
        $this->registerSchemaRoute($slug, $panel);
        $this->registerSubmitRoute($slug, $panel);
    }

    public function registerSubmitRoute(string $slug, Panel $panel): void
    {
        if (!$this->shouldRegisterSubmitRoute || !$this->name) {
            return;
        }

        Route::post("$slug/forms/{$this->name}", function () {
            $entry = static::$handlers[$this->name] ?? null;

            if (!$entry || !isset($entry['form'], $entry['callback'])) {
                abort(404, 'Form handler not found.');
            }

            /** @var Form $form */
            $form = $entry['form'];
            $callback = $entry['callback'];

            // Build the data array with request values or defaults
            $data = [];
            foreach ($form->schema as $field) {
                if ($field instanceof Field) {
                    $data[$field->name] = request()->input($field->name, $field->default);
                }
            }

            // Build validation rules
            $rules = [];
            foreach ($form->schema as $field) {
                if ($field instanceof Field && !empty($field->rules)) {
                    $rules[$field->name] = $field->rules;
                }
            }

            // Validate merged $data (request + defaults)
            validator($data, $rules)->validate();

            // Pass full $data to the callback
            return call_user_func($callback, $data);
        })->name("panel.form.submit.{$this->name}");
    }

    public function registerSchemaRoute(string $slug, Panel $panel, ): void
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

            // Build the data array with request values or defaults
            $data = [];
            foreach ($form->schema as $field) {
                if ($field instanceof Field) {
                    $data[$field->name] = request()->input($field->name, $field->default);
                }
            }

            $schema = array_map(
                fn($component) => $component instanceof Field
                    ? $component->toArrayWithData($data)
                    : $component->toArray(),
                $form->schema
            );

            return back()->with('schema', $schema);
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

    public static function fillByName(string $name, array $data = [])
    {
        return [
            'fillByName' => $name
        ];
    }

    public function toArray(): array
    {
        // Prefill data with request or default
        $data = [];
        foreach ($this->schema as $field) {
            if ($field instanceof Field) {
                $data[$field->name] = request()->input($field->name, $field->default);
            }
        }

        return [
            'type' => 'Form',
            'method' => $this->method,
            'action' => $this->action,
            'schema' => array_map(
                fn($component) => $component instanceof Field
                    ? $component->toArrayWithData($data)
                    : $component->toArray(),
                $this->schema
            ),
            'visible' => $this->visible,
        ];
    }

    // public function toArray(): array
    // {
    //     return [
    //         'type' => 'Form',
    //         'method' => $this->method,
    //         'action' => $this->action,
    //         'schema' => array_map(fn($component) => $component->toArray(), $this->schema),
    //         'visible' => $this->visible,
    //     ];
    // }
}
