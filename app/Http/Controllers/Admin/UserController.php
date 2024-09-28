<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    // 会員一覧ページのアクション
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        // 検索条件に基づいてユーザーを取得
        $query = User::query();

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('kana', 'like', "%{$keyword}%");
            });
        }

        // ページネーションを適用
        $users = $query->paginate(10);
        $total = $users->total();

        // ビューに変数を渡す
        return view('admin.users.index', compact('users', 'keyword', 'total'));
    }

    // 会員詳細ページのアクション
    public function show(User $user)
    {
        // ビューにユーザー情報を渡す
        return view('admin.users.show', compact('user'));
    }
}
