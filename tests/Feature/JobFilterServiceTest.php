<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Job;
use App\Services\JobFilterService;

class JobFilterServiceTest extends TestCase
{
    public function test_filter_with_equality()
    {

        $query = Job::query();
        JobFilterService::applyFilters($query, 'AND[company_name=Company 2]');

        $executedSql = $query->toRawSql();

        $expectedSql = "select * from `employment_jobs` where (`company_name` = 'Company 2')";

        $this->assertSame($this->normalizeSql($expectedSql), $this->normalizeSql($executedSql));
    }

    public function test_filter_with_in()
    {

        $query = Job::query();
        JobFilterService::applyFilters($query, 'AND[job_type{IN}(full-time,part-time,contract)]');

        $executedSql = $query->toRawSql();
        $expectedSql = "select * from `employment_jobs` where (`job_type` in ('full-time', 'part-time', 'contract'))";

        $this->assertSame($this->normalizeSql($expectedSql), $this->normalizeSql($executedSql));
    }

    public function test_filter_with_greater_lesser_than()
    {

        $query = Job::query();
        JobFilterService::applyFilters($query,
        'AND[salary_min{gte}(30000),salary_max{lte}(65000)]');

        $executedSql = $query->toRawSql();

        $expectedSql = "select * from `employment_jobs` where (`salary_min` >= 30000 and `salary_max` <= 65000)";

        $this->assertSame($this->normalizeSql($expectedSql), $this->normalizeSql($executedSql));
    }



    public function test_filter_with_date()
    {

        $query = Job::query();
        JobFilterService::applyFilters($query, 'AND[published_at<2025-03-27 12:00:00,published_at>2025-03-26 12:00:00]');

        $executedSql = $query->toRawSql();

        $expectedSql = "select * from `employment_jobs` where (`published_at` < '2025-03-27 12:00:00' and `published_at` > '2025-03-26 12:00:00')";

        $this->assertSame($this->normalizeSql($expectedSql), $this->normalizeSql($executedSql));
    }

    public function test_filter_with_Like()
    {

        $query = Job::query();
        JobFilterService::applyFilters($query, 'AND[title{like}(Job)]');

        $executedSql = $query->toRawSql();

        $expectedSql = "select * from `employment_jobs` where (`title` LIKE '%Job%')";

        $this->assertSame($this->normalizeSql($expectedSql), $this->normalizeSql($executedSql));
    }


    public function test_filter_with_or_and()
    {

        $query = Job::query();
        JobFilterService::applyFilters(
            $query,
            'AND[company_name=Company 2,title!=Developer]
        OR[salary_min{gte}(300),salary_max{lte}(500)]'
        );

        $executedSql = $query->toRawSql();

        $expectedSql = "select * from `employment_jobs` where (`company_name` = 'Company 2' and `title` != 'Developer') or ((`salary_min` >= 300) or (`salary_max` <= 500))";

        $this->assertSame($this->normalizeSql($expectedSql), $this->normalizeSql($executedSql));
    }




    public function test_filter_with_relation()
    {

        $query = Job::query();
        JobFilterService::applyFilters(
            $query,
            'AND[locations.country{HAS_ANY}(USA),languages.name{NOT_ANY}(PHP)]
            OR[categories.name=Software Development,categories.name=Design]'
        );

        $executedSql = $query->toRawSql();

        $expectedSql = "select * from `employment_jobs` where (exists (select * from `locations` inner join `job_location` on `locations`.`id` = `job_location`.`location_id` where `employment_jobs`.`id` = `job_location`.`job_id` and `country` in ('USA')) and exists (select * from `languages` inner join `job_language` on `languages`.`id` = `job_language`.`language_id` where `employment_jobs`.`id` = `job_language`.`job_id` and `name` not in ('PHP'))) or ((exists (select * from `categories` inner join `category_job` on `categories`.`id` = `category_job`.`category_id` where `employment_jobs`.`id` = `category_job`.`job_id` and `name` = 'Software Development')) or (exists
(select * from `categories` inner join `category_job` on `categories`.`id` = `category_job`.`category_id` where `employment_jobs`.`id` = `category_job`.`job_id` and `name` = 'Design')))";

        $this->assertSame($this->normalizeSql($expectedSql), $this->normalizeSql($executedSql));
    }
    public function test_filter_with_attribute()
    {

        $query = Job::query();
        JobFilterService::applyFilters(
            $query,
            'OR[attribute::shift=Night Shift,attribute::environment=Office,attribute::contract_duration{lte}(2)]'
        );

        $executedSql = $query->toRawSql();

        $expectedSql = "select * from `employment_jobs` where ((exists (select * from `job_attribute_values` where `employment_jobs`.`id` = `job_attribute_values`.`job_id` and exists (select * from `attributes` where `job_attribute_values`.`attribute_id` = `attributes`.`id` and `attributes`.`name` = 'shift') and `value` = 'Night Shift')) or (exists (select * from `job_attribute_values` where `employment_jobs`.`id` = `job_attribute_values`.`job_id` and exists (select * from `attributes` where `job_attribute_values`.`attribute_id` = `attributes`.`id` and `attributes`.`name` = 'environment') and `value` = 'Office')) or (exists (select * from `job_attribute_values` where `employment_jobs`.`id` = `job_attribute_values`.`job_id` and exists (select * from `attributes` where `job_attribute_values`.`attribute_id` = `attributes`.`id` and `attributes`.`name` = 'contract_duration') and `value` <= 2)))";

        $this->assertSame($this->normalizeSql($expectedSql), $this->normalizeSql($executedSql));
    }




    private function normalizeSql($sql)
    {
        return trim(preg_replace('/\s+/', ' ', $sql));
    }
}
