<?php
function request_value(string $key, string $default = ''): string {
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
        return trim((string) $_POST[$key]);
    }
    if (isset($_GET[$key]) && $_GET[$key] !== '') {
        return trim((string) $_GET[$key]);
    }
    return $default;
}

$from = request_value('from', 'Nairobi Terminus');
$to = request_value('to', 'Mombasa Terminus');
$date = request_value('date', date('Y-m-d'));
$trainType = request_value('train_type', 'Inter-County');
$coachType = request_value('coach_type', 'Premium');
$adults = request_value('adults', '0');
$child1 = request_value('child1', '0');
$child2 = request_value('child2', '0');
$total = request_value('total', '0');

$fromSafe = htmlspecialchars($from, ENT_QUOTES, 'UTF-8');
$toSafe = htmlspecialchars($to, ENT_QUOTES, 'UTF-8');
$dateSafe = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
$trainTypeSafe = htmlspecialchars($trainType, ENT_QUOTES, 'UTF-8');
$coachTypeSafe = htmlspecialchars($coachType, ENT_QUOTES, 'UTF-8');
$adultsSafe = htmlspecialchars($adults, ENT_QUOTES, 'UTF-8');
$child1Safe = htmlspecialchars($child1, ENT_QUOTES, 'UTF-8');
$child2Safe = htmlspecialchars($child2, ENT_QUOTES, 'UTF-8');
$totalSafe = htmlspecialchars($total, ENT_QUOTES, 'UTF-8');

$travelDateText = date('D, M j Y', strtotime($dateSafe)) ?: $dateSafe;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madaraka Express - Booking Details</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style-sgr.css">
    <link rel="stylesheet" href="css/custom.css">
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <style>
        body { background: #f7f7f7; }
        .page-title-container { background: #882430; color: #fff; padding: 24px 0; margin-bottom: 24px; }
        .page-title-container h2 { margin: 0; color: #fff; font-weight: 700; }
        .btn-medium.signupbtn { background: #ff5624; color: #fff; padding: 10px 16px; border-radius: 6px; float: right; }
        .booking-shell { background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.08); padding: 24px; margin-bottom: 24px; }
        .booking-summary { background: #fff7f3; border: 1px solid #ffd4c1; border-radius: 10px; padding: 18px; margin-bottom: 20px; }
        .booking-summary h3 { margin-top: 0; color: #882430; }
        .summary-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .summary-item { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 12px; }
        .summary-label { display: block; font-size: 12px; color: #777; font-weight: 700; text-transform: uppercase; }
        .summary-value { display: block; font-size: 16px; font-weight: 700; color: #222; margin-top: 4px; }
        .section-title { margin: 24px 0 12px; color: #882430; font-weight: 700; }
        .passenger-card { border: 1px solid #e7e7e7; border-radius: 10px; padding: 16px; margin-bottom: 16px; background: #fff; }
        .form-control, .input-text { border-radius: 6px; }
        .payment-box { border: 1px solid #e7e7e7; border-radius: 10px; padding: 16px; background: #fff; }
        .checkout-btn { background: #882430; color: #fff; border: 0; border-radius: 8px; padding: 14px 18px; font-weight: 700; width: 100%; }
        .sidebar-card { background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.08); padding: 18px; }
        .sidebar-card h3 { margin-top: 0; color: #882430; }
        .fare-line { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .fare-total { font-size: 24px; font-weight: 800; color: #882430; margin-top: 10px; }
        .muted { color: #777; }
        @media (max-width: 768px) {
            .summary-grid { grid-template-columns: 1fr; }
            .btn-medium.signupbtn { float: none; display: inline-block; margin-top: 12px; }
        }
    </style>
</head>
<body>
    <div class="page-title-container">
        <div class="container">
            <div class="page-title pull-left">
                <h2 class="entry-title">Your Booking details</h2>
            </div>
            <a href="index.php" class="btn-medium signupbtn">Book a Train</a>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="booking-shell">
                    <div class="booking-summary">
                        <h3>Booking Summary</h3>
                        <div class="summary-grid">
                            <div class="summary-item"><span class="summary-label">From</span><span class="summary-value"><?php echo $fromSafe; ?></span></div>
                            <div class="summary-item"><span class="summary-label">To</span><span class="summary-value"><?php echo $toSafe; ?></span></div>
                            <div class="summary-item"><span class="summary-label">Departure Date</span><span class="summary-value"><?php echo $dateSafe; ?></span></div>
                            <div class="summary-item"><span class="summary-label">Train Type</span><span class="summary-value"><?php echo $trainTypeSafe; ?></span></div>
                            <div class="summary-item"><span class="summary-label">Coach Type</span><span class="summary-value"><?php echo $coachTypeSafe; ?></span></div>
                        </div>
                    </div>

                    <form id="bookingPaymentForm" class="booking-form-update booking-ajax-form" action="review-and-pay.php" method="post" role="form">
                        <h3 class="section-title">Passenger Details</h3>

                        <div class="passenger-card">
                            <h4>Adult Details - 1</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Full name - Passenger 1</label>
                                    <input type="text" class="form-control" name="fullnames[1]" required>
                                </div>
                                <div class="col-sm-6">
                                    <label>ID card no / passport no</label>
                                    <input type="text" class="form-control" name="ids[1]" required>
                                </div>
                            </div>
                        </div>

                        <div class="passenger-card">
                            <h4>Child Details [12-17 YEARS] - 1</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Full name - Passenger 2</label>
                                    <input type="text" class="form-control" name="fullnames[2]">
                                </div>
                                <div class="col-sm-6">
                                    <label>Guardian ID / Passport No</label>
                                    <input type="text" class="form-control" name="ids[2]">
                                </div>
                            </div>
                        </div>

                        <h3 class="section-title">Payment Details</h3>
                        <div class="payment-box">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>m-Pesa Mobile No</label>
                                    <input type="text" class="form-control" name="pay_mobile" required>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="leaving_from" value="<?php echo $fromSafe; ?>">
                        <input type="hidden" name="going_to" value="<?php echo $toSafe; ?>">
                        <input type="hidden" name="travel_date" value="<?php echo $dateSafe; ?>">
                        <input type="hidden" name="train_type" value="<?php echo $trainTypeSafe; ?>">
                        <input type="hidden" name="coach_type" value="<?php echo $coachTypeSafe; ?>">
                        <input type="hidden" name="adults" value="<?php echo $adultsSafe; ?>">
                        <input type="hidden" name="child1" value="<?php echo $child1Safe; ?>">
                        <input type="hidden" name="child2" value="<?php echo $child2Safe; ?>">
                        <input type="hidden" name="totalFare" value="<?php echo $totalSafe; ?>">
                        <input type="hidden" name="mpesa_checkout_request_id" value="">
                        <input type="hidden" name="mpesa_merchant_request_id" value="">

                        <div class="row" style="margin-top:24px;">
                            <div class="col-sm-6">
                                <button type="submit" id="proceedToPayment" class="checkout-btn">Proceed to payment</button>
                            </div>
                        </div>

                        <div id="paymentStatus" class="alert" style="display:none; margin-top:16px;"></div>
                    </form>
                </div>
            </div>

            <div class="col-md-3">
                <div class="sidebar-card">
                    <h3><?php echo $fromSafe; ?> to <?php echo $toSafe; ?></h3>
                    <p class="muted">Train type: <?php echo $trainTypeSafe; ?></p>
                    <div class="fare-line"><span>Date</span><span><?php echo $travelDateText; ?></span></div>
                    <div class="fare-line"><span>Adults</span><span><?php echo $adultsSafe; ?></span></div>
                    <div class="fare-line"><span>Children 12-17</span><span><?php echo $child1Safe; ?></span></div>
                    <div class="fare-line"><span>Children 3-11</span><span><?php echo $child2Safe; ?></span></div>
                    <div class="fare-line"><span>Total Fare</span><span>KSH <?php echo $totalSafe; ?></span></div>
                    <div class="fare-total">KSH <?php echo $totalSafe; ?></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(function() {
            var $form = jQuery('#bookingPaymentForm');
            var $button = jQuery('#proceedToPayment');
            var $status = jQuery('#paymentStatus');

            $form.on('submit', function(event) {
                event.preventDefault();
                initiateMpesaStkPush();
            });

            function normalizePhone(phone) {
                var value = String(phone || '').replace(/\s+/g, '').replace(/[^0-9+]/g, '');
                if (value.indexOf('+254') === 0) {
                    value = value.substring(1);
                } else if (value.indexOf('0') === 0) {
                    value = '254' + value.substring(1);
                } else if (value.indexOf('7') === 0 || value.indexOf('1') === 0) {
                    value = '254' + value;
                }
                return value;
            }

            function parseAmount(amount) {
                var numeric = String(amount || '').replace(/[^0-9.]/g, '');
                if (!numeric || isNaN(Number(numeric))) {
                    return 0;
                }
                return Math.round(Number(numeric));
            }

            function showStatus(message, isError) {
                $status
                    .removeClass('alert-success alert-danger')
                    .addClass(isError ? 'alert-danger' : 'alert-success')
                    .text(message)
                    .show();
            }

            function initiateMpesaStkPush() {
                var mobile = normalizePhone($form.find('[name="pay_mobile"]').val());
                var amount = parseAmount($form.find('[name="totalFare"]').val());

                if (!mobile || !/^254(7|1)\d{8}$/.test(mobile)) {
                    showStatus('Enter a valid Safaricom mobile number.', true);
                    return;
                }

                if (!amount || amount <= 0) {
                    showStatus('Total fare is missing or invalid.', true);
                    return;
                }

                $button.prop('disabled', true).text('Sending M-Pesa prompt...');
                showStatus('Sending STK push to ' + mobile + ' for KSH ' + amount + '...', false);

                var payload = $form.serializeArray();
                payload.push({ name: 'phone', value: mobile });
                payload.push({ name: 'pay_mobile', value: mobile });
                payload.push({ name: 'amount', value: amount });

                jQuery.ajax({
                    url: 'mpesa/stk-push.php',
                    method: 'POST',
                    data: payload,
                    dataType: 'json'
                }).done(function(response) {
                    if (response && response.success) {
                        if (response.checkoutRequestID) {
                            $form.find('[name="mpesa_checkout_request_id"]').val(response.checkoutRequestID);
                        }
                        if (response.merchantRequestID) {
                            $form.find('[name="mpesa_merchant_request_id"]').val(response.merchantRequestID);
                        }

                        $form.off('submit');
                        $form.get(0).submit();
                        return;
                    }

                    showStatus((response && response.message) ? response.message : 'Unable to initiate STK push.', true);
                    $button.prop('disabled', false).text('Proceed to payment');
                }).fail(function(xhr) {
                    var message = 'Unable to initiate STK push.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showStatus(message, true);
                    $button.prop('disabled', false).text('Proceed to payment');
                });
            }
        });
    </script>
</body>
</html>
