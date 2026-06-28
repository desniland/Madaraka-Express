<?php
function value_or_default(string $key, string $default = ''): string {
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
        return trim((string) $_POST[$key]);
    }
    if (isset($_GET[$key]) && $_GET[$key] !== '') {
        return trim((string) $_GET[$key]);
    }
    return $default;
}

$from = value_or_default('leaving_from', value_or_default('from', 'Nairobi Terminus'));
$to = value_or_default('going_to', value_or_default('to', 'Mombasa Terminus'));
$date = value_or_default('travel_date', value_or_default('date', date('Y-m-d')));
$trainType = value_or_default('train_type', 'Inter-County');
$coachType = value_or_default('coach_type', 'Premium');
$amount = value_or_default('totalFare', value_or_default('total', '0'));
$phone = value_or_default('pay_mobile', '');
$checkoutRequestId = value_or_default('mpesa_checkout_request_id', '');
$merchantRequestId = value_or_default('mpesa_merchant_request_id', '');

$fromSafe = htmlspecialchars($from, ENT_QUOTES, 'UTF-8');
$toSafe = htmlspecialchars($to, ENT_QUOTES, 'UTF-8');
$dateSafe = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
$trainTypeSafe = htmlspecialchars($trainType, ENT_QUOTES, 'UTF-8');
$coachTypeSafe = htmlspecialchars($coachType, ENT_QUOTES, 'UTF-8');
$amountSafe = htmlspecialchars($amount, ENT_QUOTES, 'UTF-8');
$phoneSafe = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
$checkoutRequestIdSafe = htmlspecialchars($checkoutRequestId, ENT_QUOTES, 'UTF-8');
$merchantRequestIdSafe = htmlspecialchars($merchantRequestId, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madaraka Express - Payment</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style-sgr.css">
    <style>
        body { background: #f7f7f7; }
        .wrap { max-width: 980px; margin: 40px auto; }
        .panel-box { background: #fff; border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,.08); padding: 24px; }
        .title { margin-top: 0; color: #882430; }
        .summary-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
        .item { border: 1px solid #eee; border-radius: 10px; padding: 12px; }
        .label { display: block; font-size: 12px; font-weight: 700; color: #777; text-transform: uppercase; }
        .value { display: block; font-size: 16px; font-weight: 700; color: #222; margin-top: 4px; }
        .notice { margin-top: 18px; background: #fff7f3; border: 1px solid #ffd4c1; padding: 14px; border-radius: 10px; }
        .small { color: #777; }
        .back-btn { display: inline-block; margin-top: 16px; background: #ff5624; color: #fff; padding: 10px 14px; border-radius: 6px; }
        @media (max-width: 768px) { .summary-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="panel-box">
            <h2 class="title">Payment request sent</h2>
            <p>Complete the M-Pesa prompt on your phone. This page keeps your booking details ready while payment is confirmed.</p>

            <div class="summary-grid">
                <div class="item"><span class="label">From</span><span class="value"><?php echo $fromSafe; ?></span></div>
                <div class="item"><span class="label">To</span><span class="value"><?php echo $toSafe; ?></span></div>
                <div class="item"><span class="label">Departure Date</span><span class="value"><?php echo $dateSafe; ?></span></div>
                <div class="item"><span class="label">Train Type</span><span class="value"><?php echo $trainTypeSafe; ?></span></div>
                <div class="item"><span class="label">Coach Type</span><span class="value"><?php echo $coachTypeSafe; ?></span></div>
                <div class="item"><span class="label">Phone</span><span class="value"><?php echo $phoneSafe; ?></span></div>
                <div class="item"><span class="label">Amount</span><span class="value">KSH <?php echo $amountSafe; ?></span></div>
            </div>

            <div class="notice">
                <div><strong>Checkout Request ID:</strong> <?php echo $checkoutRequestIdSafe ?: 'Pending'; ?></div>
                <div><strong>Merchant Request ID:</strong> <?php echo $merchantRequestIdSafe ?: 'Pending'; ?></div>
                <p class="small" style="margin-top:10px;">If the STK prompt does not appear, verify your number and try again from the booking details page.</p>
            </div>

            <a href="index.php" class="back-btn">Book another train</a>
        </div>
    </div>
</body>
</html>
