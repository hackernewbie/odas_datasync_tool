<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcessesRun extends Model
{
    use HasFactory;

    protected $table        =   'table_processes_run';

    protected $fillable     =   ['description','status'];
}
