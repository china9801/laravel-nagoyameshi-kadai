<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;// 管理者モデルがある場合

class HomeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_guest_can_access_home_page()
    {
        $response = $this->get('/');
    }

    public function test_logged_in_user_can_access_home_page()
    {
        $user = User::factory()->create(); // 一般ユーザーを作成
        $this->actingAs($user);

        $response = $this->get('/');
    }

    public function test_logged_in_admin_cannot_access_home_page()
    {
        $admin = Admin::factory()->create(); // 管理者を作成
        $this->actingAs($admin);

        $response = $this->get('/');
        $response->assertRedirect('/login'); // リダイレクト先を確認（必要に応じて変更）
    }
}
