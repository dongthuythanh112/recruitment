<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Job;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $candidate = $this->candidate($request);
        $since = now()->subMonths(3);
        $notifications = $request->user()->userNotifications()
            ->where('created_at', '>=', $since)
            ->latest('created_at')
            ->paginate(12);

        return view('client.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $request->user()->userNotifications()
                ->where('created_at', '>=', $since)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function show(Request $request, Notification $notification): View
    {
        $candidate = $this->candidate($request);
        $this->assertOwned($candidate, $notification);

        return view('client.notifications.show', compact('notification'));
    }

    public function open(Request $request, Notification $notification): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $this->assertOwned($candidate, $notification);

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        if ($notification->type === 'application.status_changed') {
            $applicationId = $notification->data['application_id'] ?? null;
            $application = is_numeric($applicationId)
                ? $candidate->applications()->whereKey($applicationId)->first()
                : null;

            if ($application) {
                return redirect(route('candidate.applications.index', ['application_id' => $application->id]).'#application-'.$application->id);
            }
        }

        $jobId = $notification->data['job_id'] ?? null;
        $job = is_numeric($jobId) ? Job::published()->whereKey($jobId)->first() : null;

        return $job
            ? redirect()->route('jobs.show', $job->slug)
            : redirect()->route('candidate.notifications.show', $notification);
    }

    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $this->assertOwned($candidate, $notification);
        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return back()->with('success', 'Đã đánh dấu thông báo là đã xem.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $this->candidate($request);
        $request->user()->userNotifications()
            ->where('created_at', '>=', now()->subMonths(3))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã xem.');
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

    private function assertOwned(CandidateProfile $candidate, Notification $notification): void
    {
        abort_unless((int) $notification->user_id === (int) $candidate->user_id, 404);
    }
}
