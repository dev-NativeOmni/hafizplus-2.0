<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuranPdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }

    public function test_guest_cannot_access_quran_pdf(): void
    {
        $response = $this->get(route('quran.pdf'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_quran_pdf(): void
    {
        $studentUser = User::where('username', 'santri')->first();
        $this->assertNotNull($studentUser);

        $response = $this->actingAs($studentUser)->get(route('quran.pdf'));
        $response->assertStatus(200);
        $response->assertSee('Mushaf Al-Qur\'an Digital');
    }
}
