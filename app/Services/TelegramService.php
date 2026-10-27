<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class TelegramService
{
    public function token(): ?string
    {
        return Setting::get('telegram.bot_token', config('services.telegram.bot_token')) ?: null;
    }

    /** @return list<string> */
    public function chatIds(): array
    {
        $ids = Setting::get('telegram.chat_ids', config('services.telegram.chat_ids', []));

        return array_values(array_unique(array_map('strval', (array) $ids)));
    }

    public function setToken(?string $token): void
    {
        $token ? Setting::put('telegram.bot_token', $token) : Setting::forget('telegram.bot_token');
    }

    /** @param list<string> $ids */
    public function setChatIds(array $ids): void
    {
        Setting::put('telegram.chat_ids', array_values(array_unique(array_map('strval', $ids))));
    }

    public function addChat(string $chatId): void
    {
        $this->setChatIds([...$this->chatIds(), $chatId]);
    }

    public function removeChat(string $chatId): void
    {
        $this->setChatIds(array_values(array_diff($this->chatIds(), [$chatId])));
    }

    public function subscribeCode(): ?string
    {
        return Setting::get('telegram.subscribe_code', config('services.telegram.subscribe_code')) ?: null;
    }

    public function webhookSecret(): ?string
    {
        return Setting::get('telegram.webhook_secret', config('services.telegram.webhook_secret')) ?: null;
    }

    public function isConfigured(): bool
    {
        return $this->token() !== null && $this->chatIds() !== [];
    }

    public function sendMessage(string $chatId, string $html): Response
    {
        return $this->call('sendMessage', [
            'chat_id' => $chatId,
            'text' => $html,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);
    }

    /** jpg/png/webp — как фото с превью, остальное (HEIC) — документом. Подпись ≤ 1024 символов. */
    public function sendFile(string $chatId, string $path, string $captionHtml): Response
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        [$method, $field] = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)
            ? ['sendPhoto', 'photo']
            : ['sendDocument', 'document'];

        $token = $this->token() ?? throw new RuntimeException('TELEGRAM_BOT_TOKEN не задан');

        return $this->http(json: false)
            ->attach($field, file_get_contents($path), basename($path))
            ->post("https://api.telegram.org/bot{$token}/{$method}", [
                'chat_id' => $chatId,
                'caption' => mb_strimwidth($captionHtml, 0, 1024),
                'parse_mode' => 'HTML',
            ]);
    }

    /** Отправляет заявку во все подписанные чаты. Возвращает ошибки по чатам (пусто — всё ушло). */
    public function notifyLead(Lead $lead): array
    {
        if (! $this->isConfigured()) {
            return ['_' => 'Telegram не настроен: нет токена или chat_id'];
        }

        $text = $this->formatLead($lead);
        $photo = $lead->photo_path && Storage::disk('local')->exists($lead->photo_path)
            ? Storage::disk('local')->path($lead->photo_path)
            : null;
        $errors = [];

        foreach ($this->chatIds() as $chatId) {
            try {
                $response = $photo
                    ? $this->sendFile($chatId, $photo, $text)
                    : $this->sendMessage($chatId, $text);
                if (! $response->json('ok')) {
                    $errors[$chatId] = $response->json('description', 'HTTP '.$response->status());
                }
            } catch (\Throwable $e) {
                $errors[$chatId] = $e->getMessage();
            }
        }

        return $errors;
    }

    public function formatLead(Lead $lead): string
    {
        $e = fn (?string $v) => htmlspecialchars((string) $v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $phoneDigits = preg_replace('/\D+/', '', $lead->phone);

        $lines = [
            '🚗 <b>Ремскол — заявка #'.$lead->id.'</b>',
            '',
            '👤 <b>Имя:</b> '.$e($lead->name),
            '📞 <b>Телефон:</b> <a href="tel:+'.$phoneDigits.'">'.$e($lead->phone).'</a>',
        ];

        if ($lead->service) {
            $lines[] = '🛠 <b>Услуга:</b> '.$e($lead->service);
        }
        if ($lead->comment) {
            $lines[] = '💬 <b>Сообщение:</b> '.$e($lead->comment);
        }
        if ($lead->utm) {
            $lines[] = '📊 <b>UTM:</b> '.$e(http_build_query($lead->utm, '', ', '));
        }

        $lines[] = '';
        $lines[] = '🕒 '.$lead->created_at?->timezone(config('app.timezone'))->format('d.m.Y H:i');

        return implode("\n", $lines);
    }

    public function getMe(): Response
    {
        return $this->call('getMe');
    }

    public function setWebhook(string $url, ?string $secret): Response
    {
        return $this->call('setWebhook', array_filter([
            'url' => $url,
            'secret_token' => $secret,
            'allowed_updates' => ['message'],
            'drop_pending_updates' => true,
        ]));
    }

    public function deleteWebhook(): Response
    {
        return $this->call('deleteWebhook');
    }

    public function getWebhookInfo(): Response
    {
        return $this->call('getWebhookInfo');
    }

    private function call(string $method, array $payload = []): Response
    {
        $token = $this->token() ?? throw new RuntimeException('TELEGRAM_BOT_TOKEN не задан');

        return $this->http()->post("https://api.telegram.org/bot{$token}/{$method}", $payload);
    }

    private function http(bool $json = true): PendingRequest
    {
        $request = Http::timeout(20)->connectTimeout(5)->retry(2, 500, throw: false);
        if ($json) {
            $request = $request->asJson();
        }

        if ($proxy = config('services.telegram.proxy')) {
            $request = $request->withOptions(['proxy' => $proxy]);
        }

        return $request;
    }
}
