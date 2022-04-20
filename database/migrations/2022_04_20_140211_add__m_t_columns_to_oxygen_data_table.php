<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMTColumnsToOxygenDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oxygen_data', function (Blueprint $table) {
            $table->string('lmo_current_stock_in_MT')->after('cryogenic_plant_in_ltr')->nullable();
            $table->string('lmo_current_storage_capacity_in_MT')->after('lmo_current_stock_in_MT')->nullable();
            $table->string('psa_gen_capacity_in_MT')->after('psa_capacity_in_cum')->nullable();
            $table->string('psa_storage_capacity_in_MT')->after('psa_gen_capacity_in_MT')->nullable();
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
            $table->dropColumn('');
        });
    }
}
