<?php

namespace App\Http\Controllers;

use App\Models\AiReport;
use App\Services\FinancialService;
use App\Services\OpenAiService;
use Illuminate\Http\Request;

class AiAnalysisController extends Controller
{
    public function index()
    {
        $reports = AiReport::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('ai.analysis', [
            'isConfigured' => OpenAiService::isConfigured(),
            'reports' => $reports,
        ]);
    }

    public function show(AiReport $report)
    {
        abort_if($report->user_id !== auth()->id(), 403);

        return view('ai.show', compact('report'));
    }

    /**
     * Called via fetch() from the frontend.
     * Returns JSON so the client can handle success/error without page reload.
     */
    public function store(Request $request)
    {
        if (! OpenAiService::isConfigured()) {
            return response()->json([
                'error' => __('app.messages.api_not_configured'),
            ], 422);
        }

        set_time_limit(120);

        try {
            $service = new FinancialService(auth()->id());
            $payload = $service->buildAiPayload();

            $openAi = new OpenAiService(
                apiKey: auth()->user()->openai_key,
                model: config('services.openai.model', 'gpt-4o-mini'),
            );

            $content = $openAi->analyze($payload);

            if (empty(trim($content))) {
                return response()->json([
                    'error' => __('app.messages.openai_error'),
                ], 500);
            }

            $report = AiReport::create([
                'user_id' => auth()->id(),
                'content' => $content,
            ]);

            return response()->json([
                'redirect' => route('ai.show', $report),
            ]);

        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => __('app.messages.openai_error')], 500);
        }
    }

    public function destroy(AiReport $report)
    {
        abort_if($report->user_id !== auth()->id(), 403);

        $report->delete();

        return redirect()->route('ai.analysis')
            ->with('success', __('app.ai.report_deleted'));
    }
}
