<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ── Index ──────────────────────────────────────────────────────────────────

    public function test_index_requires_authentication(): void
    {
        $this->get(route('categories.index'))->assertRedirect(route('login'));
    }

    public function test_index_loads_with_categories(): void
    {
        Category::create(['name' => 'Lazer', 'color' => '#8B5CF6', 'icon' => 'tag']);

        $this->actingAs($this->user)
             ->get(route('categories.index'))
             ->assertOk()
             ->assertSee('Lazer');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_loads(): void
    {
        $this->actingAs($this->user)
             ->get(route('categories.create'))
             ->assertOk();
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function test_user_can_create_category(): void
    {
        $this->actingAs($this->user)
             ->post(route('categories.store'), [
                 'name'  => 'Viagens',
                 'color' => '#3B82F6',
                 'icon'  => 'globe',
             ])
             ->assertRedirect(route('categories.index'))
             ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', ['name' => 'Viagens', 'color' => '#3B82F6']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
             ->post(route('categories.store'), [])
             ->assertSessionHasErrors(['name', 'color', 'icon']);
    }

    public function test_store_rejects_duplicate_name(): void
    {
        Category::create(['name' => 'Lazer', 'color' => '#8B5CF6', 'icon' => 'tag']);

        $this->actingAs($this->user)
             ->post(route('categories.store'), [
                 'name'  => 'Lazer',
                 'color' => '#FF0000',
                 'icon'  => 'tag',
             ])
             ->assertSessionHasErrors('name');
    }

    public function test_store_rejects_invalid_hex_color(): void
    {
        $this->actingAs($this->user)
             ->post(route('categories.store'), [
                 'name'  => 'Válida',
                 'color' => 'not-a-color',
                 'icon'  => 'tag',
             ])
             ->assertSessionHasErrors('color');
    }

    public function test_store_rejects_short_name(): void
    {
        $this->actingAs($this->user)
             ->post(route('categories.store'), [
                 'name'  => 'X',
                 'color' => '#3B82F6',
                 'icon'  => 'tag',
             ])
             ->assertSessionHasErrors('name');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function test_user_can_delete_custom_category(): void
    {
        $cat = Category::create(['user_id' => $this->user->id, 'name' => 'Removível', 'color' => '#64748B', 'icon' => 'tag', 'is_default' => false]);

        $this->actingAs($this->user)
             ->delete(route('categories.destroy', $cat))
             ->assertRedirect(route('categories.index'))
             ->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    public function test_cannot_delete_default_category(): void
    {
        $cat = Category::create(['name' => 'Padrão', 'color' => '#64748B', 'icon' => 'tag', 'is_default' => true]);

        $this->actingAs($this->user)
             ->delete(route('categories.destroy', $cat))
             ->assertRedirect()
             ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $cat->id]);
    }

    public function test_cannot_delete_category_with_transactions(): void
    {
        $cat = Category::create(['user_id' => $this->user->id, 'name' => 'Usada', 'color' => '#64748B', 'icon' => 'tag', 'is_default' => false]);

        Transaction::create([
            'user_id'     => $this->user->id,
            'category_id' => $cat->id,
            'type'        => 'expense',
            'amount'      => 100,
            'description' => 'Teste',
            'date'        => now()->format('Y-m-d'),
        ]);

        $this->actingAs($this->user)
             ->delete(route('categories.destroy', $cat))
             ->assertRedirect()
             ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $cat->id]);
    }
}
