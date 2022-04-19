<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilityBedInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_bed_info', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('oxygen_data_id')->unsigned()->index()->nullable();     // Local Facility Id
            $table->foreign('oxygen_data_id')->references('id')->on('oxygen_data')->onDelete('cascade');
            $table->string('odas_facility_id')->nullable();
            $table->string('no_gen_beds')->nullable();
            $table->string('no_hdu_beds')->nullable();
            $table->string('no_icu_beds')->nullable();
            $table->string('no_o2_concentrators')->nullable();
            $table->string('no_vent_beds')->nullable();
            $table->string('occupancy_date')->nullable();
            $table->text('requestId')->nullable();

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
        Schema::dropIfExists('facility_bed_info');
    }
}
