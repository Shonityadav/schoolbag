<?php

namespace App\Services\IdCard;

use App\Models\IdCardAuditLog;
use Illuminate\Support\Facades\Request;

class AuditLoggerService
{
    /**
     * Log an action regarding ID cards.
     */
    public static function log(string $action, $affectedRecord = null)
    {
        if (auth()->check()) {
            IdCardAuditLog::create([
                'institute_id' => auth()->user()->institute_id ?? null,
                'user_id' => auth()->id(),
                'ip_address' => Request::ip(),
                'action' => $action,
                'affected_record' => is_string($affectedRecord) ? $affectedRecord : json_encode($affectedRecord),
            ]);
        }
    }
}
