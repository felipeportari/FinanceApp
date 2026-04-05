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
    private User $userWithKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user        = User::factory()->create(['openai_key' => null]);
        $this->userWithKey = User::factory()->create(['openai_key' => 'sk-fake-key']);
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
        $this->actingAs($this->user)
             ->get(route('ai.analysis'))
             ->assertOk()
             ->assertSee('API Key não configurada');
    }

    public function test_page_shows_analyze_button_when_api_key_configured(): void
    {
        $this->actingAs($this->userWithKey)
             ->get(route('ai.analysis'))
             ->assertOk()
             ->assertSee('Gerar Análise Financeira');
    }

    // ── Analyze ───────────────────────────────────────────────────────────────

    public function test_analyze_redirects_with_error_when_api_not_configured(): void
    {
        $this->actingAs($this->user)
             ->post(route('ai.analyze'))
             ->assertRedirect()
             ->assertSessionHas('error');
    }

    public function test_analyze_returns_ai_content_on_success(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '## Diagnóstico\nVocê está gastando demais.']],
                ],
            ], 200),
        ]);

        $this->actingAs($this->userWithKey)
             ->post(route('ai.analyze'))
             ->assertOk()
             ->assertViewHas('analysis');
    }

    public function test_analyze_shows_error_on_openai_failure(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $this->actingAs($this->userWithKey)
             ->post(route('ai.analyze'))
             ->assertRedirect()
             ->assertSessionHas('error');
    }
}
