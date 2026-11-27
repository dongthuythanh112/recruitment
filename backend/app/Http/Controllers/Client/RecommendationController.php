<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\User;
use App\Services\JobRecommendationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    public function index(Request $request, JobRecommendationService $recommendations): View
    {
        abort_unless(
            $request->user()?->role === User::ROLE_CANDIDATE
                && $request->user()?->status === User::STATUS_ACTIVE,
            403,
            'Chức năng này chỉ dành cho ứng viên đang hoạt động.',
        );

        $candidate = $request->user()->candidateProfile()->with('skills')->first() ?? new CandidateProfile();
        $suggestions = $recommendations->recommend($candidate, 8);

        return view('client.recommendations.index', [
            'recommendedJobs' => $suggestions['matched'],
            'featuredJobs' => $suggestions['featured'],
        ]);
    }
}
