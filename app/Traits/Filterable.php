<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    protected $filterOps;
    protected $op;
    protected $query;

    public function scopeFilter(Builder $query): Builder
    {
        if (!$this->filterColumns && method_exists($this, 'getFilterColumns')) {
            $this->filterColumns = $this->getFilterColumns();
        }

        if (!$this->filterColumns)
            return $query;

        $this->filterOps = getFiltersFromRequest(key: 'filterOps');

        return $query->when($filters = getFiltersFromRequest(), function ($query) use ($filters) {
            foreach ($filters as $key => $value) {
                if (!$this->isColumnFilterable($key) || $value == "" || $value == "null") {
                    continue;
                }

                $this->query = $query;
                $this->op = $this->getFilterOp($key);
                $value = $this->op == 'like' && !is_array($value) ? "%$value%" : $value;
                $filterColumn = $this->getFilterColumn($key);

                if ($this->isDateDataType($key) && !isDateFromFormat($value)) {
                    continue;
                }

                if ($this->isBetweenFilter($value)) {
                    $this->applyBetweenFilter($filterColumn, $value);

                } elseif ($this->filterOpIs('in')) {
                    $this->applyInFilter($filterColumn, $value);

                } elseif ($this->isColumnRelation($filterColumn)) {
                    $this->applyRelationFilter($filterColumn, $value);

                } else {
                    $this->applyRegularFilter($filterColumn, $value, $key);
                }
            }
        });
    }

    protected function isColumnFilterable($column): bool
    {
        return isset($this->filterColumns[$column]['column']) || array_key_exists($column, $this->filterColumns);
    }

    protected function getFilterOp($key): string
    {
        return $this->filterOps[$key] ?? $this->filterColumns[$key]['op'] ?? '=';
    }

    protected function getFilterColumn($key): string
    {
        $column = @$this->filterColumns[$key]['column'];
        if (isset($column))
            return $column;

        return $key;
    }

    protected function isBetweenFilter($value): bool
    {
        return is_array($value) && $this->filterOpIs('between');
    }

    protected function filterOpIs($candidateOp): bool
    {
        return $this->op === $candidateOp;
    }

    protected function applyBetweenFilter($column, $value)
    {
        $this->query->whereBetween($column, $value);
    }

    protected function applyInFilter($column, $value)
    {
        $value = is_array($value) ? $value : [$value];
        $this->query->whereIn($column, $value);
    }

    protected function isColumnRelation($column): bool
    {
        return strpos($column, '.');
    }

    protected function isDateDataType($key): bool
    {
        return @$this->filterColumns[$key]['datatype'] == 'date';
    }

    protected function applyRelationFilter($column, $value)
    {
        [$relation, $relationColumn] = explode('.', $column);

        $this->query->whereHas($relation, function ($q) use ($relationColumn, $value) {
            if ($this->filterOpIs('relationIn')) {
                $value = is_array($value) ? $value : [$value];
                return $q->whereIn($relationColumn, $value);
            }

            return $q->where($relationColumn, $this->op, $value);
        });
    }

    protected function applyRegularFilter($column, $value, $key)
    {
        if ($this->isDateDataType($key))
            return $this->query->whereDate($column, $this->op, $value);

        $this->query->where($column, $this->op, $value);
    }
}
