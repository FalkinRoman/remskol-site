<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Команды бота:
 *  /start            — показать chat_id
 *  /subscribe КОД    — получать заявки в этот чат (КОД = TELEGRAM_SUBSCRIBE_CODE)
 *  /unsubscribe      — перестать получать заявки
 *  /status           — подписан ли чат
 */
class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, TelegramService $telegram): JsonResponse
    {
        $secret = $telegram->webhookSecret();
        if ($secret && ! hash_equals($secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'))) {
            abort(403);
        }

        $message = $request->input('message');
        $chatId = (string) data_get($message, 'chat.id', '');
        $text = trim((string) data_get($message, 'text', ''));

        if ($chatId === '' || $text === '') {
            return response()->json(['ok' => true]);
        }

        [$command, $arg] = array_pad(preg_split('/\s+/', $text, 2), 2, '');
        $command = strtolower(explode('@', $command)[0]);

        $reply = match ($command) {
            '/start' => "Бот заявок с сайта Ремскол.\nВаш chat_id: <code>{$chatId}</code>\n\nЧтобы получать заявки: /subscribe КОД",
            '/subscribe' => $this->subscribe($telegram, $chatId, trim($arg)),
            '/unsubscribe' => $this->unsubscribe($telegram, $chatId),
            '/status' => in_array($chatId, $telegram->chatIds(), true)
                ? '✅ Этот чат получает заявки.'
                : '❌ Этот чат не подписан на заявки.',
            default => null,
        };

        if ($reply !== null) {
            rescue(fn () => $telegram->sendMessage($chatId, $reply), report: true);
        }

        return response()->json(['ok' => true]);
    }

    private function subscribe(TelegramService $telegram, string $chatId, string $code): string
    {
        $expected = $telegram->subscribeCode();

        if (! $expected || ! hash_equals($expected, $code)) {
            return '⛔ Неверный код подписки.';
        }

        $telegram->addChat($chatId);

        return '✅ Готово, заявки с сайта будут приходить в этот чат.';
    }

    private function unsubscribe(TelegramService $telegram, string $chatId): string
    {
        $telegram->removeChat($chatId);

        return 'Чат отписан от заявок.';
    }
}
