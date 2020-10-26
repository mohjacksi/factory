<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToJobsTable extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedInteger('city_id');
            $table->foreign('city_id', 'city_fk_2472538')->references('id')->on('cities');
            $table->unsignedInteger('specialization_id');
            $table->foreign('specialization_id', 'specialization_fk_2472544')->references('id')->on('specializations');
        });
    }
}
