<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

     /** @test */
    public function test_guest_cannot_access_user_index() {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_regular_user_cannot_access_user_index()
    {
        $user = User::factory()->create(['role' => 'user']); // 一般ユーザー作成
        $this->actingAs($user);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function test_admin_can_access_user_index()
    {
        $admin = User::factory()->create(['role' => 'admin']); // 管理者作成
        $this->actingAs($admin);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200); // OK
    }

    /** @test */
    public function test_guest_cannot_access_user_show()
    {
        $user = User::factory()->create(); // 任意のユーザー作成
        $response = $this->get(route('admin.users.show', $user));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_regular_user_cannot_access_user_show()
    {
        $user = User::factory()->create(['role' => 'user']); // 一般ユーザー作成
        $this->actingAs($user);

        $targetUser = User::factory()->create(); // 表示するユーザー作成
        $response = $this->get(route('admin.users.show', $targetUser));
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function test_admin_can_access_user_show()
    {
        $admin = User::factory()->create(['role' => 'admin']); // 管理者作成
        $this->actingAs($admin);

        $targetUser = User::factory()->create(); // 表示するユーザー作成
        $response = $this->get(route('admin.users.show', $targetUser));
        $response->assertStatus(200); // OK
    }

}
