<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_restaurant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();//外部キー制約（constrained()）、参照先のデータが削除されると参照元のデータも同時に削除（cascadeOnDelete()）
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();//cascade＝「階段のように連なる滝」のこと。1つのデータが削除されると連続して他のデータも削除されるイメージ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_restaurant');
    }
};
