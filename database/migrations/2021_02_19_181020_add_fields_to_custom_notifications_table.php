<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCustomNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_notifications', function (Blueprint $table) {
            //
            $table->string('model_type')->nullable()->after('city_id');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_notifications', function (Blueprint $table) {
            //
        });
    }
}
