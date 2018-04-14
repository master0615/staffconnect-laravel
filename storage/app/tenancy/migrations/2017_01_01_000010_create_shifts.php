<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShifts extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // shift statuses
        if (!Schema::hasTable('shift_statuses')) {
            Schema::create('shift_statuses', function (Blueprint $table) {
                $table->increments('id');
                $table->string('status', 30);
                $table->unsignedInteger('priority');
                $table->string('bg_color', 7);
                $table->string('border_color', 7);
                $table->string('font_color', 7)->default('#fff');
            });
        }

        // groups of shifts
        if (!Schema::hasTable('shift_groups')) {
            Schema::create('shift_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('gname', 70);
                $table->boolean('apply_all_or_nothing')->default('0'); // when set then applicant must apply for all roles with matching name in group
            });
        }

        // shifts
        if (!Schema::hasTable('shifts')) {
            Schema::create('shifts', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('shift_group_id')->nullable();
                $table->foreign('shift_group_id')
                    ->references('id')
                    ->on('shift_groups')
                    ->onDelete('set null');
                $table->boolean('live')->default(0); //can staff see?
                $table->unsignedInteger('shift_status_id')->nullable();
                $table->foreign('shift_status_id')
                    ->references('id')
                    ->on('shift_statuses')
                    ->onDelete('set null');
                $table->unsignedInteger('client_id')->nullable();
                $table->foreign('client_id')
                    ->references('id')
                    ->on('clients')
                    ->onDelete('set null');
                $table->unsignedBigInteger('location_id')->nullable();
                $table->foreign('location_id')
                    ->references('id')
                    ->on('locations')
                    ->onDelete('set null');
                $table->string('title', 70);
                $table->string('generic_title', 50)->nullable(); // replaces title for non selected staff
                $table->dateTime('shift_start'); // 03:33:33 indicates TBA start
                $table->dateTime('shift_end'); // 04:44:44 indicates TBA end
                $table->string('location', 70)->nullable();
                $table->string('generic_location', 70)->nullable(); // replaces location for non selected staff
                $table->string('timezone', 30); // php timezone string eg Australia/Perth automatically on address geoloc otherwise default agency timezone
                $table->string('contact', 50)->nullable();
                $table->string('address', 120)->nullable();
                $table->double('lat')->nullable(); // latitude
                $table->double('lon')->nullable(); // longitude
                $table->string('notes')->nullable();
                $table->boolean('reports_completed')->default('0'); // if all reports have been completed
                $table->boolean('staff_paid')->default('0'); // if all staff have been paid
                $table->enum('bill_status', [
                    'invoiced',
                    'paid',
                ])->nullable(); // if client has been invoiced or has paid
                $table->unsignedInteger('locked')->nullable(); // when locked by admin then other admins cant change, owners can. When locked by owner only that owner can change
                $table->foreign('locked')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('shift_admin_note_types')) {
            Schema::create('shift_admin_note_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('tname', 20);
                $table->string('color', 10);
            });
        }

        if (!Schema::hasTable('shift_admin_notes')) {
            Schema::create('shift_admin_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('shift_id');
                $table->foreign('shift_id')
                    ->references('id')
                    ->on('shifts')
                    ->onDelete('cascade');
                $table->unsignedInteger('creator_id')->nullable();
                $table->foreign('creator_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
                $table->string('note');
                $table->unsignedInteger('type_id')->nullable();
                $table->foreign('type_id')
                    ->references('id')
                    ->on('shift_admin_note_types')
                    ->onDelete('set null');
                $table->boolean('client_visible')->default('0');
                $table->timestamps();
            });
        }

        // general geographic area - named 'regions' on v3
        if (!Schema::hasTable('shift_work_area')) {
            Schema::create('shift_work_area', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('shift_id');
                $table->foreign('shift_id')
                    ->references('id')
                    ->on('shifts')
                    ->onDelete('cascade');
                $table->unsignedInteger('work_area_id');
                $table->foreign('work_area_id')
                    ->references('id')
                    ->on('work_areas')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('shift_tracking_option')) {
            Schema::create('shift_tracking_option', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('shift_id');
                $table->foreign('shift_id')
                    ->references('id')
                    ->on('shifts')
                    ->onDelete('cascade');
                $table->unsignedInteger('tracking_option_id');
                $table->foreign('tracking_option_id')
                    ->references('id')
                    ->on('tracking_options')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('shift_manager')) {
            Schema::create('shift_manager', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('shift_id');
                $table->foreign('shift_id')
                    ->references('id')
                    ->on('shifts')
                    ->onDelete('cascade');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }

        // flags for shifts to indicate whatever the agency wants. filterable on calendar and list view
        if (!Schema::hasTable('flag_shift')) {
            Schema::create('flag_shift', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('shift_id');
                $table->foreign('shift_id')
                    ->references('id')
                    ->on('shifts')
                    ->onDelete('cascade');
                $table->unsignedInteger('flag_id');
                $table->foreign('flag_id')
                    ->references('id')
                    ->on('flags')
                    ->onDelete('cascade');
            });
        }

        // shiftr in v3. one or more roles in shift, contains staffing details - requirements, payrates etc. eg 3 x blonde models
        // TODO cstatus
        // TODO astatus
        // TODO mage mxage move to reqs
        // TODO requploads move
        // TODO travel rates to seperate table?
        if (!Schema::hasTable('shift_roles')) {
            Schema::create('shift_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('shift_id');
                $table->foreign('shift_id')
                    ->references('id')
                    ->on('shifts')
                    ->onDelete('cascade');
                $table->unsignedInteger('outsource_company_id')->nullable(); // whole role can be outsourced
                $table->foreign('outsource_company_id')
                    ->references('id')
                    ->on('outsource_companies')
                    ->onDelete('set null');
                $table->string('rname', 50); // role name, previously role in v3
                $table->unsignedMediumInteger('num_required')->default(1); // number of staff required in role
                $table->enum('sex', [
                    'male',
                    'female',
                ])->nullable(); // sex requirement
                $table->string('notes')->nullable();
                $table->string('completion_notes')->nullable();
                $table->double('bill_rate', 10, 2)->nullable(); // client bill rate
                $table->enum('bill_rate_type', [
                    'phr',
                    'flat',
                ])
                    ->default('phr')
                    ->nullable(); // client bill rate type - per hour or flat
                $table->unsignedInteger('pay_category_id')->nullable();
                $table->foreign('pay_category_id')
                    ->references('id')
                    ->on('pay_categories')
                    ->onDelete('set null');
                $table->double('pay_rate', 10, 2)->nullable(); // staff pay rate
                $table->enum('pay_rate_type', [
                    'phr',
                    'flat',
                ])
                    ->default('phr')
                    ->nullable(); // staff pay rate type - per hour or flat
                $table->dateTime('role_start')->nullable(); // roles can have different start end times to shift. if null then takes shift time
                $table->dateTime('role_end')->nullable();
                $table->unsignedMediumInteger('unpaid_break')->nullable(); // unpaid break time in mins.
                $table->unsignedMediumInteger('paid_break')->nullable(); // paid break time in mins.
                $table->double('expense_limit', 10, 2)->nullable(); // expense max limit claimable. if null then nothing claimable
                $table->dateTime('application_deadline')->nullable(); // after this date staff cannot apply to work this role
                $table->unsignedTinyInteger('display_order')->nullable(); // display order shift page
                $table->timestamps();
            });
        }

        // requirements previously shiftr_attr, shiftr_preq, shiftr_ratings
        if (!Schema::hasTable('role_requirements')) {
            Schema::create('role_requirements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('shift_role_id');
                $table->foreign('shift_role_id')
                    ->references('id')
                    ->on('shift_roles')
                    ->onDelete('cascade');
                $table->enum('requirement', [
                    'age',
                    'custom_rating',
                    'performance_rating',
                    'attribute',
                    'profile_element',
                    'quiz',
                ]);
                $table->enum('operator', [
                    '=',
                    '!=',
                    '>',
                    '<',
                ]);
                $table->unsignedInteger('other_id')->nullable();
                $table->string('value');
            });
        }

        // doesn't exist in v3. this is to add eg travel allowance which was previously in role
        if (!Schema::hasTable('role_pay_items')) {
            Schema::create('role_pay_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('shift_role_id');
                $table->foreign('shift_role_id')
                    ->references('id')
                    ->on('shift_roles')
                    ->onDelete('cascade');
                $table->double('unit_rate', 10, 2)->nullable();
                $table->enum('unit_rate_type', [
                    'pu', // per unit
                    'flat',
                ])->default('pu');
                $table->double('units', 10, 2)->nullable();
                $table->string('item_name', 30);
                $table->enum('item_type', [
                    'bonus',
                    'expense',
                    'travel',
                    'other',
                ])->default('other');
            });
        }

        // staff statuses. priority has no use currently but needed if allow customised statuses in future?
        if (!Schema::hasTable('staff_statuses')) {
            Schema::create('staff_statuses', function (Blueprint $table) {
                $table->increments('id');
                $table->string('status', 35);
                $table->unsignedInteger('priority');
                $table->boolean('selected')->default('0');
                $table->string('message', 200);
                $table->string('bg_color', 7);
                $table->string('border_color', 7);
                $table->string('font_color', 7)->default('#fff');
            });
        }

        // previously shiftf in v3. links users to roles in shifts
        if (!Schema::hasTable('role_staff')) {
            Schema::create('role_staff', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('shift_role_id');
                $table->foreign('shift_role_id')
                    ->references('id')
                    ->on('shift_roles')
                    ->onDelete('cascade');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->unsignedInteger('outsource_company_id')->nullable(); // if null then staff works directly for this agency
                $table->foreign('outsource_company_id')
                    ->references('id')
                    ->on('outsource_companies')
                    ->onDelete('set null');
                $table->unsignedInteger('staff_status_id')->nullable();
                $table->foreign('staff_status_id')
                    ->references('id')
                    ->on('staff_statuses')
                    ->onDelete('set null');
                $table->enum('pay_status', ['no_pay', 'held', 'released', 'submitted', 'paid'])->default('held');
                $table->dateTime('staff_start')->nullable(); // staff can have different start end times to role and shift. if null then takes role then shift time
                $table->dateTime('staff_end')->nullable();
                $table->unsignedMediumInteger('unpaid_break')->nullable(); // unpaid break time in mins.
                $table->unsignedMediumInteger('paid_break')->nullable(); // paid break time in mins.
                $table->double('expense_limit', 10, 2)->nullable(); // expense max limit claimable. if null then nothing claimable
                $table->double('bill_rate', 10, 2)->nullable(); // client bill rate
                $table->enum('bill_rate_type', [
                    'phr',
                    'flat',
                ])
                    ->default('phr')
                    ->nullable(); // client bill rate type - per hour or flat
                $table->double('pay_rate', 10, 2)->nullable(); // staff pay rate
                $table->enum('pay_rate_type', [
                    'phr',
                    'flat',
                ])
                    ->default('phr')
                    ->nullable(); // staff pay rate type - per hour or flat
                $table->boolean('team_leader')->default('0'); // team lead can check in and out others in shift
                $table->boolean('times_locked')->default('0'); // if times_locked = 1 then staff do not have option to change start and end times upon shift completion
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('application_reasons')) {
            Schema::create('application_reasons', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('role_staff_id');
                $table->foreign('role_staff_id')
                    ->references('id')
                    ->on('role_staff')
                    ->onDelete('cascade');
                $table->string('reason', 200);
            });
        }

        if (!Schema::hasTable('replacement_reasons')) {
            Schema::create('replacement_reasons', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('role_staff_id');
                $table->foreign('role_staff_id')
                    ->references('id')
                    ->on('role_staff')
                    ->onDelete('cascade');
                $table->string('reason', 200);
            });
        }

        if (!Schema::hasTable('staff_checks')) {
            Schema::create('staff_checks', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('role_staff_id');
                $table->foreign('role_staff_id')
                    ->references('id')
                    ->on('role_staff')
                    ->onDelete('cascade');
                $table->enum('type', ['in', 'out', 'in_attempt', 'out_attempt']);
                $table->double('lat')->nullable(); // latitude
                $table->double('lon')->nullable(); // longitude
                $table->double('distance')->nullable();
                $table->string('ext', 4)->nullable(); //photo
                $table->double('photo_lat')->nullable(); // latitude
                $table->double('photo_lon')->nullable(); // longitude
                $table->dateTime('photo_created_at')->nullable();
                $table->dateTime('check_time');
                $table->unsignedInteger('checker_id')->nullable(); //who checked staff in?
                $table->foreign('checker_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
                $table->timestamps();
            });
        }

        // doesn't exist in v3. this is to add eg travel allowance which was previously in role_staff, bonuses, deductions previously in deduction table
        if (!Schema::hasTable('staff_pay_items')) {
            Schema::create('staff_pay_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('role_staff_id');
                $table->foreign('role_staff_id')
                    ->references('id')
                    ->on('role_staff')
                    ->onDelete('cascade');
                $table->unsignedBigInteger('role_pay_item_id')->nullable(); // should give admin option to sync this or not
                $table->foreign('role_pay_item_id')
                    ->references('id')
                    ->on('role_pay_items')
                    ->onDelete('set null');
                $table->double('unit_rate', 10, 2)->nullable();
                $table->enum('unit_rate_type', [
                    'pu', // per unit
                    'flat',
                ])->default('pu');
                $table->double('units', 10, 2)->nullable();
                $table->string('item_name', 30);
                $table->enum('item_type', [
                    'bonus',
                    'deduction',
                    'expense',
                    'travel',
                    'other',
                ])->default('other');
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
        //
        Schema::dropIfExists('application_reasons');
        Schema::dropIfExists('replacement_reasons');
        Schema::dropIfExists('staff_pay_items');
        Schema::dropIfExists('staff_checks');
        Schema::dropIfExists('role_staff');
        Schema::dropIfExists('role_requirements');
        Schema::dropIfExists('role_pay_items');
        Schema::dropIfExists('shift_roles');
        Schema::dropIfExists('shift_managers');
        Schema::dropIfExists('shift_manager');
        Schema::dropIfExists('flag_shift');
        Schema::dropIfExists('shift_tracking_option');
        Schema::dropIfExists('shift_work_area');
        Schema::dropIfExists('shift_admin_notes');
        Schema::dropIfExists('shift_admin_note_types');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('shift_groups');
        Schema::dropIfExists('staff_statuses');
        Schema::dropIfExists('shift_statuses');
    }
}
