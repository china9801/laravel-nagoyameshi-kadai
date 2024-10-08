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
    // 未ログインのユーザーは管理者側の会員一覧ページにアクセスできない
    public function test_unauthenticated_user_cannot_access_admin_users_index()
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会員一覧ページにアクセスできない
    public function test_regular_user_cannot_access_admin_users_index()
    {
        $regularUser = User::factory()->create();
        $response = $this->actingAs($regularUser)->get(route('admin.users.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会員一覧ページにアクセスできる
    public function test_admin_user_can_access_admin_users_index()
    {
        $admin = new Admin();
        $admin->password = Hash::make('password');
        $admin->save();
        $admin->email = 'admin@example.com';

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは管理者側の会員詳細ページにアクセスできない
    public function test_unauthenticated_user_cannot_access_admin_users_show()
    {
        $regularUser = User::factory()->create();
        $response = $this->get(route('admin.users.show', $regularUser));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会員詳細ページにアクセスできない
    public function test_regular_user_cannot_access_admin_users_show()
    {
        $regularUser = User::factory()->create();
        $response = $this->actingAs($regularUser)->get(route('admin.users.show', $regularUser));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会員詳細ページにアクセスできる
    public function test_admin_user_can_access_admin_users_show()
    {
        $adminUser = new Admin();
        $adminUser->email = 'admin@example.com';
        $adminUser->password = Hash::make('nagoyameshi');
        $adminUser->save();

        $regularUser = User::factory()->create();

        $response = $this->actingAs($admin,'admin')->get(route('admin.users.show', $regularUser));
        
        $response->assertStatus(200);
    }

}
