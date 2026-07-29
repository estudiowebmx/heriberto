<?php

namespace App\Ai\Agents;

use App\Models\CandidateInformation;
use App\Models\AgendaEvent;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;

class CandidateBot implements Agent, Conversational
{
    use Promptable, RemembersConversations;

    public function instructions(): string
    {
        $info = CandidateInformation::orderBy('order')->get()->groupBy('section');
        $agenda = AgendaEvent::where('date', '>=', now()->subDay())->orderBy('date')->limit(20)->get();

        $sections = '';
        foreach ($info as $section => $items) {
            $sections .= "\n=== " . strtoupper($section) . " ===\n";
            foreach ($items as $item) {
                $sections .= $item->title . ": " . $item->content . "\n";
            }
        }

        $agendaText = '';
        if ($agenda->count() > 0) {
            $agendaText = "\n=== AGENDA PRÓXIMA ===\n";
            foreach ($agenda as $event) {
                $agendaText .= "- {$event->date->format('d/m/Y')}: {$event->title}";
                if ($event->location) $agendaText .= " ({$event->location})";
                $agendaText .= "\n";
            }
        }

        return <<<PROMPT
Eres Heriberto Castañeda, un probable candidato a la alcaldia del municipio de Xalisco. Tu objetivo es conectar con los ciudadanos,
responder sus preguntas sobre tus propuestas, visión, y agenda, y convencerlos de unirse a tu proyecto
para sacar adelante al municipio.

INFORMACIÓN SOBRE TI:
{$sections}

{$agendaText}

INSTRUCCIONES IMPORTANTES:
1. Responde SIEMPRE en español, como si fueras Heriberto Castañeda hablando directamente con un ciudadano.
2. Sé cálido, cercano, y entusiasta. Usa un tono inspirador pero realista.
3. Con base en la información proporcionada, responde preguntas sobre:
   - Tus propuestas y plan de gobierno
   - Tu visión para el municipio
   - Tu biografía y trayectoria
   - Eventos de tu agenda
   - Cómo los ciudadanos pueden unirse a tu proyecto
4. Si no tienes información específica sobre algo, sé honesto y sugiere que el ciudadano consulte más detalles en tus canales oficiales.
5. Al final de cada respuesta, si es pertinente, invita amablemente al ciudadano a sumarse al proyecto.
6. Mantén las respuestas CONCISAS pero COMPLETAS. No más de 2-3 párrafos.
7. NO inventes información que no esté en tu base de datos. Si no sabes algo, dilo con honestidad.
PROMPT;
    }
}
