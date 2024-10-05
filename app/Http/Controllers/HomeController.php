<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        // データを取得
        $highly_rated_restaurants = Restaurant::take(6)->get(); // レビュー機能実装後に並び替え
        $categories = Category::all(); // すべてのカテゴリ
        $new_restaurants = Restaurant::orderBy('created_at', 'desc')->take(6)->get(); // 新しい順に取得

        // ビューにデータを渡す
        return view('home',compact('highly_rated_restaurants', 'categories', 'new_restaurants'));
    }
}
