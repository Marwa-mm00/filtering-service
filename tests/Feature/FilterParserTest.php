<?php

namespace Tests\Unit;

use App\Services\FilterParser;
use PHPUnit\Framework\TestCase;

class FilterParserTest extends TestCase
{
    public function test_it_parses_simple_and_conditions()
    {
        $filter = 'AND[type=tt, status{IN}(closed,archived)]';
        $expected = [
            'AND' => [
                ['field' => 'type', 'operator' => '=', 'value' => 'tt'],
                ['field' => 'status', 'operator' => 'in', 'value' => ['closed', 'archived']],
            ],
            'OR' => []
        ];

        $this->assertEquals($expected, FilterParser::parse($filter));
    }

    public function test_it_parses_simple_or_conditions()
    {
        $filter = 'OR[status{Not_IN}(closed,archived)]';
        $expected = [
            'AND' => [],
            'OR' => [
                ['field' => 'status', 'operator' => 'not_in', 'value' => ['closed', 'archived']]
            ]
        ];

        $this->assertEquals($expected, FilterParser::parse($filter));
    }

    public function test_it_parses_like_conditions()
    {
        $filter = 'AND[name{LIKE}(John), description{LIKE}(test)]';
        $expected = [
            'AND' => [
                ['field' => 'name', 'operator' => 'like', 'value' => 'John'],
                ['field' => 'description', 'operator' => 'like', 'value' => 'test']
            ],
            'OR' => []
        ];

        $this->assertEquals($expected, FilterParser::parse($filter));
    }

    public function test_it_parses_attribute_conditions()
    {
        $filter = 'AND[attribute::name{LIKE}(John), attribute::description{LIKE}(test)]';
        $expected = [
            'AND' => [
                ['field' => 'attribute::name', 'operator' => 'like', 'value' => 'John'],
                ['field' => 'attribute::description', 'operator' => 'like', 'value' => 'test']
            ],
            'OR' => []
        ];
        $this->assertEquals($expected, FilterParser::parse($filter));
    }

    public function test_it_parses_field_with_underscore_conditions()
    {
        $filter = 'AND[first_name=John,last_name{LIKE}(test)]';
        $expected = [
            'AND' => [
                ['field' => 'first_name', 'operator' => '=', 'value' => 'John'],
                ['field' => 'last_name', 'operator' => 'like', 'value' => 'test']
            ],
            'OR' => []
        ];
        $this->assertEquals($expected, FilterParser::parse($filter));
    }

    public function test_it_parses_mixed_and_or_conditions()
    {
        $filter = 'AND[type=tt, status{IN}(closed,archived)]OR[role=admin,type=m]';
        $expected = [
            'AND' => [
                ['field' => 'type', 'operator' => '=', 'value' => 'tt'],
                ['field' => 'status', 'operator' => 'in', 'value' => ['closed', 'archived']]
            ],
            'OR' => [
                ['field' => 'role', 'operator' => '=', 'value' => 'admin'],
                ['field' => 'type', 'operator' => '=', 'value' => 'm'],
            ]
        ];
        $this->assertEquals($expected, FilterParser::parse($filter));
    }

    public function test_it_parses_empty_filter()
    {
        $filter = '';
        $expected = ['AND' => [], 'OR' => []];
        $this->assertEquals($expected, FilterParser::parse($filter));
    }

    public function test_it_parses_wrong_operators()
    {
        $filter = 'AND[name{L}John, description{N}test]';
        $expected = [
            'AND' => [],
            'OR' => []
        ];
        $this->assertEquals($expected, FilterParser::parse($filter));
    }

    public function test_it_parses_integers_conditions()
    {
        $filter = 'AND[salary>1000, package{lte}(200)]OR[age{gte}(18), age<30]';
        $expected = [
            'AND' => [
                ['field' => 'salary', 'operator' => '>', 'value' => '1000'],
                ['field' => 'package', 'operator' => 'lte', 'value' => '200']
            ],
            'OR' => [
                ['field' => 'age', 'operator' => 'gte', 'value' => '18'],
                ['field' => 'age', 'operator' => '<', 'value' => '30']
            ]
        ];

        $this->assertEquals($expected, FilterParser::parse($filter));
    }

    public function test_it_parses_relation_conditions()
    {
        $filter = 'AND[locations{IS_ANY}(test,test2),languages{HAS_ANY}(test)]';
        $expected = [
            'AND' => [
                ['field' => 'locations', 'operator' => 'is_any', 'value' => ['test', 'test2']],
                ['field' => 'languages', 'operator' => 'has_any', 'value' => 'test']
            ],
            'OR' => []
        ];

        $this->assertEquals($expected, FilterParser::parse($filter));
    }
}
