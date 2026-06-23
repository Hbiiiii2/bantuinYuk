<?php
$data = json_encode(['email'=>'budi@example.com', 'password'=>'user123']);
$ch = curl_init('http://bantuinYuk.test/api/v1/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$resp = curl_exec($ch);
$json = json_decode($resp, true);
$token = $json['data']['token']['access_token'] ?? null;

$ch2 = curl_init('http://bantuinYuk.test/api/v1/users/dashboard');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$resp2 = curl_exec($ch2);
$err = json_decode($resp2, true);
echo 'Message: ' . ($err['message'] ?? 'Unknown') . "\n";
echo 'File: ' . ($err['file'] ?? 'Unknown') . "\n";
echo 'Line: ' . ($err['line'] ?? 'Unknown') . "\n";
