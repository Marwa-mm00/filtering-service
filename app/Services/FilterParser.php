<?php

namespace App\Services;

class FilterParser
{
    public static function parse($filterString)
    {
        if (empty($filterString)) {
            return ['AND' => [], 'OR' => []];
        }

        $result = ['AND' => [], 'OR' => []];

        // Matches AND[...] or OR[...] groups
        preg_match_all('/(AND|OR)\[([^\]]+)\]/', $filterString, $groups, PREG_SET_ORDER);

        foreach ($groups as $group) {
            $logicType = $group[1]; // AND or OR
            $conditions = $group[2];

            preg_match_all('/([\w:.]+)(?:{([\w_]+)})?\(([^)]+)\)|([\w:.]+)(!=|=|>|<)([^,\]]+)/', $conditions, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                if (!empty($match[1])) { // Handles `field{OPERATOR}(values)`
                    $field = $match[1];
                    $operator = strtolower($match[2] ?? 'eq'); // Default '=' -> 'eq'
                    $values = explode(',', $match[3]);
                } else { // Handles `field=value`
                    $field = $match[4];
                    $operator = $match[5];
                    $values = [$match[6]];
                }

                // Trim values and convert to array if multiple
                $values = array_map('trim', $values);
                if (count($values) === 1) {
                    $values = $values[0]; // Convert to single value
                }

                $result[$logicType][] = [
                    'field' => $field,
                    'operator' => $operator,
                    'value' => $values
                ];
            }
        }

        return $result;
    }
}
