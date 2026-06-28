<?php
return [
    'env' => getenv('MPESA_ENV') ?: 'sandbox',
    'consumer_key' => getenv('MPESA_CONSUMER_KEY') ?: 'YOUR_CONSUMER_KEY',
    'consumer_secret' => getenv('MPESA_CONSUMER_SECRET') ?: 'YOUR_CONSUMER_SECRET',
    'shortcode' => getenv('MPESA_SHORTCODE') ?: 'YOUR_SHORTCODE',
    'passkey' => getenv('MPESA_PASSKEY') ?: 'YOUR_PASSKEY',
    'callback_url' => getenv('MPESA_CALLBACK_URL') ?: 'https://example.com/mpesa/callback.php',
    'transaction_desc' => getenv('MPESA_TRANSACTION_DESC') ?: 'Madaraka Express booking',
    'account_reference' => getenv('MPESA_ACCOUNT_REFERENCE') ?: 'MadarakaExpress'
];
