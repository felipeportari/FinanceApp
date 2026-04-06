<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\OpenAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenAiServiceTest extends TestCase
{
    use RefreshDatabase;

    // ── isConfigured ──────────────────────────────────────────────────────────

    public function test_is_configured_returns_false_when_key_is_empty(): void
    {
        $user = User::factory()->create(['openai_key' => null]);
        $this->actingAs($user);
        $this->assertFalse(OpenAiService::isConfigured());
    }

    public function test_is_configured_returns_false_when_not_authenticated(): void
    {
        $this->assertFalse(OpenAiService::isConfigured());
    }

    public function test_is_configured_returns_true_when_key_is_set(): void
    {
        $user = User::factory()->create(['openai_key' => 'sk-test-key']);
        $this->actingAs($user);
        $this->assertTrue(OpenAiService::isConfigured());
    }

    // ── analyze ───────────────────────────────────────────────────────────────

    public function test_analyze_returns_ai_content_on_success(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '## Análise\nVocê gasta demais em lazer.']],
                ],
            ], 200),
        ]);

        $service = new OpenAiService('sk-fake-key');
        $result = $service->analyze(['total_balance' => -500]);

        $this->assertStringContainsString('Análise', $result);
        $this->assertStringContainsString('lazer', $result);
    }

    public function test_analyze_throws_on_api_failure(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['error' => ['message' => 'Unauthorized']], 401),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/OpenAI|API Key/i');

        $service = new OpenAiService('sk-invalid');
        $service->analyze([]);
    }

    public function test_analyze_sends_bearer_token(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'ok']]],
            ], 200),
        ]);

        $service = new OpenAiService('sk-my-secret-key');
        $service->analyze(['data' => 'test']);

        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization', 'Bearer sk-my-secret-key');
        });
    }

    public function test_analyze_sends_financial_data_in_body(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'ok']]],
            ], 200),
        ]);

        $service = new OpenAiService('sk-test');
        $service->analyze(['total_balance' => 1234.56]);

        Http::assertSent(function (Request $request) {
            $body = $request->data();

            return isset($body['messages']) &&
                   str_contains(json_encode($body['messages']), '1234.56');
        });
    }

    public function test_analyze_uses_configured_model(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'ok']]],
            ], 200),
        ]);

        $service = new OpenAiService('sk-test', 'gpt-4o');
        $service->analyze([]);

        Http::assertSent(function (Request $request) {
            return $request->data()['model'] === 'gpt-4o';
        });
    }
}
