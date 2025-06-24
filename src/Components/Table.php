<?php

namespace FlexibleApp\Panel\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Closure;

class Table extends Component
{
    public array $columns = [];
    public array $rows = [];
    public ?array $pagination = null;
    public array $actions = [];
    public array $rowsActions = [];

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function columns(array $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function rows(array|LengthAwarePaginator $rows): static
    {
        if ($rows instanceof LengthAwarePaginator) {
            $this->rows = $rows->items();
            $this->pagination = [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ];
        } else {
            $this->rows = $rows;
            $this->pagination = null;
        }

        return $this;
    }

    public function actions(array $actions): static
    {
        foreach ($actions as $key => $value) {
            $this->actions[$key] = $value instanceof Closure
                ? ['type' => 'server', 'handler' => $value]
                : ['type' => 'client', 'payload' => $value];
        }
        return $this;
    }

    public function rowActions(array $rowActions): static
    {
        foreach ($rowActions as $key => $value) {
            $this->rowActions[$key] = $value instanceof Closure
                ? ['type' => 'server', 'handler' => $value]
                : ['type' => 'client', 'payload' => $value];
        }
        return $this;
    }

    public function toArray(): array
    {
        // dd($this->actions);

        return [
            'type' => 'Table',
            // 'columns' => $this->columns,
            'columns' => array_map(fn($col) => $col instanceof Component ? $col->toArray() : $col, $this->columns),
            'rows' => $this->rows,
            'visible' => $this->visible,
            'pagination' => $this->pagination,
            // 'actions' => $this->actions,
            'actions' => collect($this->actions)
                ->map(fn($action, $key) => [
                    'key' => $key, 
                    'type' => $action['type'],
                    'payload' => $action['type'] == 'client' ? $action['payload'] : null,
                ])
                ->values()
                ->all(),
        ];
    }
}
