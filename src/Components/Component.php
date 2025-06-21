<?php

namespace FlexibleApp\Panel\Components;

abstract class Component
{
    public bool $visible = true;

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function visible(bool $condition = true): static
    {
        $this->visible = $condition;
        return $this;
    }

    public function when(bool $condition): static
    {
        return $this->visible($condition);
    }

    abstract public function toArray(): array;
}
