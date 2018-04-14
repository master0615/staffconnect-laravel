<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // user table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('fname', 20);
                $table->string('lname', 20);
                $table->string('alias', 20)->nullable();
                $table->string('email')->unique();
                $table->string('password');
                $table->unsignedBigInteger('mob')->nullable();
                $table->string('address', 40)->nullable();
                $table->char('unit', 10)->nullable();
                $table->char('city', 20)->nullable();
                $table->char('state', 20)->nullable();
                $table->char('postcode', 8)->nullable();
                $table->enum('sex', [
                    'male',
                    'female',
                ])->nullable();
                $table->date('dob')->nullable();
                $table->enum('lvl', [
                    'owner',
                    'admin',
                    'staff',
                    'client',
                    'ext',
                    'registrant1', // there are multiple registration steps
                    'registrant2',
                    'registrant3',
                    'registrant4',
                    'registrant5',
                    'registrant6',
                    'registrant7',
                    'registrant8',
                    'rejected',
                    'api',
                ])->default('staff');
                $table->boolean('fav')->default('0'); // global agency favourites. there will also be per admin favourites
                $table->string('ppic_a', 5)->nullable(); // profile pic will be stored as [usr_id]ppic_a.ext ppic_a will be char incremented a,b,c ect used to force browser to refresh image when updated
                $table->string('ustat', 30)->nullable(); // user status 'new', 'interviewed', 'existing' customisable
                $table->double('lat', 11, 7)->nullable();
                $table->double('lon', 11, 7)->nullable();
                $table->enum('geocode_status', [
                    'not_ready', // incomplete address
                    'ready', // ready to attempt
                    'success',
                    'failed',
                    'given_up', // failed twice?
                ])->default('not_ready');
                $table->boolean('profile_updated_notify')->default('0');
                $table->dateTime('last_login')->nullable();
                $table->enum('active', [
                    'active',
                    'inactive',
                    'blacklisted',
                ]);
                $table->boolean('works_here')->default(1); // indicates that user works for this company as opposed to outsourced company
                $table->rememberToken();
                $table->timestamps();
            });
        }

        Schema::create('attribute_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedInteger('attribute_id');
            $table->foreign('attribute_id')
                ->references('id')
                ->on('attributes')
                ->onDelete('cascade');
            $table->dateTime('created_at');
            $table->unsignedInteger('setter_id')->nullable();
            $table->foreign('setter_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        Schema::create('client_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');
        });

        Schema::create('rating_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedInteger('rating_id')
                ->references('id')
                ->on('ratings')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('score')->default(0);
        });

        Schema::create('user_work_area', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedInteger('work_area_id');
            $table->foreign('work_area_id')
                ->references('id')
                ->on('work_areas')
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
        //
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('attribute_user');
        Schema::dropIfExists('rating_user');
        Schema::dropIfExists('user_work_area');
        Schema::dropIfExists('client_user');
        Schema::dropIfExists('users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
