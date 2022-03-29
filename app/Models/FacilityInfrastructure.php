<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityInfrastructure extends Model
{
    use HasFactory;

    protected $table        =   'facility_infrastructure';

    protected $fillable     =   [
                        'facility_information_id','general_beds_with_o2','hdu_beds','icu_beds',
                        'o2_concentrators', 'ventilators','requestId',
    ];

    public function Facility(){
        return $this->belongsTo('App\Models\Facility');
    }
}
