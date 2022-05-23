<?php

use App\Models\ODASToken;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

function SanitizeString($inputString){
    return preg_replace('/[.]/', '', $inputString);
}

function ConvertCuMToMT($value_in_cum){
    $valueInMT  =   $value_in_cum*1.4291/1000;
    return $valueInMT;
}

function ConvertLPMToMT($value_in_LPM){
    $valueInCUM     =   ($value_in_LPM/1000)*60*24;
    $valueInMT      =   $valueInCUM*1.4291/1000;

    return $valueInMT;
}

function getODASAccessToken(){
    $clientId       =   config('odas.odas_client_id');
    $clientSecret   =   config('odas.odas_client_secret');
    $odasApiBAseURL =   config('odas.odas_base_url');

    $params = array(
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
     );

    $client = new Client();

    $response = $client->post($odasApiBAseURL.'v1.0/odas/get-access-token', [
        'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
        'body'    => json_encode($params)
    ]);
    $dataRes =   json_decode($response->getBody(), true);

    return $dataRes['accessToken'];
}
