<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeocodeLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // store from google geocode api
        Schema::create('geocode_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('lat',11,7);
            $table->double('lon',11,7);
            $table->string('formatted_address',100);
            $table->string('ca_short',30);
            $table->string('ca_long',50);
            $table->string('aa1_short',30);
            $table->string('aa1_long',50);
            $table->string('aa2_short',30);
            $table->string('aa2_long',50);
            $table->string('aa3_short',30);
            $table->string('aa3_long',50);
            $table->string('country_short',2);
            $table->string('country_long',30);
            $table->string('postal_code',10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('geocode_locations');
    }
}
