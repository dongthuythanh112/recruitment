<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\View\View;

class JobController extends Controller
{
    /**
     * Tin chưa duyệt, đã ẩn hoặc quá hạn trả về 404 như tin không tồn tại.
     */
    public function show(string $slug): View
    {
        $job = Job::published()
            ->with(['company', 'industry', 'skills'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('client.jobs.show', [
            'job' => $job,
            'relatedJobs' => Job::published()
                ->with('company')
                ->where('industry_id', $job->industry_id)
                ->whereKeyNot($job->id)
                ->latest('published_at')
                ->limit(4)
                ->get(),
        ]);
    }
}
