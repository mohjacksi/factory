<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToOffersTable extends Migration
{
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedInteger('category_id');
            $table->foreign('category_id', 'category_fk_2472459')->references('id')->on('categories');
            $table->unsignedInteger('trader_id')->nullable();
            $table->foreign('trader_id', 'trader_fk_2504442')->references('id')->on('traders');
        });
    }
}
