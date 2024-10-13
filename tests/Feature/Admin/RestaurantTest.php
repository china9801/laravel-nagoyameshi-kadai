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
    // 未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_guest_cannot_access_restaurant_index()
    {
        $response = $this->get(route('admin.restaurants.index'));
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーがアクセスできないことを確認
    }
     // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_logged_in_user_cannot_access_restaurant_index()
    {
        $user = User::factory()->create(); // 一般ユーザーを作成

        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーがアクセスできないことを確認
    }
    // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
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
    // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_guest_cannot_access_restaurant_show()
    {
        $restaurant = Restaurant::factory()->create(); // 店舗を作成
        $response = $this->get(route("admin.restaurants.show", $restaurant));
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーがアクセスできないことを確認
    }

    // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_logged_in_user_cannot_access_restaurant_show()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route("admin.restaurants.show", $restaurant));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーがアクセスできないことを確認
    }

    // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
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
    // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_guest_cannot_access_restaurant_create()
    {
        $response = $this->get(route('admin.restaurants.create'));
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーがアクセスできないことを確認
    }

    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_logged_in_user_cannot_access_restaurant_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.create'));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーがアクセスできないことを確認
    }

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
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
    // 未ログインのユーザーは店舗を登録できない
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

    public function test_general_user_cannot_store_restaurant()
{
    // 一般ユーザーとしてログイン
    $this->actingAs($this->generalUser);

    $restaurant_data = [
        'name' => 'Unauthorized Restaurant',
        'category_ids' => $this->categories->pluck('id')->toArray(),
    ];

    // 店舗を登録しようとする
    $response = $this->post(route('restaurant.store'), $restaurant_data);

    // ステータスコードが403であることを検証
    $response->assertStatus(403);
}



    // ログイン済みの一般ユーザーは店舗を登録できない
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

    // ログイン済みの管理者は店舗を登録できる
    public function test_logged_in_admin_can_store_restaurant()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $categories = Category::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();

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


        unset($restaurantData['category_ids']);
        $this->assertDatabaseHas('restaurants', $restaurantData);// データベースに登録されていることを確認

        foreach ($categoryIds as $categoryId) {
        $this->assertDatabaseHas('category_restaurant', [
            'category_id' => $categoryId,
        ]);
        }
        
        $response->assertRedirect(route('admin.restaurants.index')); // 登録後のリダイレクト先を確認
    }

    // editアクションのテスト
    // 未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_guest_cannot_access_restaurant_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route("admin.restaurants.edit", $restaurant));
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーがアクセスできないことを確認
    }

    // ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_logged_in_user_cannot_access_restaurant_edit()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route("admin.restaurants.edit", $restaurant));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーがアクセスできないことを確認
    }

    // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_logged_in_admin_can_access_restaurant_edit()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route("admin.restaurants.edit", $restaurant));
        $response->assertStatus(200); // 管理者がアクセスできることを確認
    }

    // updateアクションのテスト
     // 未ログインのユーザーは店舗を更新できない
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

    // ログイン済みの管理者は店舗を更新できる
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
        $response->assertRedirect(route('admin.restaurants.show', $restaurant_old));  //更新後のリダイレクト先,ここをコントローラーと同じになるよう変更
    }

    // destroyアクションのテスト
    // 未ログインのユーザーは店舗を削除できない
    public function test_guest_cannot_destroy_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete(route("admin.restaurants.destroy", $restaurant));
        //$this->assertDatabaseHas('restaurants', $restaurant);
        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーが削除できないことを確認
    }

    // ログイン済みの一般ユーザーは店舗を削除できない
    public function test_logged_in_user_cannot_destroy_restaurant()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->delete(route("admin.restaurants.destroy", $restaurant));
        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.login')); // 一般ユーザーが削除できないことを確認
    }

    // ログイン済みの管理者は店舗を削除できる
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

    //ログイン済みの一般ユーザーは店舗を登録できない
    public function test_user_cannot_setting_admin_restaurants(){
    $user = User::factory()->create();

    $categories = Category::factory()->count(3)->create();
    $categoryIds = $categories->pluck('id')->toArray();

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
        'category_ids' => $categoryIds
    ];

    $response = $this->actingAs($user)->post(route('admin.restaurants.store'), $restaurantData);
    
    unset($restaurantData['category_ids']);
    $this->assertDatabaseMissing('restaurants', $restaurantData);
    $response->assertRedirect(route('admin.login'));
}


}