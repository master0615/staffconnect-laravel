<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocations extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('chains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cname', 40);
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('chain_id')->nullable();
            $table->foreign('chain_id')
                ->references('id')
                ->on('chains')
                ->onDelete('set null');
            $table->string('lname', 60);
            $table->string('generic_lname', 60)->nullable(); // for hiding actual name from non selected staff
            $table->string('address', 100)->nullable();
            $table->double('lat', 11, 7)->nullable();
            $table->double('lon', 11, 7)->nullable();
            $table->string('location_number', 10)->nullable();
            $table->string('google_place_id')->nullable();
            $table->mediumtext('notes')->nullable();
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
        Schema::dropIfExists('locations');
        Schema::dropIfExists('chains');
    }
}
