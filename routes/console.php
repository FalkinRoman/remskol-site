<?php

use App\Models\Lead;
use App\Models\Setting;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('telegram:setup {--token= : Токен бота от @BotFather} {--chat=* : chat_id получателей (можно несколько)} {--code= : Код для /subscribe}', function (TelegramService $telegram) {
    if ($token = $this->option('token')) {
        $telegram->setToken($token);
        $me = $telegram->getMe();
        $me->json('ok')
            ? $this->info('Бот: @'.$me->json('result.username'))
            : $this->error('Токен не принят Telegram: '.$me->json('description'));
    }

    if ($chats = $this->option('chat')) {
        $telegram->setChatIds($chats);
    }

    if ($code = $this->option('code')) {
        Setting::put('telegram.subscribe_code', $code);
    }

    $this->table(['Параметр', 'Значение'], [
        ['Токен', $telegram->token() ? Str::mask($telegram->token(), '*', 10, -4) : '—'],
        ['Получатели', implode(', ', $telegram->chatIds()) ?: '—'],
        ['Код подписки', $telegram->subscribeCode() ?? '—'],
    ]);
})->purpose('Настройка Telegram-бота для заявок');

Artisan::command('telegram:webhook {action=set : set|delete|info}', function (TelegramService $telegram) {
    $response = match ($this->argument('action')) {
        'delete' => $telegram->deleteWebhook(),
        'info' => $telegram->getWebhookInfo(),
        default => (function () use ($telegram) {
            $secret = $telegram->webhookSecret();
            if (! $secret) {
                $secret = Str::random(48);
                Setting::put('telegram.webhook_secret', $secret);
            }

            return $telegram->setWebhook(route('telegram.webhook'), $secret);
        })(),
    };

    $this->line(json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
})->purpose('Управление вебхуком Telegram (нужен HTTPS и правильный APP_URL)');

Artisan::command('telegram:test', function (TelegramService $telegram) {
    if (! $telegram->isConfigured()) {
        $this->error('Сначала: php artisan telegram:setup --token=... --chat=...');

        return 1;
    }

    $lead = new Lead(['name' => 'Тест', 'phone' => '+7 (999) 000-00-00', 'service' => 'Проверка связи']);
    $lead->id = 0;
    $lead->created_at = now();

    foreach ($telegram->chatIds() as $chatId) {
        $r = $telegram->sendMessage($chatId, $telegram->formatLead($lead));
        $r->json('ok') ? $this->info("{$chatId}: OK") : $this->error("{$chatId}: ".$r->json('description'));
    }
})->purpose('Отправить тестовую заявку во все чаты');

Artisan::command('leads:resend', function () {
    Lead::query()->whereNull('telegram_sent_at')->orderBy('id')->each(function (Lead $lead) {
        dispatch_sync(new \App\Jobs\SendLeadToTelegram($lead));
        $this->line("#{$lead->id}: ".($lead->fresh()->telegram_sent_at ? 'OK' : 'ошибка'));
    });
})->purpose('Переотправить в Telegram заявки, которые не дошли');
