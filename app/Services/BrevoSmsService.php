<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BrevoSmsService
{
    /**
     * Máximo de SMS a enviar en paralelo por bloque.
     * El límite de Brevo es 150 req/s; 100 deja margen suficiente.
     */
    private const CHUNK_SIZE = 100;

    /**
     * Segundos que se espera entre bloques para respetar el rate limit de Brevo.
     */
    private const CHUNK_PAUSE_MICROSECONDS = 1000000;

    /**
     * Envía un SMS transaccional a través de la API de Brevo.
     *
     * @return array{ok: bool, http?: ?int, messageId?: ?int, code?: ?string, error?: ?string}
     */
    public function send(string $telefono, string $contenido): array
    {
        $resultado = $this->sendMany([
            ['telefono' => $telefono, 'contenido' => $contenido],
        ]);

        return $resultado[0];
    }

    /**
     * Envía varios SMS de forma concurrente, en bloques, a través de la API de Brevo.
     *
     * @param  array<array-key, array{telefono: string, contenido: string}>  $items
     * @return array<array-key, array{ok: bool, http: ?int, messageId: ?int, code: ?string, error: ?string}>
     */
    public function sendMany(array $items): array
    {
        $apiKey = config('services.brevo.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('No se ha configurado BREVO_API_KEY en el archivo .env.');
        }

        $endpoint = config('services.brevo.endpoint');
        $sender = config('services.brevo.sms_sender');

        $chunks = array_chunk($items, self::CHUNK_SIZE, true);
        $lastIndex = count($chunks) - 1;
        $results = [];

        foreach ($chunks as $index => $chunk) {
            $responses = Http::pool(function ($pool) use ($chunk, $apiKey, $endpoint, $sender) {
                $requests = [];

                foreach ($chunk as $key => $item) {
                    $requests[$key] = $pool->as($key)
                        ->withHeaders([
                            'api-key' => $apiKey,
                            'accept' => 'application/json',
                            'content-type' => 'application/json',
                        ])
                        ->timeout(12)
                        ->connectTimeout(8)
                        ->post($endpoint, [
                            'sender' => $sender,
                            'recipient' => $this->normalizarTelefono($item['telefono']),
                            'content' => $item['contenido'],
                            'type' => 'transactional',
                            'unicodeEnabled' => true,
                        ]);
                }

                return $requests;
            }, self::CHUNK_SIZE);

            foreach ($chunk as $key => $item) {
                $results[$key] = $this->interpretarRespuesta($responses[$key] ?? null, $item['telefono']);
            }

            if ($index < $lastIndex) {
                usleep(self::CHUNK_PAUSE_MICROSECONDS);
            }
        }

        ksort($results);

        return $results;
    }

    /**
     * Interpreta la respuesta (o excepción) de una petición a Brevo.
     *
     * @return array{ok: bool, http: ?int, messageId: ?int, code: ?string, error: ?string}
     */
    private function interpretarRespuesta(mixed $response, string $telefono): array
    {
        $recipient = $this->normalizarTelefono($telefono);

        if ($response instanceof ConnectionException) {
            Log::error('Error de conexión al enviar SMS con Brevo', [
                'telefono' => $recipient,
                'error' => $response->getMessage(),
            ]);

            return [
                'ok' => false,
                'http' => null,
                'messageId' => null,
                'code' => 'connection_error',
                'error' => $response->getMessage(),
            ];
        }

        if (! $response instanceof Response) {
            return [
                'ok' => false,
                'http' => null,
                'messageId' => null,
                'code' => 'unknown_error',
                'error' => 'No se obtuvo una respuesta válida de Brevo.',
            ];
        }

        if ($response->failed()) {
            Log::error('Error al enviar SMS con Brevo', [
                'telefono' => $recipient,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'ok' => false,
                'http' => $response->status(),
                'messageId' => null,
                'code' => $response->json('code'),
                'error' => $response->json('message') ?? $response->body(),
            ];
        }

        return [
            'ok' => true,
            'http' => $response->status(),
            'messageId' => $response->json('messageId'),
            'code' => null,
            'error' => null,
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
