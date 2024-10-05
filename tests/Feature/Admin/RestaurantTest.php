<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // indexアクションのテスト
    public function test_guest_cannot_access_restaurant_index()
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/login'); // 未ログインのユーザーがアクセスできないことを確認
    }

    public function test_logged_in_user_cannot_access_restaurant_index()
    {
        $user = User::factory()->create(); // 一般ユーザーを作成
        $this->actingAs($user);

        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/login'); // 一般ユーザーがアクセスできないことを確認
    }

    public function test_logged_in_admin_can_access_restaurant_index()
    {
        $admin = Admin::factory()->create(); // 管理者を作成
        $this->actingAs($admin);

        $response = $this->get('/admin/restaurants');
        $response->assertStatus(200); // 管理者がアクセスできることを確認
    }

    // showアクションのテスト
    public function test_guest_cannot_access_restaurant_show()
    {
        $restaurant = Restaurant::factory()->create(); // 店舗を作成
        $response = $this->get("/admin/restaurants/{$restaurant->id}");
        $response->assertRedirect('/login'); // 未ログインのユーザーがアクセスできないことを確認
    }

    public function test_logged_in_user_cannot_access_restaurant_show()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();
        $response = $this->get("/admin/restaurants/{$restaurant->id}");
        $response->assertRedirect('/login'); // 一般ユーザーがアクセスできないことを確認
    }

    public function test_logged_in_admin_can_access_restaurant_show()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin);

        $restaurant = Restaurant::factory()->create();
        $response = $this->get("/admin/restaurants/{$restaurant->id}");
        $response->assertStatus(200); // 管理者がアクセスできることを確認
    }

    // createアクションのテスト
    public function test_guest_cannot_access_restaurant_create()
    {
        $response = $this->get('/admin/restaurants/create');
        $response->assertRedirect('/login'); // 未ログインのユーザーがアクセスできないことを確認
    }

    public function test_logged_in_user_cannot_access_restaurant_create()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/restaurants/create');
        $response->assertRedirect('/login'); // 一般ユーザーがアクセスできないことを確認
    }

    public function test_logged_in_admin_can_access_restaurant_create()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/restaurants/create');
        $response->assertStatus(200); // 管理者がアクセスできることを確認
    }

    // storeアクションのテスト
    public function test_guest_cannot_store_restaurant()
    {
        $response = $this->post('/admin/restaurants', []);
        $response->assertRedirect('/login'); // 未ログインのユーザーが登録できないことを確認
    }

    public function test_logged_in_user_cannot_store_restaurant()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/admin/restaurants', []);
        $response->assertRedirect('/login'); // 一般ユーザーが登録できないことを確認
    }

    public function test_logged_in_admin_can_store_restaurant()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin);

        $restaurantData = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ];

        $response = $this->post('/admin/restaurants', $restaurantData);
        $response->assertRedirect('/admin/restaurants'); // 登録後のリダイレクト先を確認
        $this->assertDatabaseHas('restaurants', $restaurantData); // データベースに登録されていることを確認
    }

    // editアクションのテスト
    public function test_guest_cannot_access_restaurant_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get("/admin/restaurants/{$restaurant->id}/edit");
        $response->assertRedirect('/login'); // 未ログインのユーザーがアクセスできないことを確認
    }

    public function test_logged_in_user_cannot_access_restaurant_edit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();
        $response = $this->get("/admin/restaurants/{$restaurant->id}/edit");
        $response->assertRedirect('/login'); // 一般ユーザーがアクセスできないことを確認
    }

    public function test_logged_in_admin_can_access_restaurant_edit()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin);

        $restaurant = Restaurant::factory()->create();
        $response = $this->get("/admin/restaurants/{$restaurant->id}/edit");
        $response->assertStatus(200); // 管理者がアクセスできることを確認
    }

    // updateアクションのテスト
    public function test_guest_cannot_update_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->put("/admin/restaurants/{$restaurant->id}", []);
        $response->assertRedirect('/login'); // 未ログインのユーザーが更新できないことを確認
    }

    public function test_logged_in_user_cannot_update_restaurant()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();
        $response = $this->put("/admin/restaurants/{$restaurant->id}", []);
        $response->assertRedirect('/login'); // 一般ユーザーが更新できないことを確認
    }

    public function test_logged_in_admin_can_update_restaurant()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin);

        $restaurant = Restaurant::factory()->create();
        $updatedData = [
            'name' => '更新テスト',
            'description' => '更新テスト',
            'lowest_price' => 1500,
            'highest_price' => 5500,
            'postal_code' => '0000001',
            'address' => '更新テスト',
            'opening_time' => '11:00:00',
            'closing_time' => '21:00:00',
            'seating_capacity' => 60,
        ];

        $response = $this->put("/admin/restaurants/{$restaurant->id}", $updatedData);
        $response->assertRedirect('/admin/restaurants'); // 更新後のリダイレクト先を確認
        $this->assertDatabaseHas('restaurants', $updatedData); // データベースに更新されていることを確認
    }

    // destroyアクションのテスト
    public function test_guest_cannot_destroy_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete("/admin/restaurants/{$restaurant->id}");
        $response->assertRedirect('/login'); // 未ログインのユーザーが削除できないことを確認
    }

    public function test_logged_in_user_cannot_destroy_restaurant()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();
        $response = $this->delete("/admin/restaurants/{$restaurant->id}");
        $response->assertRedirect('/login'); // 一般ユーザーが削除できないことを確認
    }


}
