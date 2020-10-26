<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToDepartmentsTable extends Migration
{
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->unsignedInteger('city_id');
            $table->foreign('city_id', 'city_fk_2472362')->references('id')->on('cities');
            $table->unsignedInteger('category_id')->nullable();
            $table->foreign('category_id', 'category_fk_2472418')->references('id')->on('categories');
        });
    }
}
