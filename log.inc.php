<?php

// https://coworking-metz-ag.requestcatcher.com/
function ag_log_erreur($message)
{

    ag_log_message($message);
    exit;
}

function ag_log_message($message)
{
    $data = [
        'message' => $message,
        'datetime' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'uid'=> get_current_user_id(),
        'name'=> wp_get_current_user()->display_name??'',
    ];

    $jsonPayload = json_encode($data, JSON_PRETTY_PRINT);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://coworking-metz-ag.requestcatcher.com/log");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);


}