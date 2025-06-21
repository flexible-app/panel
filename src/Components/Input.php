<?php

namespace FlexibleApp\Panel\Components;

class Input extends Component
{
    public string $label;
    public string $name;
    public ?string $placeholder = null;
    public bool $required = false;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->label = ucfirst(str_replace('_', ' ', $name));
    }

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function placeholder(string $text): static
    {
        $this->placeholder = $text;
        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => 'Input',
            'name' => $this->name,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'required' => $this->required,
        ];
    }
}
