<?php

namespace App\Http\Controllers;

use App\Services\FinancialService;
use App\Services\OpenAiService;
use Illuminate\Http\Request;

class AiAnalysisController extends Controller
{
    public function index()
    {
        return view('ai.analysis', [
            'isConfigured' => OpenAiService::isConfigured(),
            'analysis'     => null,
        ]);
    }

    public function analyze(Request $request)
    {
        if (!OpenAiService::isConfigured()) {
            return back()->with('error', 'API Key da OpenAI não configurada. Adicione OPENAI_API_KEY no arquivo .env.');
        }

        try {
            $service  = new FinancialService(auth()->id());
            $payload  = $service->buildAiPayload();

            $openAi   = new OpenAiService(
                apiKey: config('services.openai.key'),
                model:  config('services.openai.model', 'gpt-4o-mini')
            );

            $analysis = $openAi->analyze($payload);

            return view('ai.analysis', [
                'isConfigured' => true,
                'analysis'     => $analysis,
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
