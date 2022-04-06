<?php

use App\ShipRocket;
use Illuminate\Support\Facades\Http;

function getODASAccessToken(){
    $clientId       =   config('odas.odas_client_id');
    $clientSecret   =   config('odas.odas_client_secret');
    $odasApiBAseURL =   config('odas.odas_base_url');

    //dd($odasApiBAseURL);
    //dd($clientId . " : " . $clientSecret);

    $data = array(
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
     );

    //  $response = Http::post($odasApiBAseURL.'v1.0/odas/get-access-token', [
    //     'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
    //     'body'    => json_encode($data)
    // ]);



    $client = new \GuzzleHttp\Client();
    $response = $client->post($odasApiBAseURL.'v1.0/odas/get-access-token', [
        'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
        'body'    => json_encode($data)
    ]);
    $dataRes =   json_decode($response->getBody(), true);
    dd($dataRes);
    //$token  =   $response->json()['token'];
    // // Save record into the table
    // $shiprocketToken                =   new ShipRocket();
    // $shiprocketToken->token         =   $token;
    // $shiprocketToken->save();
}
