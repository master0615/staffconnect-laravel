<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class createGeneral extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cname', 50);
            $table->unsignedTinyInteger('display_order')->nullable();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aname', 50);
            $table->enum('visibility', [ // controls if staff see this or admins only
                'staff',
                'admin',
            ])->default('staff');
            $table->enum('role_default', [ // default role requirement
                'yes',
                'no',
            ])->nullable();
            $table->unsignedTinyInteger('display_order')->nullable();
            $table->unsignedInteger('attribute_cat_id')->nullable();
            $table->foreign('attribute_cat_id')
                ->references('id')
                ->on('attribute_categories')
                ->onDelete('set null');
        });

        // TODO clients
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cname', 50);
        });

        Schema::create('flags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fname', 20);
            $table->string('color', 10);
        });

        // TODO outsource_companies previously agencies
        Schema::create('outsource_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cname', 50);
        });

        // replaces region abbreviation in regions table in v3 'abbr'
        Schema::create('work_area_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cname', 50);
        });

        // replaces 'regions' in v3. Previously used for physical areas staff are willing to travel to for work eg Western Australia but now should also be used for things like eg 'kitchen', 'bar1' etc
        Schema::create('work_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aname', 50);
            $table->string('php_tz', 30)->nullable();
            $table->double('lat', 11, 7)->nullable();
            $table->double('lon', 11, 7)->nullable();
            $table->unsignedInteger('work_area_cat_id')->nullable();
            $table->foreign('work_area_cat_id')
                ->references('id')
                ->on('work_area_categories')
                ->onDelete('set null');
        });

        // track in v3
        Schema::create('tracking_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cname', 30);
            $table->enum('staff_visibility', [
                'hidden',
                'visible',
                'visible_after_selection',
            ])->default('hidden');
            $table->enum('client_visibility', [
                'hidden',
                'visible',
            ])->default('hidden');
            $table->boolean('required')->default(0); // required for shift creation
        });

        // tracko in v3
        Schema::create('tracking_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('oname', 60);
            $table->unsignedInteger('tracking_cat_id');
            $table->foreign('tracking_cat_id')
                ->references('id')
                ->on('tracking_categories')
                ->onDelete('cascade');
            $table->enum('staff_visibility', [
                'all',
                'team',
            ])->default('all'); // controls visibility to all staff or only a selected team
            $table->boolean('active')->default(1);
        });

        // ratings for users eg presentation, punctuality etc
        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rname', 30);
        });

        $procedure = "
            CREATE FUNCTION `distance`(
             lat1  numeric (9,6),
             lon1  numeric (9,6),
             lat2  numeric (9,6),
             lon2  numeric (9,6)
            )  RETURNS   decimal (10,5)
            BEGIN
              DECLARE  x  decimal (20,10);
              DECLARE  pi  decimal (21,20);
              SET  pi = 3.14159265358979323846;
              SET  x = sin( lat1 * pi/180 ) * sin( lat2 * pi/180  ) + cos( lat1 *pi/180 ) * cos( lat2 * pi/180 ) * cos(  abs( (lon2 * pi/180) -  (lon1 *pi/180) ) );
              SET  x = atan( ( sqrt( 1- power( x, 2 ) ) ) / x );
              RETURN  abs(1.852 * 60.0 * ((x/pi)*180) )/ 1.609344;
            END
        ";

        DB::unprepared("DROP FUNCTION IF EXISTS distance");
        DB::unprepared($procedure);
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
        DB::unprepared("DROP FUNCTION IF EXISTS distance");
        Schema::dropIfExists('tracking_options');
        Schema::dropIfExists('tracking_categories');
        Schema::dropIfExists('work_areas');
        Schema::dropIfExists('work_area_categories');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('attribute_categories');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('flags');
        Schema::dropIfExists('outsource_companies');
        Schema::dropIfExists('ratings');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
