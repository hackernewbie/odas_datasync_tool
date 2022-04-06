<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiRelatedColumnsToFacilityInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facility_information', function (Blueprint $table) {
            $table->string('reference_number')->nullable()->after('latitude');
            $table->string('status')->nullable()->after('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facility_information', function (Blueprint $table) {
            $table->dropColumn('reference_number');
            $table->dropColumn('status');
        });
    }
}
