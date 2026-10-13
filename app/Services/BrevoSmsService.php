<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BrevoSmsService
{
    /**
     * Envía un SMS transaccional a través de la API de Brevo.
     *
     * @return array{ok: bool, data?: array, error?: string}
     */
    public function send(string $telefono, string $contenido): array
    {
        $apiKey = config('services.brevo.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('No se ha configurado BREVO_API_KEY en el archivo .env.');
        }

        $recipient = $this->normalizarTelefono($telefono);

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post(config('services.brevo.endpoint'), [
            'sender' => config('services.brevo.sms_sender'),
            'recipient' => $recipient,
            'content' => $contenido,
            'type' => 'transactional',
            'unicodeEnabled' => true,
        ]);

        if ($response->failed()) {
            Log::error('Error al enviar SMS con Brevo', [
                'telefono' => $recipient,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'ok' => false,
                'error' => $response->json('message') ?? $response->body(),
            ];
        }

        return [
            'ok' => true,
            'data' => $response->json(),
        ];
    }

    /**
     * Convierte un teléfono de 10 dígitos al formato E.164 requerido por Brevo.
     */
    public function normalizarTelefono(string $telefono): string
    {
        $digits = preg_replace('/\D/', '', $telefono);

        if (strlen($digits) > 10 && str_starts_with($digits, '52')) {
            return '+'.$digits;
        }

        return '+'.config('services.brevo.country_code').$digits;
    }
}
