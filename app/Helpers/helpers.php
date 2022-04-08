<?php

use App\Models\ODASToken;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

function SanitizeString($inputString){
    return preg_replace('/[.]/', '', $inputString);
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
