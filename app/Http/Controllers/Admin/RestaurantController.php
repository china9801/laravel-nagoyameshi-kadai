<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RestaurantController extends Controller
{
    // 店舗一覧ページ
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $query = Restaurant::query();

        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        $restaurants = $query->paginate(10);
        $total = $query->count();

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    // 店舗詳細ページ
    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact('restaurant'));
    }

    // 店舗登録ページ
    public function create()
    {
        return view('admin.restaurants.create');
    }

    // 店舗登録機能
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|integer|min:0|max:highest_price',
            'highest_price' => 'required|integer|min:0|gte:lowest_price',
            'postal_code' => 'required|numeric|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|integer|min:0',
        ]);

        $restaurant = new Restaurant($request->except('image'));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('restaurants', 'public');
            $restaurant->image = basename($path);
        } else {
            $restaurant->image = '';
        }

        $restaurant->save();

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
    }

    // 店舗編集ページ
    public function edit(Restaurant $restaurant)
    {
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    // 店舗更新機能
    public function update(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|integer|min:0|max:highest_price',
            'highest_price' => 'required|integer|min:0|gte:lowest_price',
            'postal_code' => 'required|numeric|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|integer|min:0',
        ]);

        $restaurant->fill($request->except('image'));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('restaurants', 'public');
            $restaurant->image = basename($path);
        }

        $restaurant->save();

        return redirect()->route('admin.restaurants.show', $restaurant)->with('flash_message', '店舗を編集しました。');
    }

    // 店舗削除機能
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
}
