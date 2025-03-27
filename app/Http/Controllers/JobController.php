<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Services\JobFilterService;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query();

        if ($request->has('filters') && $request->input('filters')) {

            $query = JobFilterService::applyFilters($query, $request->input('filters'));
        }

        return $query->paginate(50);
    }
}
