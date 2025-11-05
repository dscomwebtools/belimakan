<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadeNotification;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'pemberitahuan');

        $notifications = Notification::where('type', ucfirst($type))
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->message = strip_tags($item->message);
                $item->formatted_date = $item->created_at->translatedFormat('d F Y H:i');
                return $item;
            });

        // Kalau request-nya AJAX (fetch)
        if ($request->ajax()) {
            return response()->json($notifications);
        }

        // Kalau normal load (halaman pertama)
        return view('admin.notification.index', compact('notifications'));
    }

    public function fetch(Request $request)
    {
        $type = $request->get('type', 'Pemberitahuan');

        $notifications = Notification::query()
            ->whereRaw('LOWER(type) = ?', [strtolower($type)])
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'message' => strip_tags($item->message),
                    'type' => $item->type,
                    'is_broadcast' => $item->is_broadcast,
                    'created_at' => Carbon::parse($item->created_at)
                        ->locale('id')
                        ->translatedFormat('d F Y H:i'),
                ];
            });

        return response()->json($notifications);
    }

    public function create()
    {
        $users = User::select('id', 'name', 'email')->get();
        return view('admin.notification.form', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => [
                'nullable',
                Rule::in(array_merge(['broadcast'], User::pluck('id')->toArray())),
            ],
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
            'link' => 'nullable|string|max:255',
            'is_broadcast' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date',
        ]);

        $data['is_broadcast'] = $request->user_id === 'broadcast';
        $data['user_id'] = $request->user_id === 'broadcast' ? null : $request->user_id;

        $notification = Notification::create($data);

        if ($data['is_broadcast']) {
            $users = User::pluck('id');
        } else {
            $users = collect([$data['user_id']]);
        }

        $notification->users()->attach($users);

        $recipients = User::whereIn('id', $users)->get();

        FacadeNotification::send($recipients, new CustomNotification([
            'title' => $data['title'],
            'message' => $data['message'],
        ]));

        return redirect()
            ->route('notification.index')
            ->with('success', 'Notification created successfully.');
    }

    public function edit(Notification $notification)
    {
        $users = User::select('id', 'name', 'email')->get();
        return view('admin.notification.form', ['data' => $notification, 'users' => $users]);
    }

    public function update(Request $request, Notification $notification)
    {
        $data = $request->validate([
            'user_id' => [
                'nullable',
                Rule::in(array_merge(['broadcast'], User::pluck('id')->toArray())),
            ],
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
            'link' => 'nullable|string|max:255',
            'is_broadcast' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_broadcast'] = $request->user_id === 'broadcast';
        $data['user_id'] = $request->user_id === 'broadcast' ? null : $request->user_id;
        $data['is_active'] = $request->has('is_active');

        $notification->update($data);

        return redirect()
            ->route('notification.index')
            ->with('success', 'Notifikasi berhasil diperbarui.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()
            ->route('notification.index')
            ->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function userNotifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->withPivot('is_read', 'read_at')
            ->orderByDesc('notifications.created_at')
            ->get();

        return response()->json($notifications);
    }

    public function userNotificationsCount()
    {
        $user = Auth::user();

        $unreadCount = $user->notifications()
            ->wherePivot('is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = Notification::findOrFail($id);

        $user->notifications()->updateExistingPivot($notification->id, [
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['message' => 'Marked as read.']);
    }
}
