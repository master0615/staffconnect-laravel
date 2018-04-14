<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTerms extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('terms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tname', 30);
            $table->mediumText('terms');
            $table->boolean('active')->default(1);
        });
        
        Schema::create('terms_apply_lvls', function (Blueprint $table) { // which user types do the terms apply to
            $table->increments('id');
            $table->unsignedInteger('term_id');
            $table->foreign('term_id')
                ->references('id')
                ->on('terms')
                ->onDelete('cascade');
            $table->enum('user_lvl', [
                'owner',
                'admin',
                'staff',
                'client',
                'ext',
                'registrant',
                'api'
            ])->default('staff');
        });
        
        Schema::create('term_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('term_id');
            $table->foreign('term_id')
                ->references('id')
                ->on('terms')
                ->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->dateTime('created_at');
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
        Schema::dropIfExists('term_user');
        Schema::dropIfExists('terms_apply_lvls');
        Schema::dropIfExists('terms');
    }
}
