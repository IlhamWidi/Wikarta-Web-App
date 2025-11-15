<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Services\NotificationSender;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_reports');
    }

    public function index(Request $request)
    {
        $query = NotificationLog::query()->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('channel') && $request->channel !== 'all') {
            $query->where('channel', $request->channel);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function resend(NotificationLog $notificationLog, NotificationSender $notificationSender)
    {
        $notificationLog->increment('retry_count');
        $notificationLog->update([
            'status' => 'pending',
            'failed_at' => null,
            'error_message' => null,
            'sent_at' => null,
        ]);

        try {
            $notificationSender->resendFromLog($notificationLog);

            $notificationLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return response()->json([
                'message' => 'Notifikasi dikirim ulang',
                'data' => $notificationLog->fresh(),
            ]);
        } catch (\Throwable $e) {
            $notificationLog->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal mengirim ulang notifikasi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
