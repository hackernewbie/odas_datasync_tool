<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDemandEstimationColumnsToOxygenDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oxygen_data', function (Blueprint $table) {
            $table->string('demand_estimation')->nullable()->after('appx_o2_demand_with_all_beds_full');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oxygen_data', function (Blueprint $table) {
            $table->dropColumn('demand_estimation');
        });
    }
}
