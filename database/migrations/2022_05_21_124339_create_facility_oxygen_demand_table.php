<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilityOxygenDemandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_oxygen_demand', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('oxygen_data_id')->unsigned()->index()->nullable();
            $table->foreign('oxygen_data_id')->references('id')->on('oxygen_data')->onDelete('cascade');
            $table->string('accuracy_remarks')->nullable();
            $table->string('demand_accuracy_flag');
            $table->string('demand_for_date');                          /// yyyy-mm--dd
            $table->string('demand_raised_date');                       /// in UTC
            $table->string('over_estimated_by')->nullable();            /// Numeric
            $table->string('total_estimated_demand');                   /// Numeric
            $table->string('under_estimated_by')->nullable();           /// Numeric
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
        Schema::dropIfExists('facility_oxygen_demand');
    }
}
