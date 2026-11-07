<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobModerationHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobModerationController extends Controller
{
    /**
     * Hàng đợi duyệt: tin gửi sớm nhất được xử lý trước.
     */
    public function index(): View
    {
        $jobs = Job::with(['company', 'industry', 'creator'])
            ->where('status', Job::STATUS_PENDING)
            ->oldest()
            ->paginate(15);

        return view('admin.job-moderation.index', compact('jobs'));
    }

    public function moderate(Request $request, Job $job): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(array_keys(Job::MODERATION_ACTIONS))],
            'note' => [Rule::requiredIf($request->input('action') === JobModerationHistory::ACTION_REJECT), 'nullable', 'string', 'max:2000'],
        ]);

        $job->moderate($data['action'], $request->user(), $data['note'] ?? null);

        $message = match ($data['action']) {
            JobModerationHistory::ACTION_APPROVE => 'Đã duyệt tin tuyển dụng.',
            JobModerationHistory::ACTION_REJECT => 'Đã từ chối tin tuyển dụng.',
            JobModerationHistory::ACTION_HIDE => 'Đã ẩn tin tuyển dụng.',
            JobModerationHistory::ACTION_RESTORE => 'Đã khôi phục tin tuyển dụng.',
        };

        return back()->with('success', $message);
    }
}
