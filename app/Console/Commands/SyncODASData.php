<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncODASData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs all Oxygen data from Google Sheet To the ODAS API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::debug('SyncaData command fired');
        app('App\Http\Controllers\FacilityController@GetFacilities')->everyMinute();
    }
}
