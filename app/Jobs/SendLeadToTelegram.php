<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\TelegramService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendLeadToTelegram
{
    use Dispatchable;

    public function __construct(public Lead $lead) {}

    public function handle(TelegramService $telegram): void
    {
        $errors = $telegram->notifyLead($this->lead);

        if ($errors === []) {
            $this->lead->update(['telegram_sent_at' => now(), 'telegram_error' => null]);

            return;
        }

        $this->lead->update(['telegram_error' => json_encode($errors, JSON_UNESCAPED_UNICODE)]);
        Log::warning('Lead not delivered to Telegram', ['lead' => $this->lead->id, 'errors' => $errors]);
    }
}
