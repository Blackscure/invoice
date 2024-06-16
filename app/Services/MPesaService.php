<?php

// app/Services/MpesaService.php

namespace App\Services;

class MpesaService
{
    public function getAccessToken($consumerKey, $consumerSecret)
    {
        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);

        if ($curl_response === false) {
            return null;
        }

        $result = json_decode($curl_response);
        return $result->access_token;
    }

    public function initiatePayment($accessToken, $shortcode, $lipaNaMpesaOnlinePasskey, $amount, $phoneNumber, $callbackUrl, $accountReference,  $invoiceNumber)
    {
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $timestamp = date('YmdHis');
        $password = base64_encode($shortcode . $lipaNaMpesaOnlinePasskey . $timestamp);

        $curl_post_data = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phoneNumber,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => $callbackUrl,
            'AccountReference' =>  $invoiceNumber,
            'TransactionDesc' => 'Payment for Invoice ' . $invoiceNumber,
        ];

        $data_string = json_encode($curl_post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        if ($curl_response === false) {
            return null;
        }

        return json_decode($curl_response);
    }
}
