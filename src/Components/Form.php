<?php

namespace FlexibleApp\Panel\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use FlexibleApp\Panel\Components\Field;
use FlexibleApp\Panel\Panel;
use Closure;



class Form extends Component
{
    public string $method = 'POST';
    public ?string $action = null;
    public array $schema = [];
    public bool $shouldRegisterSubmitRoute = false;

    protected $panel = null;
    protected ?Closure $submitHandler = null;
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
        static::$handlers[$this->name] = $callback;

        $this->action = $this->name;

        return $this;
    }

    public function registerSubmitRoute(Panel $panel, string $slug): void
    {
        if (!$this->shouldRegisterSubmitRoute || !$this->name) {
            return;
        }

        // Store both form and handler
        static::$handlers[$this->name] = [
            'form' => $this,
            'callback' => $this->submitHandler,
        ];

        Route::post("$slug/forms/{name}", function ($name) {
            $entry = static::$handlers[$name] ?? null;

            if (!$entry || !isset($entry['form'], $entry['callback'])) {
                abort(404, 'Form handler not found.');
            }

            /** @var \FlexibleApp\Panel\Components\Form $form */
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

            // Call form handler with validated data
            return call_user_func($callback, $validated);
        })->name("panel.form.submit.{$this->name}");
    }

    // public function registerSubmitRoute(Panel $panel, $slug): void
    // {
    //     if (!$this->shouldRegisterSubmitRoute || !$this->name) return;

    //     Route::post("$slug/forms/{name}", function ($name) {
    //         $handler = static::$handlers[$name] ?? null;

    //         if ($handler) {
    //             return call_user_func($handler, request()->all());
    //         }

    //         abort(404, 'Form handler not found.');
    //     })->name("panel.form.submit.{$this->name}");
    // }

    public function toArray(): array
    {
        return [
            'type' => 'Form',
            'method' => $this->method,
            'action' => $this->action,
            'schema' => array_map(fn ($component) => $component->toArray(), $this->schema),
            'visible' => $this->visible,
        ];
    }
}
