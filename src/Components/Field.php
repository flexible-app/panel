<?php

namespace FlexibleApp\Panel\Components;

abstract class Field extends Component
{
    public string $name;
    public string $label;
    public mixed $default = null;
    public array $rules = [];
    public bool $reactive = false;
    public $visibleCondition = null;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->label = ucfirst(str_replace('_', ' ', $name));
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function default(mixed $default): static
    {
        $this->default = $default;
        return $this;
    }

    public function rules(array $rules): static
    {
        $this->rules = $rules;
        return $this;
    }

    public function reactive(bool $state = true): static
    {
        $this->reactive = $state;
        return $this;
    }

    // ✅ This is the correct new method name
    public function visibleWhen($condition): static
    {
        $this->visibleCondition = $condition;
        return $this;
    }

    public function isVisible(array $data = []): bool
    {
        if ($this->visibleCondition) {
            return (bool) call_user_func($this->visibleCondition, $data);
        }

        return $this->visible;
    }

    public function toArray(): array
    {
        return [
            'type' => class_basename(static::class),
            'name' => $this->name,
            'label' => $this->label,
            'default' => $this->default,
            'rules' => $this->rules,
            'reactive' => $this->reactive,
            'visible' => $this->isVisible(request()->all()),
        ];
    }
}
