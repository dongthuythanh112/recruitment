<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $since = now()->subMonths(3);
        $notifications = $request->user()->userNotifications()
            ->where('created_at', '>=', $since)
            ->latest('created_at')
            ->paginate(12);

        return view('recruiter.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $request->user()->userNotifications()
                ->where('created_at', '>=', $since)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function open(Request $request, Notification $notification): RedirectResponse
    {
        $this->assertOwned($request, $notification);

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        $applicationId = $notification->data['application_id'] ?? null;
        $application = is_numeric($applicationId)
            ? Application::whereKey($applicationId)
                ->whereHas('job', fn ($query) => $query->where('created_by', $request->user()->id))
                ->first()
            : null;

        return $application
            ? redirect()->route('recruiter.applications.show', $application)
            : redirect()->route('recruiter.applications.index');
    }

    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        $this->assertOwned($request, $notification);
        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return back()->with('success', 'Đã đánh dấu thông báo là đã xem.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->userNotifications()
            ->where('created_at', '>=', now()->subMonths(3))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã xem.');
    }

    private function assertOwned(Request $request, Notification $notification): void
    {
        abort_unless((int) $notification->user_id === (int) $request->user()->id, 404);
    }
}
