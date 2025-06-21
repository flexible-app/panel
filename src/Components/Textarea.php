<?php

namespace FlexibleApp\Panel\Components;

class Textarea extends Component
{
    public string $label;
    public string $name;
    public ?string $placeholder = null;
    public int $rows = 4;

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

    public function rows(int $rows): static
    {
        $this->rows = $rows;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => 'Textarea',
            'name' => $this->name,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'rows' => $this->rows,
        ];
    }
}
