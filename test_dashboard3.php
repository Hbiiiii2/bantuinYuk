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

echo "--- /tasks/my ---\n";
$ch2 = curl_init('http://bantuinYuk.test/api/v1/tasks/my');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$resp2 = curl_exec($ch2);
$code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
echo "Status: $code2\nResponse: $resp2\n\n";

echo "--- /wallet ---\n";
$ch3 = curl_init('http://bantuinYuk.test/api/v1/wallet');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$resp3 = curl_exec($ch3);
$code3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
echo "Status: $code3\nResponse: $resp3\n";
