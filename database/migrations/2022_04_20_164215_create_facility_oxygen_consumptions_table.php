<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilityOxygenConsumptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_oxygen_consumptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('oxygen_data_id')->unsigned()->index()->nullable();
            $table->foreign('oxygen_data_id')->references('id')->on('oxygen_data')->onDelete('cascade');
            $table->string('consumption_for_date')->nullable();
            $table->string('consumption_updated_date')->nullable();
            $table->string('total_oxygen_consumed')->nullable();
            $table->string('total_oxygen_generated')->nullable();
            $table->string('odas_facility_id')->nullable();
            $table->string('requestId')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('facility_oxygen_consumptions');
    }
}
