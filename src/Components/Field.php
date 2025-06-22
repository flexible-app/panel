<?php

namespace FlexibleApp\Panel\Components;

abstract class Field extends Component
{
    public string $name;
    public string $label;
    public mixed $default = null;
    public array $rules = [];

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

    public function getValidationRule(): ?array
    {
        return $this->rules ? [$this->name => $this->rules] : null;
    }
}
