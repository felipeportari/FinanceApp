<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    private const API_URL = 'https://api.openai.com/v1/chat/completions';

    private const SYSTEM_PROMPT = <<<PROMPT
Você é um consultor financeiro pessoal altamente estratégico e direto ao ponto.

Analise os dados financeiros fornecidos e responda com:

1. Principais erros financeiros do usuário
2. Principais acertos financeiros
3. Categorias onde há desperdício
4. Sugestões práticas de economia
5. Oportunidades de aumento de lucro
6. Um diagnóstico geral (crítico e objetivo)

Seja direto, analítico e profissional. Evite respostas genéricas.
Formate sua resposta com títulos claros usando markdown (## para seções, **negrito** para destaques).
Use valores monetários no formato R$ 0.000,00.
PROMPT;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $model = 'gpt-4o-mini'
    ) {}

    /**
     * Analyze financial data and return AI insights.
     *
     * @throws \RuntimeException
     */
    public function analyze(array $financialData): string
    {
        $userMessage = $this->buildUserMessage($financialData);

        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post(self::API_URL, [
                'model'       => $this->model,
                'messages'    => [
                    ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                    ['role' => 'user',   'content' => $userMessage],
                ],
                'temperature' => 0.7,
                'max_tokens'  => 2000,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new \RuntimeException(
                'Falha ao conectar com a OpenAI. Verifique sua API Key nas configurações.'
            );
        }

        return $response->json('choices.0.message.content', 'Não foi possível gerar análise.');
    }

    private function buildUserMessage(array $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<MSG
Analise os seguintes dados financeiros e forneça insights estratégicos:

```json
{$json}
```
MSG;
    }

    public static function isConfigured(): bool
    {
        return !empty(auth()->user()?->openai_key);
    }
}
