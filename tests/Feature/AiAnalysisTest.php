<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAnalysisTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ── Page access ───────────────────────────────────────────────────────────

    public function test_page_requires_authentication(): void
    {
        $this->get(route('ai.analysis'))->assertRedirect(route('login'));
    }

    public function test_page_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
             ->get(route('ai.analysis'))
             ->assertOk()
             ->assertViewIs('ai.analysis');
    }

    public function test_page_shows_warning_when_api_key_not_configured(): void
    {
        config(['services.openai.key' => '']);

        $this->actingAs($this->user)
             ->get(route('ai.analysis'))
             ->assertOk()
             ->assertSee('API Key não configurada');
    }

    public function test_page_shows_analyze_button_when_api_key_configured(): void
    {
        config(['services.openai.key' => 'sk-fake-key']);

        $this->actingAs($this->user)
             ->get(route('ai.analysis'))
             ->assertOk()
             ->assertSee('Gerar Análise Financeira');
    }

    // ── Analyze ───────────────────────────────────────────────────────────────

    public function test_analyze_redirects_with_error_when_api_not_configured(): void
    {
        config(['services.openai.key' => '']);

        $this->actingAs($this->user)
             ->post(route('ai.analyze'))
             ->assertRedirect()
             ->assertSessionHas('error');
    }

    public function test_analyze_returns_ai_content_on_success(): void
    {
        config(['services.openai.key' => 'sk-fake-key']);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '## Diagnóstico\nVocê está gastando demais.']],
                ],
            ], 200),
        ]);

        $this->actingAs($this->user)
             ->post(route('ai.analyze'))
             ->assertOk()
             ->assertViewHas('analysis');
    }

    public function test_analyze_shows_error_on_openai_failure(): void
    {
        config(['services.openai.key' => 'sk-fake-key']);

        Http::fake([
            'api.openai.com/*' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $this->actingAs($this->user)
             ->post(route('ai.analyze'))
             ->assertRedirect()
             ->assertSessionHas('error');
    }
}
