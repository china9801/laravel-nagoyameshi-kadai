<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // indexアクションのテスト
    public function test_guest_cannot_access_restaurant_index()
    {
        $response = $this->get(route('admin.restaurants.index'));
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーがアクセスできないことを確認
    }

    public function test_logged_in_user_cannot_access_restaurant_index()
    {
        $user = User::factory()->create(); // 一般ユーザーを作成

        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーがアクセスできないことを確認
    }

    public function test_logged_in_admin_can_access_restaurant_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.index'));
        $response->assertStatus(200); // 管理者がアクセスできることを確認
    }

    // showアクションのテスト
    public function test_guest_cannot_access_restaurant_show()
    {
        $restaurant = Restaurant::factory()->create(); // 店舗を作成
        $response = $this->get(route("admin.restaurants.show", $restaurant));
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーがアクセスできないことを確認
    }

    public function test_logged_in_user_cannot_access_restaurant_show()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route("admin.restaurants.show", $restaurant));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーがアクセスできないことを確認
    }

    public function test_logged_in_admin_can_access_restaurant_show()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route("admin.restaurants.show", $restaurant));
        $response->assertStatus(200); // 管理者がアクセスできることを確認
    }

    // createアクションのテスト
    public function test_guest_cannot_access_restaurant_create()
    {
        $response = $this->get(route('admin.restaurants.create'));
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーがアクセスできないことを確認
    }

    public function test_logged_in_user_cannot_access_restaurant_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.create'));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーがアクセスできないことを確認
    }

    public function test_logged_in_admin_can_access_restaurant_create()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();
        
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.create'));
        $response->assertStatus(200); // 管理者がアクセスできることを確認
    }

    // storeアクションのテスト
    public function test_guest_cannot_store_restaurant()
    {
        $restaurant_data = [
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

        $response = $this->post(route('admin.restaurants.store', $restaurant_data));
        $this->assertDatabaseMissing('restaurants', $restaurant_data);
        $response->assertRedirect(route('admin.login')); // 一般ユーザーが登録できないことを確認
    }

    public function test_logged_in_user_cannot_store_restaurant()
    {
        $user = User::factory()->create();

        $restaurant_data = [
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

        $response = $this->actingAs($user)->post(route('admin.restaurants.store', $restaurant_data));
        $this->assertDatabaseMissing('restaurants', $restaurant_data);
        $response->assertRedirect(route('admin.login')); // 一般ユーザーが登録できないことを確認

    }

    public function test_logged_in_admin_can_store_restaurant()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $restaurant_data = [
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

        $response = $this->actingAs($admin, 'admin')->post(route('admin.restaurants.store', $restaurant_data));

        $this->assertDatabaseHas('restaurants', $restaurant_data); // データベースに登録されていることを確認
        $response->assertRedirect(route('admin.restaurants.index')); // 登録後のリダイレクト先を確認
    }

    // editアクションのテスト
    public function test_guest_cannot_access_restaurant_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route("admin.restaurants.edit", $restaurant));
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーがアクセスできないことを確認
    }

    public function test_logged_in_user_cannot_access_restaurant_edit()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route("admin.restaurants.edit", $restaurant));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーがアクセスできないことを確認
    }

    public function test_logged_in_admin_can_access_restaurant_edit()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        // $admin = Admin::factory()->create();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route("admin.restaurants.edit", $restaurant));
        $response->assertStatus(200); // 管理者がアクセスできることを確認
    }

    // updateアクションのテスト
    public function test_guest_cannot_update_restaurant()
    {
        $restaurant_old = Restaurant::factory()->create();
        $restaurant_new = [
            'name' => '更新テスト',
            'description' => '更新テスト',
            'lowest_price' => 2000,
            'highest_price' => 6000,
            'postal_code' => '1111111',
            'address' => '更新テスト',
            'opening_time' => '11:00:00',
            'closing_time' => '21:00:00',
            'seating_capacity' => 60,
        ]; 
        $response = $this->patch(route("admin.restaurants.update", $restaurant_old), $restaurant_new);
        $this->assertDatabaseMissing('restaurants',$restaurant_new);
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーが更新できないことを確認
    }

    public function test_logged_in_user_cannot_update_restaurant()
    {
        $user = User::factory()->create();

        $restaurant_old = Restaurant::factory()->create();
        $restaurant_new = [
            'name' => '更新テスト',
            'description' => '更新テスト',
            'lowest_price' => 2000,
            'highest_price' => 6000,
            'postal_code' => '1111111',
            'address' => '更新テスト',
            'opening_time' => '11:00:00',
            'closing_time' => '21:00:00',
            'seating_capacity' => 60,
        ]; 

        $response = $this->actingAs($user)->patch(route("admin.restaurants.update", $restaurant_old), $restaurant_new);
        $this->assertDatabaseMissing('restaurants',$restaurant_new);
        $response->assertRedirect(route('admin.login')); // 一般ユーザーが更新できないことを確認
    }

    public function test_logged_in_admin_can_update_restaurant()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $restaurant_old = Restaurant::factory()->create();
        $restaurant_new = [
            'name' => '更新テスト',
            'description' => '更新テスト',
            'lowest_price' => 2000,
            'highest_price' => 6000,
            'postal_code' => '1111111',
            'address' => '更新テスト',
            'opening_time' => '11:00:00',
            'closing_time' => '21:00:00',
            'seating_capacity' => 60,
        ]; 

        $response = $this->actingAs($admin, 'admin')->patch(route("admin.restaurants.update", $restaurant_old), $restaurant_new);
        $this->assertDatabaseHas('restaurants', $restaurant_new); // データベースに更新されていることを確認
        $response->assertRedirect(route('admin.restaurants.index')); // 更新後のリダイレクト先を確認
    }

    // destroyアクションのテスト
    public function test_guest_cannot_destroy_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete(route("admin.restaurants.destroy", $restaurant));
        //$this->assertDatabaseHas('restaurants', $restaurant);
        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーが削除できないことを確認
    }

    public function test_logged_in_user_cannot_destroy_restaurant()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->delete(route("admin.restaurants.destroy", $restaurant));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーが削除できないことを確認
    }


    public function test_logged_in_admin_can_destroy_restaurant()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->delete(route("admin.restaurants.destroy", $restaurant));
        //$this->assertDatabaseMissing('restaurants', $restaurant);
        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.restaurants.index')); // 更新後のリダイレクト先を確認
    }

}