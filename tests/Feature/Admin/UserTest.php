<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    // 会員一覧ページのテストメソッド
    public function test_unauthenticated_user_cannot_access_admin_users_index()
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_cannot_access_admin_users_index()
    {
        $regularUser = User::factory()->create();
        $response = $this->actingAs($regularUser)->get(route('admin.users.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_users_index()
    {
        $adminUser = new Admin();
        $adminUser->email = 'adminuser@example.com';
        $adminUser->password = Hash::make('password');
        $adminUser->save();
        $response = $this->actingAs($adminUser,'admin')->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    // 会員詳細ページのテストメソッド
    public function test_unauthenticated_user_cannot_access_admin_users_show()
    {
        $regularUser = User::factory()->create();
        $response = $this->get(route('admin.users.show', $regularUser));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_cannot_access_admin_users_show()
    {
        $regularUser = User::factory()->create();
        $response = $this->actingAs($regularUser)->get(route('admin.users.show', $regularUser));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_users_show()
    {
        $adminUser = new Admin();
        $adminUser->email = 'adminuser@example.com';
        $adminUser->password = Hash::make('password');
        $adminUser->save();

        $regularUser = User::factory()->create();

        $response = $this->actingAs($adminUser,'admin')->get(route('admin.users.show', $regularUser));
        $response->assertStatus(200);
    }

}
