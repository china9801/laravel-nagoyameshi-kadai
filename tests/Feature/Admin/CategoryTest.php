<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_categories_index()
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_user_cannot_access_admin_categories_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_admin_can_access_admin_categories_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }

// storeアクションのテスト
    //未ログインのユーザーはカテゴリを登録できない
    public function test_guest_cannot_access_store_category()
    {
        $user = User::factory()->create();

        $category_data = ['name' => 'テスト',];

        $response = $this->actingAs($user)->post(route('admin.categories.store'),$category_data);//データ登録するときはpost　更新する時はpatch
        
        $this->assertDatabaseMissing('categories', $category_data);
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーがアクセスできないことを確認
    }

    //ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_logged_in_user_cannot_access_store_category()
    {
        $user = User::factory()->create();

        $category_data = ['name' => 'テスト',];

        $response = $this->actingAs($user)->get(route('admin.categories.store'), $category_data);
        $this->assertDatabaseMissing('categories', $category_data);
        $response->assertRedirect(route('admin.login')); // 一般ユーザーがアクセスできないことを確認
    }

    // ログイン済みの管理者はカテゴリを登録できる
    public function test_admin_can_access_admin_categories_store()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $category_data = [
            'name' => 'テスト',
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), $category_data);

        $this->assertDatabaseHas('categories', $category_data);
        $response->assertRedirect(route('admin.categories.index'));
    }
// updateアクションのテスト
    //未ログインのユーザーはカテゴリを更新できない
    public function test_guest_cannot_update_category()
    {
        $category_old = Category::factory()->create();

        $category_new = [
            'name' => '更新テスト',
        ]; 
        $response = $this->patch(route("admin.categories.update", $category_old), $category_new);
        $this->assertDatabaseMissing('categories',$category_new);
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーが更新できないことを確認
    }
    
    //ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_logged_in_user_cannot_update_category()
    {
        $user = User::factory()->create();

        $category_old = Category::factory()->create();
        $category_new = [
            'name' => '更新テスト',
        ]; 

        $response = $this->actingAs($user)->patch(route("admin.categories.update", $category_old), $category_new);
        $this->assertDatabaseMissing('categories',$category_new);
        $response->assertRedirect(route('admin.login')); // 一般ユーザーが更新できないことを確認
    }

    //ログイン済みの管理者はカテゴリを更新できる
    public function test_logged_in_admin_can_update_category()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $category_old = Category::factory()->create();
        $category_new = [
            'name' => '更新テスト',
        ]; 

        $response = $this->actingAs($admin, 'admin')->patch(route("admin.categories.update", $category_old), $category_new);
        $this->assertDatabaseHas('categories', $category_new); // データベースに更新されていることを確認
        $response->assertRedirect(route('admin.categories.index'));  //更新後のリダイレクト先,ここをコントローラーと同じになるよう変更
    }

// destroyアクションのテスト
    //未ログインのユーザーはカテゴリを削除できない
    public function test_guest_cannot_destroy_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $response = $this->delete(route("admin.categories.destroy", $category));
        $response->assertRedirect(route('admin.login')); // 未ログインのユーザーが削除できないことを確認
    }

    //ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_logged_in_user_cannot_destroy_category()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();
        $response = $this->actingAs($user)->delete(route("admin.categories.destroy", $category));
        $response->assertRedirect(route('admin.login')); // 一般ユーザーが削除できないことを確認
    }

    //ログイン済みの管理者はカテゴリを削除できる
    public function test_logged_in_admin_can_destroy_category()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $category = Category::factory()->create();
        $response = $this->actingAs($admin, 'admin')->delete(route("admin.categories.destroy", $category));
        //$this->assertDatabaseMissing('categories', $category);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.categories.index')); // 更新後のリダイレクト先を確認
    }


}
