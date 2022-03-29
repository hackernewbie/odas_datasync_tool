<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilityNodalOfficerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_nodal_officer_details', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('facility_information_id')->unsigned()->index()->nullable();     // Local Facility Id
            $table->foreign('facility_information_id')->references('id')->on('facility_information')->onDelete('cascade');
            $table->string('officer_name');
            $table->string('officer_designation');
            $table->string('officer_salutation');
            $table->string('officer_country_code');
            $table->string('officer_mobile_number');
            $table->string('officer_email');
            $table->string('requestId')->nullable();            /// System-generated id that we generate for every request. 36 characters.
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
        Schema::dropIfExists('facility_nodal_officer_details');
    }
}
