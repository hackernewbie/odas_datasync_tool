<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthFacilityAnalysis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_facility_analysis', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('oxygen_data_id')->unsigned()->index()->nullable();     // Local Facility Id
            $table->foreign('oxygen_data_id')->references('id')->on('oxygen_data')->onDelete('cascade');
            $table->string('demand')->nullable();
            $table->string('available_supply_at_facility')->nullable();
            $table->string('remaining_demand_after_exhausting_filled_cylinders')->nullable();
            $table->string('supply_in_transit_of_typeB')->nullable();
            $table->string('remaining_demand_after_factoring_in_transit_cylinders')->nullable();
            $table->string('capacity_of_empty_cylinders_typeD')->nullable();
            $table->string('capacity_of_empty_cylinders_typeB')->nullable();
            $table->string('no_of_typeD_cylinders_to_refill')->nullable();
            $table->string('no_of_typeB_cylinders_to_refill_if_demand_unmet_typeB')->nullable();
            $table->string('typeB_empty_cylinders_to_be_returned')->nullable();
            $table->string('typeD_empty_cylinders_to_be_returned')->nullable();

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
        Schema::dropIfExists('health_facility_analysis');
    }
}
