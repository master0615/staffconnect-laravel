<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayLevels extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('pay_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cname', 20);
        });
        
        Schema::create('pay_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pay_cat_id');
            $table->foreign('pay_cat_id')
                ->references('id')
                ->on('pay_categories')
                ->onDelete('cascade');
            $table->string('pname', 20);
            $table->double('pay_rate', 10, 2);
            $table->enum('pay_rate_type', [
                'phr',
                'flat'
            ])->default('phr');
        });
        
        Schema::create('pay_level_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedInteger('pay_level_id');
            $table->foreign('pay_level_id')
                ->references('id')
                ->on('pay_levels')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_level_user');
        Schema::dropIfExists('pay_levels');
        Schema::dropIfExists('pay_categories');
    }
}
