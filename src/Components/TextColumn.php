<?php

namespace FlexibleApp\Panel\Components;

use Illuminate\Support\Str;

class TextColumn
{
    public string $type = 'text';
    public string $key;
    public ?string $label = null;
    public ?string $align = null;
    public bool $bold = false;

    protected $formatter = null;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->label = Str::of($key)->snake()->replace('_', ' ')->title(); 
    }

    public static function make(string $key): static
    {
        return new static($key);
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function align(string $align): static
    {
        $this->align = $align;
        return $this;
    }

    public function bold(): static
    {
        $this->bold = true;
        return $this;
    }

    public function format(callable $callback): static
    {
        $this->formatter = $callback;
        return $this;
    }

    public function getFormattedValue($row): string
    {
        $value = $row[$this->key] ?? '';

        return $this->formatter
            ? call_user_func($this->formatter, $value, $row)
            : $value;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'key' => $this->key,
            'label' => $this->label,
            'align' => $this->align,
            'bold' => $this->bold,
        ];
    }
}
