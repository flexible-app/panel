<?php

namespace FlexibleApp\Panel\Components;

class Input extends Field
{
    public ?string $placeholder = null;

    public function placeholder(string $text): static
    {
        $this->placeholder = $text;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => 'Input',
            'name' => $this->name,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'default' => $this->default,
            'rules' => $this->rules,
            'visible' => $this->visible,
        ];
    }
}
