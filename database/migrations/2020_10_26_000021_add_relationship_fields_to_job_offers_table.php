<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToJobOffersTable extends Migration
{
    public function up()
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->unsignedInteger('specialization_id')->nullable();
            $table->foreign('specialization_id', 'specialization_fk_2472836')->references('id')->on('specializations');
            $table->unsignedInteger('city_id')->nullable();
            $table->foreign('city_id', 'city_fk_2472837')->references('id')->on('cities');
        });
    }
}
