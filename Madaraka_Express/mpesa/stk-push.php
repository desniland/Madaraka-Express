<?php
header('Content-Type: application/json');

$config = require __DIR__ . '/mpesa-config.php';

function json_response(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function normalize_phone(string $phone): string {
    $phone = preg_replace('/\s+/', '', $phone);
    $phone = preg_replace('/[^0-9+]/', '', $phone);

    if (strpos($phone, '+254') === 0) {
        $phone = substr($phone, 1);
    } elseif (strpos($phone, '0') === 0) {
        $phone = '254' . substr($phone, 1);
    } elseif (strpos($phone, '7') === 0 || strpos($phone, '1') === 0) {
        $phone = '254' . $phone;
    }

    return $phone;
}

function normalize_amount($amount): int {
    $amount = preg_replace('/[^0-9.]/', '', (string) $amount);
    if ($amount === '' || !is_numeric($amount)) {
        return 0;
    }

    return (int) round((float) $amount, 0);
}

function access_token(array $config): string {
    $env = strtolower($config['env'] ?? 'sandbox');
    $baseUrl = $env === 'live'
        ? 'https://api.safaricom.co.ke'
        : 'https://sandbox.safaricom.co.ke';

    $credentials = base64_encode(($config['consumer_key'] ?? '') . ':' . ($config['consumer_secret'] ?? ''));

    $ch = curl_init($baseUrl . '/oauth/v1/generate?grant_type=client_credentials');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Basic ' . $credentials
        ]
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException('Failed to obtain access token: ' . $curlError);
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded) || empty($decoded['access_token'])) {
        throw new RuntimeException('Failed to obtain access token from Safaricom.');
    }

    return $decoded['access_token'];
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
    }

    $phone = normalize_phone((string) ($_POST['pay_mobile'] ?? $_POST['phone'] ?? ''));
    $amount = normalize_amount($_POST['totalFare'] ?? $_POST['amount'] ?? '');
    $from = trim((string) ($_POST['leaving_from'] ?? $_POST['from'] ?? ''));
    $to = trim((string) ($_POST['going_to'] ?? $_POST['to'] ?? ''));
    $travelDate = trim((string) ($_POST['travel_date'] ?? $_POST['date'] ?? ''));
    $trainType = trim((string) ($_POST['train_type'] ?? ''));

    if ($phone === '' || !preg_match('/^254(7|1)\d{8}$/', $phone)) {
        json_response(['success' => false, 'message' => 'Enter a valid Safaricom mobile number.'], 422);
    }

    if ($amount <= 0) {
        json_response(['success' => false, 'message' => 'Total fare is missing or invalid.'], 422);
    }

    if (($config['consumer_key'] ?? '') === 'YOUR_CONSUMER_KEY' || ($config['consumer_secret'] ?? '') === 'YOUR_CONSUMER_SECRET' || ($config['shortcode'] ?? '') === 'YOUR_SHORTCODE' || ($config['passkey'] ?? '') === 'YOUR_PASSKEY') {
        json_response(['success' => false, 'message' => 'Configure M-Pesa credentials in mpesa/mpesa-config.php or environment variables before using STK Push.'], 500);
    }

    $env = strtolower($config['env'] ?? 'sandbox');
    $baseUrl = $env === 'live'
        ? 'https://api.safaricom.co.ke'
        : 'https://sandbox.safaricom.co.ke';

    $timestamp = date('YmdHis');
    $password = base64_encode(($config['shortcode'] ?? '') . ($config['passkey'] ?? '') . $timestamp);
    $token = access_token($config);

    $payload = [
        'BusinessShortCode' => (string) $config['shortcode'],
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => (string) $config['shortcode'],
        'PhoneNumber' => $phone,
        'CallBackURL' => $config['callback_url'],
        'AccountReference' => $config['account_reference'] ?: trim($from . '-' . $to),
        'TransactionDesc' => trim($config['transaction_desc'] ?: 'Madaraka Express booking')
    ];

    $ch = curl_init($baseUrl . '/mpesa/stkpush/v1/processrequest');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        json_response(['success' => false, 'message' => 'Failed to initiate STK Push: ' . $curlError], 500);
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        json_response(['success' => false, 'message' => 'Unexpected response from Safaricom.', 'raw' => $response], 500);
    }

    if ($httpCode >= 400 || (($decoded['ResponseCode'] ?? '') !== '0')) {
        json_response([
            'success' => false,
            'message' => $decoded['errorMessage'] ?? $decoded['ResponseDescription'] ?? 'Could not initiate payment.',
            'response' => $decoded
        ], 500);
    }

    json_response([
        'success' => true,
        'message' => $decoded['ResponseDescription'] ?? 'STK Push sent successfully. Check your phone.',
        'checkoutRequestID' => $decoded['CheckoutRequestID'] ?? '',
        'merchantRequestID' => $decoded['MerchantRequestID'] ?? '',
        'response' => $decoded,
        'booking' => [
            'from' => $from,
            'to' => $to,
            'date' => $travelDate,
            'train_type' => $trainType,
            'amount' => $amount,
            'phone' => $phone
        ]
    ]);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
