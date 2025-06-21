<?php

namespace FlexibleApp\Panel\Components;

class Text extends Component
{
    public string $label;
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
        $this->label = '';
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

    public function toArray(): array
    {
        return [
            'type' => 'Text',
            'label' => $this->label,
            'value' => $this->value,
        ];
    }
}
