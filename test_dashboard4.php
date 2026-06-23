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
$err2 = json_decode($resp2, true);
echo "Message: " . ($err2['message'] ?? $err2['title'] ?? 'Unknown') . "\n";
if (isset($err2['file'])) echo "File: " . $err2['file'] . "\nLine: " . $err2['line'] . "\n";

echo "\n--- /wallet ---\n";
$ch3 = curl_init('http://bantuinYuk.test/api/v1/wallet');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
$resp3 = curl_exec($ch3);
$err3 = json_decode($resp3, true);
echo "Message: " . ($err3['message'] ?? $err3['title'] ?? 'Unknown') . "\n";
if (isset($err3['file'])) echo "File: " . $err3['file'] . "\nLine: " . $err3['line'] . "\n";
