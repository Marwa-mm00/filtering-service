# Laravel Job Filtering API - README

## Overview
This Laravel project provides an API endpoint to filter job listings dynamically using a flexible query string-based filtering system. It allows filtering based on standard fields, related models, and entity-attribute-value (EAV) attributes.

## Features
- Supports `AND` and `OR` conditions in the filtering.
- Handles different comparison operators (`=`, `!=`, `>`, `<`, `gte`,`lte`,`IN`,`NOT_IN`,`LIKE`,`NOT_LIKE`,`IS_ANY`,`HAS_ANY`,`NOT_ANY`).
- Supports filtering by related models.
- Implements EAV filtering for dynamic attributes.
- Uses `EXISTS` subqueries to optimize filtering performance.

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/Marwa-mm00/filtering-service.git
   cd filtering-service
   ```

2. Install dependencies:
   ```sh
   composer install
   ```
3. Set up the environment file:
   ```sh
   cp .env.example .env
   ```
   Configure database connection details in `.env`.

4. Run database migrations and seeders:
   ```sh
   php artisan migrate --seed
   ```

6. Start the Laravel development server:
   ```sh
   php artisan serve
   ```

## Or
 Manullay extract the project folder 
 Export the sample data file `jobs_filtering.sql`
 Start server command

## API Endpoint
### Filter Jobs
**Endpoint:**
```
GET /api/jobs?filters=AND[]OR[]
```

### Filter Query Syntax
Filters are passed as a query string in the following format:
```
filters=AND[field=value,field{operator}(value),relation.field{operator}(value)]OR[attribute::attribute_name{operator}(value)]NOT[field{operator}(value)]
```

#### Example Queries
1. Find jobs where `title = Developer` OR `status != closed`:
   ```
   /api/jobs?filters=AND[title=Developer]OR[status!=closed]
   ```
2. Find jobs where `job_type in (full-time,part-time,contract)`:
   ```
   /api/jobs?filters=AND[job_type{IN}(full-time,part-time,contract)]
   ```
3. Find jobs with an attribute `job_type in (full-time,part-time,contract)` AND `is_remove = false` AND `salary_min >= 30000` AND `salary_max >= 80000`:
   ```
   /api/jobs?filters=AND[job_type{IN}(full-time,part-time,contract),is_remote=0,salary_min{gte}(30000),salary_max{lte}(80000)]
   ```
4. Find jobs where `job_type = full-time` AND `languages.name in (PHP , JavaScript) ` AND `locations.city in (New York,San Francisco)` OR `attribute contract_duration >= 3` OR `status = draft`
   ```
   /api/jobs?filters=AND[job_type=full-time,languages.name{HAS_ANY}(PHP,JavaScript),locations.city{HAS_ANY}(New York,San Francisco)]OR[attribute::contract_duration{gte}(3),status=draft]
   ```

## Code Structure

### `JobFilterService.php`
- `applyFilters($query, $filterString)`: Parses filters and applies them to the query.
- `applyConditions($query, $filters)`: Handles `AND`, `OR` conditions.
- `applyCondition($query, $condition, $group)`: Determines whether filtering applies to standard fields, relations, or EAV attributes.
- `applyFieldCondition($query, $field, $method, $operator, $value)`: Filters normal fields.
- `applyRelationCondition($query, $field, $method, $operator, $value)`: Handles relational filtering.
- `applyEAVCondition($query, $field, $method, $operator, $value)`: Implements filtering for entity-attribute-value attributes.
- `mapOperator($operator)`: Converts operator aliases into valid SQL conditions.
- `castValue($value)`: Parses numeric and date values correctly.

### `FilterParser.php`
- `parse($filterString)`: Parses the filter query string and extracts filtering conditions.

## Testing

Test cases for testing `FilterParser` and `JobFilterServices` included:

```sh
php artisan test
```

## Notes
- The `employment_jobs` table is used instead of `jobs`.
- The filtering mechanism uses `EXISTS` for EAV filtering to improve performance.
- Postman collection included with examples.
