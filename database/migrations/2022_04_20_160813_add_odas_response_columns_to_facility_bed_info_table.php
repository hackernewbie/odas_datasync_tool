<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOdasResponseColumnsToFacilityBedInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facility_bed_info', function (Blueprint $table) {
            $table->string('status')->after('requestId')->nullable();
            $table->string('odas_reference_number')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facility_bed_info', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('odas_reference_number');
        });
    }
}
