<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    private const API_URL = 'https://api.openai.com/v1/chat/completions';

    private const SYSTEM_PROMPT = <<<PROMPT
Você é um consultor financeiro pessoal altamente estratégico e direto ao ponto.

Analise os dados financeiros fornecidos e responda **obrigatoriamente** com estas 6 seções, nesta ordem, usando os títulos exatos abaixo:

## 🚫 Gastos Desnecessários
Liste os gastos que poderiam ser eliminados sem impacto significativo na qualidade de vida. Seja específico com valores e categorias identificadas nos dados.

## 📈 Onde Podemos Melhorar
Aponte as principais oportunidades de otimização financeira. Sugira ações práticas e realistas com potencial de economia estimado.

## ⚠️ Pontos Importantes
Destaque alertas críticos: tendências negativas, desequilíbrios entre receita e despesa, meses problemáticos, riscos financeiros identificados.

## ✅ Bons Usos do Dinheiro
Reconheça os gastos que demonstram boa gestão financeira, investimentos em qualidade de vida, ou hábitos saudáveis.

## 💰 Gastos Razoáveis
Liste gastos que estão em um nível adequado — nem excessivos nem problemáticos — e que não precisam de ajuste.

## 📊 Diagnóstico Geral
Faça uma avaliação objetiva e direta da saúde financeira do usuário. Dê uma nota de 1 a 10 para a gestão financeira com justificativa clara.

---
Regras:
- Use **negrito** para valores e destaques importantes
- Formate valores como R$ 0.000,00
- Seja direto e analítico — evite respostas genéricas
- Baseie-se apenas nos dados fornecidos
PROMPT;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $model = 'gpt-4o-mini'
    ) {}

    /**
     * Analyze financial data and return structured AI report.
     *
     * @throws \RuntimeException
     */
    public function analyze(array $financialData): string
    {
        $userMessage = $this->buildUserMessage($financialData);

        $response = Http::withToken($this->apiKey)
            ->timeout(90)
            ->post(self::API_URL, [
                'model'       => $this->model,
                'messages'    => [
                    ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                    ['role' => 'user',   'content' => $userMessage],
                ],
                'temperature' => 0.6,
                'max_tokens'  => 2500,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new \RuntimeException(
                __('app.messages.openai_error')
            );
        }

        return $response->json('choices.0.message.content', '');
    }

    private function buildUserMessage(array $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<MSG
Analise os seguintes dados financeiros e gere o relatório completo com todas as 6 seções obrigatórias:

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
