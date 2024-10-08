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
         //検索ボックスに入力されたキーワードを取得する
        $keyword = $request->input('keyword');

        // キーワードが存在すれば検索を行い、そうでなければ全件取得する
        if ($keyword) {
            $users = User::where('name', 'like', "%{$keyword}%")->orWhere('kana', 'like', "%{$keyword}%")->paginate(15);
        } else {
            $users = User::paginate(15);
        }

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
