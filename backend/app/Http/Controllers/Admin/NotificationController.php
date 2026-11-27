<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->userNotifications()
            ->latest('created_at')
            ->paginate(15);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $request->user()->userNotifications()->whereNull('read_at')->count(),
        ]);
    }

    public function show(Request $request, Notification $notification): View
    {
        $this->assertOwned($request, $notification);
        $jobId = $notification->data['job_id'] ?? null;

        return view('admin.notifications.show', [
            'notification' => $notification,
            'job' => $jobId ? Job::find($jobId) : null,
        ]);
    }

    public function open(Request $request, Notification $notification): RedirectResponse
    {
        $this->assertOwned($request, $notification);

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        if ($notification->type === 'job.pending_review') {
            $jobId = $notification->data['job_id'] ?? null;
            $job = is_numeric($jobId) ? Job::find($jobId) : null;

            if ($job) {
                return redirect()->route('admin.jobs.show', $job);
            }
        }

        return redirect()->route('admin.notifications.show', $notification);
    }

    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        $this->assertOwned($request, $notification);
        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->userNotifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }

    private function assertOwned(Request $request, Notification $notification): void
    {
        abort_unless((int) $notification->user_id === (int) $request->user()->id, 404);
    }
}
