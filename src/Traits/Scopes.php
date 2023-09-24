<?php

namespace NamaaSolutions\CrudGenerator\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Scopes
{
    public function scopeActive($query)
    {
        $query->where('active', 1);
    }

    public function scopeFilterColumn(Builder $query, string $columnName, $operator = '=')
    {
        if (!request()->{$columnName}) {
            return $query;
        }

        switch ($operator) {
            case '=':
                return $query->where($columnName, $operator, request()->{$columnName});
                break;
            case 'LIKE':
                return $query->where($columnName, $operator, '%' . request()->{$columnName} . '%');
                break;
            default:
                return $query->where($columnName, $operator, request()->{$columnName});
                break;
        }
    }

    public function getColumnValueByOperator($value, $operator)
    {
        switch ($operator) {
            case 'LIKE':
                return '%' . $value . '%';
                break;
            case '=':
            default:
                return $value;
                break;
        }
    }

    public function scopeDynamicFilter(Builder $query, $operator = [])
    {
        if (!request()->filters) {
            return $query;
        }

        $filters = request()->filters ?? [];
        $filters = is_array(request()->filters) ? json_encode(request()->filters) : request()->filters;
        $filter = [];
        foreach (json_decode($filters, true) ?? [] as $key => $value) {
            if ($value !== null) {
                $filter[$key] = $value;
            }
        }
        unset($filter['m']); //manual

        return $query->where(function ($query) use ($filter, $operator) {
            foreach ($filter as $col => $value) {
                if (is_array($filter[$col])) { //Handle Relationship
                    $relation = null;
                    $factorial = function ($filter, $col) use (&$factorial, &$relation, $query, $operator) {
                        if (!is_array($filter[$col])) {
                            if (!empty($filter[$col])) {
                                $query->whereRelation(
                                    $relation,
                                    $col,
                                    $operator[$col] ?? 'LIKE',
                                    $this->getColumnValueByOperator($filter[$col], $operator[$col] ?? 'LIKE')
                                );
                            }

                            return 1;
                        }
                        $relation = $relation ? $relation . '.' . $col : $col;
                        foreach (array_keys($filter[$col]) as $key => $column) {
                            $factorial($filter[$col], $column);
                        }
                    };
                    $factorial($filter, $col);
                } elseif (str_contains($col, '.')) { //Handle Database With Join
                    if (!empty($filter[$col])) {
                        $query->where(
                            $col,
                            $operator[$col] ?? '=',
                            $this->getColumnValueByOperator($filter[$col], $operator[$col] ?? '=')
                        );
                    }
                } else {
                    if (($filter[$col] == 0 || !empty($filter[$col])) && in_array($col, [...$this->getFillable(), 'uuid'])) {
                        $query->where(
                            $this->getTable() . '.' . $col,
                            $operator[$col] ?? '=',
                            $this->getColumnValueByOperator($filter[$col], $operator[$col] ?? '=')
                        );
                    }
                }
            }
        });
    }

    public function scopeOrderByCrud(Builder $query, string $translationField, string $sortMethod = 'asc')
    {

        if (
            $this->translatedAttributes
            && (in_array($translationField, $this->translatedAttributes)
                || str_contains($translationField, 'ar.')
                || str_contains($translationField, 'en.'))
        ) {
            $lang = str_contains($translationField, 'ar.') ? 'ar' : 'en';
            !str_contains($translationField, 'ar.') ?: $translationField = substr($translationField, strpos($translationField, 'ar.') + 3);
            !str_contains($translationField, 'en.') ?: $translationField = substr($translationField, strpos($translationField, 'en.') + 3);

            $temp = app()->getLocale();
            app()->setLocale($lang);

            $query = $query->orderByTranslation($translationField, $sortMethod);
            app()->setLocale($temp);

            return $query;
        } elseif (str_contains($translationField, '.')) {
            return $query->orderBy($translationField, $sortMethod);
        } elseif (str_contains($translationField, '$')) {
            $translationField = str_replace('$', '.', $translationField);
            $lastDotOcc = strrpos($translationField, '.');

            return $query->with([substr($translationField, 0, $lastDotOcc) => function ($q) use ($sortMethod, $translationField, $lastDotOcc) {
                $q->orderBy(substr($translationField, $lastDotOcc + 1), $sortMethod);
            }]);
        } else {
            return $query->orderBy($translationField, $sortMethod);
        }
    }
}
