<?php

namespace App\Http\Controllers\CI;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ApiCallingController extends Controller
{
    public function api()
    {
        // Define your headers and payload
        $headers = [
            'Authorization' => 'Bearer your-access-token',
            'Accept' => 'application/json',
            'username' => 'ShebaAYS',
            'password' => '^*AY5a:vSeBa}=)',
        ];

        $payload = [
            "cif_number"=> "02833830",
            "email"=> "test@email.com",
            "mobile_no"=> "01718440618",
            "callback_url"=> url('/')."/CI",
            "global_transaction_id"=> "AYS_ID124234234"
        ];

        $url = url('/').'/api/CI-token-validation';

        // Send the HTTP GET request with headers and payload
        $response = Http::withHeaders($headers)
            ->post($url, $payload);

        // Check if the request was successful
        if ($response->successful()) {
            $responseData = $response->json();
            return redirect($responseData['ci_web_url']);

        } else {
            $errorMessage = $response->body();
            return $errorMessage;
        }
    }
}
