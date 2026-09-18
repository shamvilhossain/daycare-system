<?php

namespace App\Jobs;

use App\Models\ChildFoundReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFoundChildAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ChildFoundReport $report)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->report->loadMissing(['safetyTag.child.parents']);

        $child = $this->report->safetyTag?->child;
        if (!$child) {
            Log::warning("SendFoundChildAlertJob: Child not found for report #{$this->report->id}");
            return;
        }

        $primaryParent = $child->parentProfile;
        $guardianPhone = $primaryParent?->mobile ?? $child->ec_phone;

        $mapsUrl = null;
        if ($this->report->latitude && $this->report->longitude) {
            $mapsUrl = "https://www.google.com/maps?q={$this->report->latitude},{$this->report->longitude}";
        }

        $alertMessage = "URGENT: Your child {$child->first_name} was reported found via QR Safety Tag.";
        if ($this->report->reporter_phone) {
            $alertMessage .= " Reporter Phone: {$this->report->reporter_phone}.";
        }
        if ($mapsUrl) {
            $alertMessage .= " Location: {$mapsUrl}.";
        }
        if ($this->report->message) {
            $alertMessage .= " Message: \"{$this->report->message}\".";
        }

        Log::info("Found Child Alert for Child #{$child->id} ({$child->first_name}): {$alertMessage} [Target Phone: {$guardianPhone}]");

        // TODO: Send SMS / WhatsApp / Push Notification to guardian ($guardianPhone) and emergency contacts.
        // Example:
        // if ($guardianPhone) {
        //     SmsService::send($guardianPhone, $alertMessage);
        // }

        $this->report->update(['status' => 'notified']);
    }
}
