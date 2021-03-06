<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilityInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_information', function (Blueprint $table) {
            $table->id();
            $table->string('facility_name');
            $table->string('odas_facility_id')->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city_lgd_code')->nullable();
            $table->string('district_lgd_code');
            $table->string('pincode')->nullable();
            $table->string('state_lgd_code');
            $table->string('subdistrict_lgd_code')->nullable();
            $table->string('ownership_type');
            $table->string('ownership_subtype')->nullable();
            $table->string('facility_type');
            $table->string('facility_type_code');
            $table->string('longitude');
            $table->string('latitude');
            $table->text('requestId');
            $table->string('odas_reference_number')->nullable();
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
        Schema::dropIfExists('facility_information');
    }
}
