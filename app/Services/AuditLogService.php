<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLogService
{
    public static function log(?Model $auditable, string $event, array $context = []): void
    {
        try {
            $user = auth()->user();

            AuditLog::create([
                'trace_id' => Str::uuid()->toString(),
                'event' => $event,
                'auditable_type' => $auditable ? get_class($auditable) : 'system',
                'auditable_id' => $auditable?->getKey(),
                'user_id' => $user?->id,
                'user_type' => $user ? get_class($user) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'old_values' => $context['old'] ?? null,
                'new_values' => $context['new'] ?? null,
                'description' => $context['description'] ?? null,
                'metadata' => $context['metadata'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
