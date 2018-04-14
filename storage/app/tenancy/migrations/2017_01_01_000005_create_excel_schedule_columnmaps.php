<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExcelScheduleColumnmaps extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('excel_schedule_columnmaps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('col_name_1', 20);
            $table->string('col_name_2', 20);
            $table->string('description', 100);
        });
        
        DB::table('excel_schedule_columnmaps')->insert([
            [
                'col_name_1' => "Title",
                'col_name_2' => "Title"
            ],
            [
                'col_name_1' => "Generic Title",
                'col_name_2' => "Generic Title"
            ],
            [
                'col_name_1' => "Group",
                'col_name_2' => "Group"
            ],
            [
                'col_name_1' => "Client",
                'col_name_2' => "Client"
            ],
            [
                'col_name_1' => "Manager",
                'col_name_2' => "Manager"
            ],
            [
                'col_name_1' => "Date",
                'col_name_2' => "Date"
            ],
            [
                'col_name_1' => "Times",
                'col_name_2' => "Time"
            ],
            [
                'col_name_1' => "Location",
                'col_name_2' => "Location"
            ],
            [
                'col_name_1' => "Generic Location",
                'col_name_2' => "Generic Location"
            ],
            [
                'col_name_1' => "Chain",
                'col_name_2' => "Chain"
            ],
            [
                'col_name_1' => "Address Line 1",
                'col_name_2' => "Addr1"
            ],
            [
                'col_name_1' => "Address Line 2",
                'col_name_2' => "Addr2"
            ],
            [
                'col_name_1' => "Address Line 3",
                'col_name_2' => "Addr3"
            ],
            [
                'col_name_1' => "Contact 1",
                'col_name_2' => "Con1"
            ],
            [
                'col_name_1' => "Contact 2",
                'col_name_2' => "Con2"
            ],
            [
                'col_name_1' => "Shift Notes",
                'col_name_2' => "Shift Notes"
            ],
            [
                'col_name_1' => "Selected",
                'col_name_2' => "Selected"
            ],
            [
                'col_name_1' => "Standby",
                'col_name_2' => "Standby"
            ],
            [
                'col_name_1' => "Confirmed",
                'col_name_2' => "Confirmed"
            ],
            [
                'col_name_1' => "Role",
                'col_name_2' => "Role"
            ],
            [
                'col_name_1' => "Number",
                'col_name_2' => "Number"
            ],
            [
                'col_name_1' => "Sex",
                'col_name_2' => "Sex"
            ],
            [
                'col_name_1' => "Bill Rate",
                'col_name_2' => "Bill Rate"
            ],
            [
                'col_name_1' => "Bill Rate Type",
                'col_name_2' => "Bill Rate Type"
            ],
            [
                'col_name_1' => "Pay Rate",
                'col_name_2' => "Pay Rate"
            ],
            [
                'col_name_1' => "Pay Rate Type",
                'col_name_2' => "Pay Rate Type"
            ],
            [
                'col_name_1' => "Role Notes",
                'col_name_2' => "Role Notes"
            ],
            [
                'col_name_1' => "Completion Reports",
                'col_name_2' => "Reports"
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
        //
        Schema::dropIfExists('excel_schedule_columnmaps');
    }
}
