<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class JobFilterService
{
    public static function applyFilters(Builder $query, string $filterString): Builder
    {
        $filters = FilterParser::parse($filterString);

        return self::applyConditions($query, $filters);
    }

    private static function applyConditions(Builder $query, array $filters): Builder
    {
        if (!empty($filters['AND'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['AND'] as $condition) {
                    self::applyCondition($q, $condition);
                }
            });
        }

        if (!empty($filters['OR'])) {
            $query->orWhere(function ($q) use ($filters) {
                foreach ($filters['OR'] as $condition) {
                    $q->orWhere(function ($orQ) use ($condition) {
                        self::applyCondition($orQ, $condition, 'or');
                    });
                }

            });
        }

        return $query;
    }

    private static function applyCondition(Builder $query, array $condition, $group = 'and'): void
    {
        $field = $condition['field'];
        [$method, $operator] = self::mapOperator($condition['operator']);
        $value = self::castValue($condition['value']);

        if (strpos($field, 'attribute::') === 0) {
            self::applyEAVCondition(
                $query,
                $field,
                $method,
                $operator,
                $value,
                $group
            );
        } elseif (strpos($field, '.') !== false) {
            self::applyRelationCondition(
                $query,
                $field,
                $method,
                $operator,
                $value,
                $group
            );
        } else {
            self::applyFieldCondition(
                $query,
                $field,
                $method,
                $operator,
                $value,
                $group
            );
        }
    }

    private static function applyFieldCondition(
        Builder $query,
        string $field,
        string $method,
        ?string $operator,
        $value,
        $group = 'and'
    ): void {

        if ($operator) {
            $value = $operator == "LIKE" ? "%$value%" : $value;
            $query->{$group == 'or' ? 'orWhere' : 'where'}($field, $operator, $value);
        } else {
            $method = $group == 'or' ? 'or' . ucfirst($method) : $method;
            $query->{$method}($field, (array) $value);
        }
    }

    private static function applyRelationCondition(
        Builder $query,
        string $field,
        string $method,
        ?string $operator,
        $value,
        $group = 'and'
    ): void {
        [$relation, $column] = explode('.', $field);
        // load relation
        $query->with($relation);

        $query->whereHas($relation, function ($q) use ($column, $method, $operator, $value) {
            self::applyFieldCondition(
                $q,
                $column,
                $method,
                $operator,
                $value,
                'and'
            );
        });
    }

    private static function applyEAVCondition(
        Builder $query,
        string $field,
        string $method,
        ?string $operator,
        $value
        ,
        $group = 'and'
    ): void {
        // load attributes relation
        $query->with('attributes');
        $attributeName = str_replace('attribute::', '', $field);

        $query->whereHas('attributes', function ($q) use ($attributeName, $method, $operator, $value, $group) {
            $q->whereHas('attribute', function ($attrQuery) use ($attributeName) {
                $attrQuery->where('attributes.name', $attributeName);
            });

            self::applyFieldCondition(
                $q,
                'value',
                $method,
                $operator,
                $value,
                'and'
            );
        });
    }

    private static function mapOperator(string $operator): array
    {
        $operatorMap = [
            '=' => ['where', '='],
            '!=' => ['where', '!='],
            '>' => ['where', '>'],
            '<' => ['where', '<'],
            'eq' => ['where', '='],
            'neq' => ['where', '!='],
            'gt' => ['where', '>'],
            'gte' => ['where', '>='],
            'lt' => ['where', '<'],
            'lte' => ['where', '<='],
            'like' => ['where', 'LIKE'],
            'not_like' => ['where', 'NOT LIKE'],
            'in' => ['whereIn', null],
            'not_in' => ['whereNotIn', null],
            'is_any' => ['whereIn', null],
            'has_any' => ['whereIn', null],
            'not_any' => ['whereNotIn', null],
        ];

        return $operatorMap[$operator] ?? ['where', '='];
    }

    private static function castValue($value)
    {
        if (is_array($value)) {
            return array_map(fn($v) => self::castSingleValue($v), $value);
        }
        return self::castSingleValue($value);
    }

    private static function castSingleValue($value)
    {
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        } elseif (strtotime($value)) {
            return Carbon::parse($value);
        }
        return $value;
    }
}
