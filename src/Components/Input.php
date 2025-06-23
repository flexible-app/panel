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
        return array_merge(parent::toArray(), [
            'placeholder' => $this->placeholder,
        ]);
    }
}
