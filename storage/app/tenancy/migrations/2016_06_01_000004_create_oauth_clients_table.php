<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthClientsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')
                ->index()
                ->nullable();
            $table->string('name');
            $table->string('secret', 100);
            $table->text('redirect');
            $table->boolean('personal_access_client');
            $table->boolean('password_client');
            $table->boolean('revoked');
            $table->timestamps();
        });

        DB::table('oauth_clients')->insert([
            [
                'id' => '1',
                'name' => 'StaffConnect4 Personal Access Client',
                'secret' => '956LpvNk9O32IzrWWFgyPsr56fgH79LxnrkfyPFc',
                'redirect' => 'http://localhost',
                'personal_access_client' => '1',
                'password_client' => '0',
                'created_at' => '2017-11-28 09:29:56',
                'updated_at' => '2017-11-28 09:29:56',
            ],
            [
                'id' => '2',
                'name' => 'StaffConnect4 Password Grant Client',
                'secret' => 'A50ippEJRLojbAayTaAWeBV56bEvX6P50lTYf2gf',
                'redirect' => 'http://localhost',
                'personal_access_client' => '0',
                'password_client' => '1',
                'created_at' => '2017-11-28 09:29:56',
                'updated_at' => '2017-11-28 09:29:56',
            ],
            [
                'id' => '3',
                'name' => 'StaffConnect4 App',
                'secret' => 'rch6CdTAha6sZ4RJMBS2HKM1Zogl1TVLi8Q3mj',
                'redirect' => 'http://localhost',
                'personal_access_client' => '0',
                'password_client' => '1',
                'created_at' => '2017-11-28 09:29:56',
                'updated_at' => '2017-11-28 09:29:56',
            ],
            [
                'id' => '4',
                'name' => 'StaffConnect4 Password Grant Client',
                'secret' => 'mF6hvTJo0JX5auZExkBsU9ZcnUJBbfwVnIgAmvNj',
                'redirect' => 'https://api.formsigner.com/auth/staffconnect/callback',
                'personal_access_client' => '1',
                'password_client' => '0',
                'created_at' => '2017-11-28 09:29:56',
                'updated_at' => '2017-11-28 09:29:56',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('oauth_clients');
    }
}
