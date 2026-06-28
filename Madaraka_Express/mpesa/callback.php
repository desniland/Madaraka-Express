<?php
header('Content-Type: application/json');

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!is_array($data)) {
    $data = ['raw' => $body];
}

$logLine = date('c') . ' ' . json_encode($data, JSON_UNESCAPED_SLASHES) . PHP_EOL;
@file_put_contents(__DIR__ . '/mpesa-callback.log', $logLine, FILE_APPEND);

echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
