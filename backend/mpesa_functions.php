<?php
// backend/mpesa_functions.php

function getAccessToken($consumer_key, $consumer_secret) {
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . base64_encode($consumer_key . ':' . $consumer_secret)]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // For local testing
    $response = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($response);
    return $result->access_token ?? null;
}

function lipaNaMpesaOnline($phone, $amount, $account_reference, $transaction_desc) {
    // ==================== YOUR ACTUAL CREDENTIALS ====================
    $consumer_key = "m9s4cbrA4TlgQglykCwnSUs7bUQma8KuOjWXGFxFKSpgcs5z";
    $consumer_secret = "dgpsA1Ku6kmRsBTFZWJnSbqDDYHGWaI366uVyWM6qZksvwJoDEVteTOg0BbXhNeA";
    // ================================================================
    
    $shortcode = "174379";
    $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
    
    // ==================== YOUR NGROK CALLBACK URL ====================
    $callback_url = "https://audible-january-acid.ngrok-free.dev/Farmconnect/backend/mpesa_callback.php";
    // =================================================================

    $access_token = getAccessToken($consumer_key, $consumer_secret);
    if (!$access_token) {
        return ["error" => "Failed to get access token. Check Consumer Key/Secret."];
    }

    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode . $passkey . $timestamp);

    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // For local testing

    $curl_post_data = [
        'BusinessShortCode' => $shortcode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => $shortcode,
        'PhoneNumber' => $phone,
        'CallBackURL' => $callback_url,
        'AccountReference' => $account_reference,
        'TransactionDesc' => $transaction_desc
    ];

    $data_string = json_encode($curl_post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    
    $curl_response = curl_exec($curl);
    
    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        curl_close($curl);
        return ["error" => "cURL Error: " . $error_msg];
    }
    
    curl_close($curl);
    return json_decode($curl_response, true);
}
?>
