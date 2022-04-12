<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOxygenDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oxygen_data', function (Blueprint $table) {
            $table->id();
            $table->string('odas_facility_id');
            $table->string('facility_name');
            $table->string('supply_source')->nullable();
            $table->string('time_of_update')->nullable();
            $table->string('no_of_patients_on_o2')->nullable();
            $table->string('no_of_o2_supported_beds')->nullable();
            $table->string('no_of_ICU_beds')->nullable();
            $table->string('no_of_oxygenated_beds_including_ICU')->nullable();
            $table->string('psa_in_lpm')->nullable();
            $table->string('is_active')->nullable();
            $table->string('planned_psa_capacity_in_cum')->nullable();
            $table->string('psa_capacity_in_cum');
            $table->string('cryogenic_plant_in_ltr');
            $table->string('planned_cryogenic_capacity_in_cum');
            $table->string('cryogenic_capacity_in_cum');

            $table->string('no_of_empty_typeB_cylinders')->nullable();
            $table->string('no_typeB_cylinders_in_transit')->nullable();
            $table->string('no_filled_typeB_cylinders')->nullable();
            $table->string('total_typeB_cylinders_available')->nullable();
            $table->string('no_of_consumed_typeB_cylinders_in_last_24_hours')->nullable();

            $table->string('no_of_empty_typeD_cylinders')->nullable();
            $table->string('no_typeD_cylinders_in_transit')->nullable();
            $table->string('no_filled_typeD_cylinders')->nullable();
            $table->string('total_typeD_cylinders')->nullable();
            $table->string('no_of_consumed_typeD_cylinders_in_last_24_hours')->nullable();

            $table->string('o2_typeD_and_typeB_capacity_in_cum')->nullable();
            $table->string('overall_o2_availability_in_cum')->nullable();
            $table->string('actual_o2_availability_in_cum')->nullable();

            $table->string('no_of_BiPAP_machines')->nullable();
            $table->string('no_of_o2_concentrators')->nullable();

            $table->string('unaccounted_typeB')->nullable();
            $table->string('unaccounted_typeD')->nullable();
            $table->string('appx_o2_demand_with_current_load_in_hrs')->nullable();
            $table->string('appx_o2_demand_with_current_no_of_patients_in_cum')->nullable();
            $table->string('appx_o2_demand_with_all_beds_full')->nullable();

            $table->text('requestId');
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
        Schema::dropIfExists('oxygen_data');
    }
}
