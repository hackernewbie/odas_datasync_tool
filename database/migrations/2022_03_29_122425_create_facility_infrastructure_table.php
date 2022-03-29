<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilityInfrastructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_infrastructure', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('facility_information_id')->unsigned()->index();     // Local Facility Id
            $table->foreign('facility_information_id')->references('id')->on('facility_information')->onDelete('cascade');
            $table->string('general_beds_with_o2');
            $table->string('hdu_beds');
            $table->string('icu_beds');
            $table->string('o2_concentrators');
            $table->string('ventilators');
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
        Schema::dropIfExists('facility_infrastructure');
    }
}
