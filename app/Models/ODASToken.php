<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ODASToken extends Model
{
    use HasFactory;

    protected $table    =   'odas_auth_tokens';

    protected $fillable =   ['token','timestamp_utc'];
}
