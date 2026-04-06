<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_toggle_requires_authentication(): void
    {
        $this->post(route('theme.toggle'))->assertRedirect(route('login'));
    }

    public function test_theme_toggles_from_light_to_dark(): void
    {
        $user = User::factory()->create(['theme' => 'light']);

        $this->actingAs($user)->post(route('theme.toggle'));

        $this->assertSame('dark', $user->fresh()->theme);
    }

    public function test_theme_toggles_from_dark_to_light(): void
    {
        $user = User::factory()->create(['theme' => 'dark']);

        $this->actingAs($user)->post(route('theme.toggle'));

        $this->assertSame('light', $user->fresh()->theme);
    }

    public function test_theme_toggle_redirects_back(): void
    {
        $user = User::factory()->create(['theme' => 'light']);

        $this->actingAs($user)
            ->post(route('theme.toggle'))
            ->assertRedirect();
    }

    public function test_is_dark_theme_helper_reflects_saved_value(): void
    {
        $light = User::factory()->create(['theme' => 'light']);
        $dark = User::factory()->create(['theme' => 'dark']);

        $this->assertFalse($light->isDarkTheme());
        $this->assertTrue($dark->isDarkTheme());
    }
}
