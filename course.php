<?php

    function get_from_api ( $endpoint, $public_key, $secret_key )
    {
        // configure the request
        $ch = curl_init();
        // set the URL
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        // set the method
        $method = "GET";
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // expiry date
        $expiryDate = gmdate('Y/m/d H:i:s+00:00', strtotime('+15 minutes'));
        // build the check string
        $checkString = $endpoint . $method . $expiryDate;
        // sign it
        $signature = hash_hmac("sha256", $checkString, $secret_key);
        // set the headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Public: ' . $public_key,
            'X-Signature: ' . $signature,
            'X-Expires: ' . $expiryDate
        ));
        // make the api call
        $response = curl_exec($ch);
        $status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        // tidy up
        curl_close($ch);
        // and send back
        return array(
            'status' => $status,
            'response' => $response
        );
    }

    $result = get_from_api('https://api.elucidat.com/releases/{{release_code}}/launch','<YOUR PUBLIC KEY>','<YOUR SECRET KEY>');

    if ($result['status'] == 400 || !$result['response'])
        exit('Please remember to put your credentials in.');

    $outcome = json_decode($result['response'], true);

    print file_get_contents($outcome['url']);
                
?>