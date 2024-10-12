<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // カテゴリ一覧ページ
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');//検索してる

        if ($keyword) {
            $categories = Category::where('name', 'like', "%{$keyword}%")->paginate(10);
        } else {
            $categories = Category::paginate(10);
        }

        $total = $categories->total();

        return view('admin.categories.index', compact('categories', 'keyword', 'total'));
    }

    
    // カテゴリ登録機能
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $category = new Category(); //モデルのカテゴリのクラスをインスタンス
        $category->name = $request->input('name');//左のnameはカラム名 右のinputの中のnameはveiwのformのnameの値
        $category->save();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを登録しました。');
    }

    // カテゴリ更新機能
    public function update(Request $request,Category $category)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $category->name = $request->input('name');
        $category->save();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを編集しました。');
    }

    // カテゴリ削除機能
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを削除しました。');
    }

}
