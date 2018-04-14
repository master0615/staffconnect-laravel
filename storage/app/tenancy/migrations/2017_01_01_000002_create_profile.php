<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfile extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // categories for extra profile elements. cname displays as category header name on profile
        if (!Schema::hasTable('profile_categories')) {
            Schema::create('profile_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('cname', 40);
                $table->boolean('deletable')->default('1');
                $table->unsignedTinyInteger('display_order')->nullable(); // display order on profile
                $table->unsignedInteger('profile_cat_id')->nullable();
                $table->foreign('profile_cat_id')
                    ->references('id')
                    ->on('profile_categories')
                    ->onDelete('set null');
            });
        }

        // extra profile elements for custom fields eg height weight
        if (!Schema::hasTable('profile_elements')) {
            Schema::create('profile_elements', function (Blueprint $table) {
                $table->increments('id');
                $table->string('ename', 60);
                $table->enum('etype', [ // type of element eg choose from drop down list, enter date
                    'short',
                    'medium',
                    'long',
                    'list',
                    'date',
                    'number',
                    'listm',
                ])->default('short');
                $table->boolean('editable')->default('1'); // can this element be edited or is it a system one
                $table->boolean('deletable')->default('1'); // can this element be deleted or is it a system one
                $table->enum('visibility', [ // visiblity control
                    'optional',
                    'required',
                    'hidden',
                    'pay',
                ])->default('optional');
                $table->unsignedTinyInteger('display_order')->nullable(); // display order on profile
                $table->enum('sex', [
                    'male',
                    'female',
                ])->nullable(); // some elements are for males only or females only. null for both
                $table->enum('filter', [
                    'equals',
                    'range',
                ])->default('equals'); // should this be filterd as equals? eg sex = male or as a range eg height > 6'0
                $table->unsignedInteger('profile_cat_id')->nullable();
                $table->foreign('profile_cat_id')
                    ->references('id')
                    ->on('profile_categories')
                    ->onDelete('set null');
            });
        }

        // options for profile list types
        if (!Schema::hasTable('profile_list_options')) {
            Schema::create('profile_list_options', function (Blueprint $table) {
                $table->increments('id');
                $table->string('option', 40);
                $table->unsignedTinyInteger('display_order')->nullable();
                $table->unsignedInteger('profile_element_id');
                $table->foreign('profile_element_id')
                    ->references('id')
                    ->on('profile_elements')
                    ->onDelete('cascade');
            });
        }

        // TODO add ethnicities, languages

        // user profile data for extra profile elements
        if (!Schema::hasTable('profile_data')) {
            Schema::create('profile_data', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->string('data')->nullable();
                $table->unsignedInteger('profile_element_id')
                    ->references('id')
                    ->on('profile_element')
                    ->onDelete('cascade');
            });
        }

        // profile photo categories
        if (!Schema::hasTable('profile_photo_categories')) {
            Schema::create('profile_photo_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('cname', 40);
            });
        }

        // user profile photos
        if (!Schema::hasTable('profile_photos')) {
            Schema::create('profile_photos', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->string('ext', 4);
                $table->unsignedTinyInteger('display_order')->nullable(); // display order on profile
                $table->boolean('main')->default('0'); // main photo
                $table->boolean('locked')->default('0'); // when locked staff cannot delete
                $table->boolean('admin_only')->default('0'); // when admin only then invisible to staff. used to be seperate table 'apics' in v3
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('profile_photo_cat_link')) {
            Schema::create('profile_photo_cat_link', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('profile_photo_id');
                $table->foreign('profile_photo_id')
                    ->references('id')
                    ->on('profile_photos')
                    ->onDelete('cascade');
                $table->unsignedInteger('profile_photo_cat_id');
                $table->foreign('profile_photo_cat_id')
                    ->references('id')
                    ->on('profile_photo_categories')
                    ->onDelete('cascade');
            });
        }

        // profile video categories
        if (!Schema::hasTable('profile_video_categories')) {
            Schema::create('profile_video_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('cname', 40);
            });
        }

        // user profile videos
        if (!Schema::hasTable('profile_videos')) {
            Schema::create('profile_videos', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->string('ext', 4);
                $table->unsignedTinyInteger('display_order')->nullable(); // display order on profile
                $table->boolean('locked')->default('0'); // when locked staff cannot delete
                $table->boolean('admin_only')->default('0'); // when admin only then invisible to staff.
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('profile_video_cat_link')) {
            Schema::create('profile_video_cat_link', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('profile_video_id');
                $table->foreign('profile_video_id')
                    ->references('id')
                    ->on('profile_videos')
                    ->onDelete('cascade');
                $table->unsignedInteger('profile_video_cat_id');
                $table->foreign('profile_video_cat_id')
                    ->references('id')
                    ->on('profile_video_categories')
                    ->onDelete('cascade');
            });
        }

        // profile photo categories
        if (!Schema::hasTable('profile_document_categories')) {
            Schema::create('profile_document_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('cname', 40);
            });
        }

        // user profile docs
        if (!Schema::hasTable('profile_documents')) {
            Schema::create('profile_documents', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->string('ext', 4);
                $table->string('oname', 100); // original name
                $table->unsignedTinyInteger('display_order')->nullable(); // display order on profile
                $table->boolean('locked')->default('0'); // when locked staff cannot delete
                $table->boolean('admin_only')->default('0'); // when admin only then invisible to staff. used to be seperate table 'apics' in v3
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('profile_document_cat_link')) {
            Schema::create('profile_document_cat_link', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('profile_document_id');
                $table->foreign('profile_document_id')
                    ->references('id')
                    ->on('profile_documents')
                    ->onDelete('cascade');
                $table->unsignedInteger('profile_document_cat_id');
                $table->foreign('profile_document_cat_id')
                    ->references('id')
                    ->on('profile_document_categories')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('profile_admin_notes')) {
            Schema::create('profile_admin_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->unsignedInteger('creator_id')->nullable();
                $table->foreign('creator_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
                $table->string('note');
                $table->enum('type', [
                    'info',
                    'interview',
                    'system',
                    'positive',
                    'negative',
                ])->default('info');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_admin_notes');
        Schema::dropIfExists('profile_video_cat_link');
        Schema::dropIfExists('profile_photo_cat_link');
        Schema::dropIfExists('profile_document_cat_link');

        Schema::dropIfExists('profile_videos');
        Schema::dropIfExists('profile_photos');
        Schema::dropIfExists('profile_documents');

        Schema::dropIfExists('profile_video_categories');
        Schema::dropIfExists('profile_photo_categories');
        Schema::dropIfExists('profile_document_categories');

        Schema::dropIfExists('profile_data');
        Schema::dropIfExists('profile_list_options');
        Schema::dropIfExists('profile_elements');
        Schema::dropIfExists('profile_categories');
    }
}
