<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOdasColumnsToFacilityInfrastructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facility_infrastructure', function (Blueprint $table) {
            $table->text('odas_reference_number')->after('requestId')->nullable();
            $table->string('status')->after('odas_reference_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facility_infrastructure', function (Blueprint $table) {
            $table->dropColumn('odas_reference_number');
            $table->dropColumn('status');
        });
    }
}
