<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedJobController extends Controller
{
    public function index(Request $request): View
    {
        $candidate = $this->candidate($request);
        $jobs = $candidate->savedJobs()
            ->published()
            ->with(['company', 'industry'])
            ->orderByPivot('created_at', 'desc')
            ->paginate(12);

        return view('client.saved-jobs.index', compact('jobs'));
    }

    public function store(Request $request, Job $job): RedirectResponse
    {
        $candidate = $this->candidate($request);
        abort_unless(Job::published()->whereKey($job->id)->exists(), 404);

        $candidate->savedJobs()->syncWithoutDetaching([$job->id => ['created_at' => now()]]);

        return back()->with('success', 'Đã lưu việc làm.');
    }

    public function destroy(Request $request, Job $job): RedirectResponse
    {
        $this->candidate($request)->savedJobs()->detach($job->id);

        return back()->with('success', 'Đã bỏ lưu việc làm.');
    }

    private function candidate(Request $request): CandidateProfile
    {
        abort_unless(
            $request->user()?->role === User::ROLE_CANDIDATE
                && $request->user()?->status === User::STATUS_ACTIVE,
            403,
            'Chức năng này chỉ dành cho ứng viên đang hoạt động.',
        );

        return $request->user()->candidateProfile()->firstOrCreate();
    }
}
