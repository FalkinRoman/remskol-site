<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Jobs\SendLeadToTelegram;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    private const UTM_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'yclid', 'gclid'];

    public function store(StoreLeadRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        $utm = collect((array) json_decode((string) $request->input('utm'), true))
            ->only(self::UTM_KEYS)
            ->map(fn ($v) => Str::limit((string) $v, 200, ''))
            ->filter()
            ->all();

        $photoPath = $request->file('photo')?->store('leads/'.now()->format('Y/m'), 'local');

        $lead = Lead::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'service' => $data['service'] ?? null,
            'comment' => $data['comment'] ?? null,
            'photo_path' => $photoPath ?: null,
            'utm' => $utm ?: null,
            'page' => Str::limit((string) $request->headers->get('referer'), 500, ''),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
        ]);

        SendLeadToTelegram::dispatchAfterResponse($lead);

        $message = 'Спасибо! Заявка принята, мы перезвоним вам в ближайшее время.';

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $message]);
        }

        return redirect()->to(url('/'))
            ->with(['lead_success' => $message, 'lead_form' => 'modal']);
    }
}
