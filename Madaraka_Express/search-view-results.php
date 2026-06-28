<?php
$stationMap = [
    '2' => 'Nairobi Terminus',
    '3' => 'Mombasa Terminus',
    '4' => 'Voi',
    '5' => 'Mtito Andei',
    '6' => 'Mariakani',
    '7' => 'Miasenyi',
    '9' => 'Kibwezi',
    '10' => 'Emali',
    '11' => 'Athi River',
    '12' => 'Ongata Rongai',
    '13' => 'Ngong',
    '14' => 'Maai Mahiu',
    '15' => 'Suswa'
];

function getInputValue($key, $default = '') {
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
        return trim((string)$_POST[$key]);
    }
    if (isset($_GET[$key]) && $_GET[$key] !== '') {
        return trim((string)$_GET[$key]);
    }
    return $default;
}

$fromRaw = getInputValue('from', getInputValue('terminal_id', 'Nairobi Terminus'));
$toRaw = getInputValue('to', getInputValue('destination_id', 'Mombasa Terminus'));
$dateRaw = getInputValue('date', getInputValue('travel-date', date('Y-m-d')));

$from = isset($stationMap[$fromRaw]) ? $stationMap[$fromRaw] : $fromRaw;
$to = isset($stationMap[$toRaw]) ? $stationMap[$toRaw] : $toRaw;

$dateObj = DateTime::createFromFormat('m/d/Y', $dateRaw);
if ($dateObj !== false) {
    $date = $dateObj->format('Y-m-d');
} else {
    $date = $dateRaw;
}

$safeFrom = htmlspecialchars($from, ENT_QUOTES, 'UTF-8');
$safeTo = htmlspecialchars($to, ENT_QUOTES, 'UTF-8');
$safeDate = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | Madaraka Express</title>
    <style>
        :root {
            --brand-red: #8d2332;
            --brand-orange: #ff7a1f;
            --soft-bg: #f7f7f7;
            --text-dark: #212121;
            --text-muted: #6c6c6c;
            --card-bg: #ffffff;
            --line: #e2e2e2;
            --shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--soft-bg);
            color: var(--text-dark);
            font-family: "Source Sans Pro", Arial, sans-serif;
        }

        .results-banner-red {
            background: var(--brand-red);
            color: #fff;
            padding: 18px 20px;
        }

        .banner-inner {
            max-width: 1180px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .banner-text {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .banner-top-btn,
        .search-again-btn,
        .sidebar-book-btn {
            border: 0;
            background: var(--brand-orange);
            color: #fff;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
        }

        .banner-top-btn {
            padding: 10px 18px;
            font-size: 15px;
        }

        .results-main-wrapper {
            max-width: 1180px;
            margin: 22px auto;
            padding: 0 14px 20px;
        }

        .summary-breadcrumb-section,
        .modify-search-container,
        .premium-timeline-card,
        .booking-sidebar-box {
            background: var(--card-bg);
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .summary-breadcrumb-section {
            padding: 16px;
            margin-bottom: 14px;
        }

        .summary-intro-label {
            margin: 0 0 8px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .summary-flex-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .summary-tag {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 8px 14px;
            background: #fff;
            font-size: 14px;
        }

        .tag-label {
            font-weight: 700;
            color: var(--brand-red);
        }

        .modify-search-container {
            margin-bottom: 14px;
            overflow: hidden;
        }

        .modify-search-bar-toggle {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-weight: 700;
        }

        .modify-plus-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1px solid var(--line);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .modify-search-dropdown {
            padding: 14px 16px 18px;
        }

        .mod-trip-type-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .mod-ver-line {
            width: 1px;
            height: 20px;
            background: var(--line);
        }

        .mod-radio {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .modify-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
        }

        .mod-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .mod-group select,
        .mod-group input {
            width: 100%;
            border: 1px solid #d4d4d4;
            border-radius: 6px;
            padding: 10px;
            min-height: 42px;
            background: #fff;
        }

        .btn-group {
            align-self: end;
        }

        .search-again-btn,
        .sidebar-book-btn {
            width: 100%;
            padding: 12px;
            font-size: 15px;
        }

        .main-booking-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr);
            gap: 16px;
        }

        .premium-card-body {
            padding: 18px;
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) 1px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
        }

        .premium-journey-header {
            margin: 0 0 14px;
            color: var(--brand-red);
        }

        .timeline-point {
            display: grid;
            grid-template-columns: 90px 28px 1fr;
            align-items: start;
            gap: 8px;
        }

        .time-main {
            font-size: 22px;
            font-weight: 700;
            display: block;
        }

        .date-sub {
            font-size: 12px;
            color: var(--text-muted);
        }

        .dot-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 60px;
        }

        .orange-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--brand-orange);
            margin-top: 8px;
        }

        .vertical-line {
            width: 2px;
            flex: 1;
            background: #f6b17f;
            margin-top: 4px;
        }

        .duration-marker {
            margin: 10px 0 10px 100px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .vertical-divider-grey {
            background: var(--line);
            min-height: 100%;
        }

        .p-price-list {
            display: grid;
            gap: 10px;
        }

        .p-price-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 10px;
        }

        .p-label {
            font-weight: 600;
            color: var(--text-muted);
        }

        .p-value {
            font-weight: 700;
            color: var(--brand-red);
        }

        .booking-sidebar-box {
            padding: 16px;
        }

        .sidebar-route-title {
            margin: 0;
            color: var(--brand-red);
        }

        .sidebar-train-id {
            margin: 6px 0 14px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .sidebar-inputs-flex {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .sidebar-field label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .sidebar-field input,
        .sidebar-field select {
            width: 100%;
            border: 1px solid #d4d4d4;
            border-radius: 6px;
            min-height: 40px;
            padding: 8px;
            background: #fff;
        }

        .full-width {
            margin-top: 12px;
        }

        .sidebar-total-fare-display {
            margin: 16px 0;
            padding: 12px;
            background: #fff7f2;
            border: 1px solid #ffd2ba;
            border-radius: 8px;
            text-align: center;
        }

        .total-fare-label {
            margin: 0;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 700;
        }

        .total-fare-value {
            margin: 6px 0 0;
            color: var(--brand-red);
            font-size: 30px;
        }

        .return-label-heading {
            color: var(--brand-red);
            margin: 4px 0 10px;
        }

        @media (max-width: 1024px) {
            .main-booking-grid {
                grid-template-columns: 1fr;
            }

            .premium-card-body {
                grid-template-columns: 1fr;
            }

            .vertical-divider-grey {
                display: none;
            }

            .modify-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .banner-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .banner-text {
                font-size: 24px;
            }

            .modify-grid,
            .sidebar-inputs-flex {
                grid-template-columns: 1fr;
            }

            .timeline-point {
                grid-template-columns: 75px 22px 1fr;
            }

            .duration-marker {
                margin-left: 78px;
            }
        }
    </style>
</head>
<body>
<div class="results-banner-red">
    <div class="banner-inner">
        <h2 class="banner-text">Search Results</h2>
        <button class="banner-top-btn" onclick="window.location.href='index.php'">Book a Train</button>
    </div>
</div>

<div class="results-main-wrapper">
    <div class="summary-breadcrumb-section">
        <p class="summary-intro-label">Your Search:</p>
        <div class="summary-flex-container">
            <div class="summary-tag"><span class="tag-label">FROM :</span> <span class="tag-value" id="res-from"><?php echo $safeFrom; ?></span></div>
            <div class="summary-tag"><span class="tag-label">TO :</span> <span class="tag-value" id="res-to"><?php echo $safeTo; ?></span></div>
            <div class="summary-tag"><span class="tag-label">DEPARTURE DATE :</span> <span class="tag-value" id="res-date"><?php echo $safeDate; ?></span></div>
        </div>
    </div>

    <div class="modify-search-container">
        <div class="modify-search-bar-toggle" onclick="toggleModifySearch()">
            <span class="modify-label">Modify Search</span>
            <span class="modify-plus-btn" id="modify-icon">+</span>
        </div>
        <div id="modify-search-content" class="modify-search-dropdown" style="display: none;">
            <div class="mod-trip-type-row">
                <label class="mod-radio"><input type="radio" name="mod_trip" value="one-way" checked onclick="handleTripChange()"><span class="radio-custom"></span> One way</label>
                <div class="mod-ver-line"></div>
                <label class="mod-radio"><input type="radio" name="mod_trip" value="return" onclick="handleTripChange()"><span class="radio-custom"></span> Return trip</label>
            </div>
            <div class="modify-grid">
                <div class="mod-group"><label>TRAIN TYPE</label><select id="mod_train_type" onchange="updateStationLists()"><option value="Inter-County">Inter-County</option><option value="Express">Express</option></select></div>
                <div class="mod-group"><label>FROM</label><select id="mod_from_station" onchange="updateStationLists()"></select></div>
                <div class="mod-group"><label>TO</label><select id="mod_to_station" onchange="preventSameStation('to')"></select></div>
                <div class="mod-group"><label>DEPARTURE DATE</label><input type="date" id="mod_departure_date"></div>
                <div class="mod-group" id="mod_dep_time_group" style="display:none;"><label>DEPARTURE TIME</label><select id="mod_departure_time"><option value="03:00 pm">03:00 pm</option><option value="10:00 pm">10:00 pm</option></select></div>
                <div class="mod-group btn-group" id="btn-oneway-position"><button class="search-again-btn" onclick="executeNewPremiumSearch()" type="button">Search again</button></div>
            </div>
            <div id="return-trip-row" style="display:none; margin-top: 15px;">
                <h4 class="return-label-heading">Return trip</h4>
                <div class="modify-grid">
                    <div class="mod-group"><label>TRAIN TYPE</label><select id="ret_train_type" onchange="updateStationLists()"><option value="Inter-County">Inter-County</option><option value="Express">Express</option></select></div>
                    <div class="mod-group"><label>RETURN DATE</label><input type="date" id="mod_return_date"></div>
                    <div class="mod-group" id="ret_time_group" style="display:none;"><label>RETURN TIME</label><select id="ret_departure_time"><option value="03:00 pm">03:00 pm</option><option value="10:00 pm">10:00 pm</option></select></div>
                    <div class="mod-group"></div><div class="mod-group"></div>
                    <div class="mod-group btn-group"><button class="search-again-btn" onclick="executeNewPremiumSearch()" type="button">Search again</button></div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-booking-grid">
        <div class="pricing-card-area">
            <div class="premium-timeline-card">
                <div class="premium-card-body">
                    <div class="timeline-visual-col">
                        <h3 class="premium-journey-header" id="p-route-header">... - ...</h3>
                        <div class="timeline-point">
                            <div class="time-box"><span class="time-main" id="dep-time-display">08:22</span><span class="date-sub" id="p-start-date">...</span></div>
                            <div class="dot-wrapper"><span class="orange-dot"></span><span class="vertical-line"></span></div>
                            <span class="station-name" id="p-start-station">...</span>
                        </div>
                        <div class="duration-marker"><span class="duration-text" id="p-duration">2h 28m</span><span class="train-icon">Train</span></div>
                        <div class="timeline-point">
                            <div class="time-box"><span class="time-main" id="arr-time-display">10:50</span></div>
                            <div class="dot-wrapper"><span class="orange-dot"></span></div>
                            <span class="station-name" id="p-end-station">...</span>
                        </div>
                    </div>
                    <div class="vertical-divider-grey"></div>
                    <div class="premium-pricing-col">
                        <div class="p-price-list">
                            <div class="p-price-item"><span class="p-label">ADULTS</span><span class="p-value" id="card-price-premium-a">KSH --</span></div>
                            <div class="p-price-item"><span class="p-label">CHILDREN (3-11YRS)</span><span class="p-value" id="card-price-premium-c">KSH --</span></div>
                            <div class="p-price-item"><span class="p-label">CHILDREN (BELOW 3YRS)</span><span class="p-value">KSH 0 - FREE</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar-calculation-area">
            <div class="booking-sidebar-box">
                <h4 class="sidebar-route-title" id="side-route-title">...</h4>
                <p class="sidebar-train-id" id="disp-train-id">TRAIN : --</p>
                <div class="sidebar-inputs-flex">
                    <div class="sidebar-field"><label>ADULTS</label><input type="number" id="adults" value="0" min="0" oninput="calculateTotal()"></div>
                    <div class="sidebar-field"><label>CHILDREN (12-17)</label><input type="number" id="child1" value="0" min="0" oninput="calculateTotal()"></div>
                    <div class="sidebar-field"><label>CHILDREN (3-11)</label><input type="number" id="child2" value="0" min="0" oninput="calculateTotal()"></div>
                </div>
                <div class="sidebar-field full-width"><label>COACH TYPE</label><select id="coach-type" onchange="calculateTotal()"><option value="Premium">PREMIUM</option><option value="First Class">FIRST CLASS</option><option value="Economy Class">ECONOMY CLASS</option></select></div>
                <div class="sidebar-total-fare-display"><p class="total-fare-label">TOTAL FARE:</p><h2 class="total-fare-value" id="total-price">KSH 0</h2></div>
                <button class="sidebar-book-btn" onclick="goToBookingDetails()" type="button">Book a Train</button>
            </div>
        </div>
    </div>
</div>

<script>
    const premiumFareData = {
        "Nairobi-Emali": 3180, "Nairobi-Kibwezi": 4860, "Nairobi-Mtito Andei": 5980, "Nairobi-Voi": 8540, "Nairobi-Miasenyi": 9820, "Nairobi-Mariakani": 11500, "Nairobi-Mombasa": 12000,
        "Athi River-Nairobi": 480, "Athi River-Emali": 2640, "Athi River-Kibwezi": 4380, "Athi River-Mtito Andei": 5500, "Athi River-Voi": 8060, "Athi River-Miasenyi": 9340, "Athi River-Mariakani": 11020, "Athi River-Mombasa": 11580,
        "Emali-Kibwezi": 1740, "Emali-Mtito Andei": 2860, "Emali-Voi": 5360, "Emali-Miasenyi": 6700, "Emali-Mariakani": 8380, "Emali-Mombasa": 8940,
        "Kibwezi-Mtito Andei": 940, "Kibwezi-Voi": 3680, "Kibwezi-Miasenyi": 4960, "Kibwezi-Mariakani": 6700, "Kibwezi-Mombasa": 7200,
        "Mtito Andei-Voi": 2560, "Mtito Andei-Miasenyi": 3820, "Mtito Andei-Mariakani": 5520, "Mtito Andei-Mombasa": 6080,
        "Voi-Miasenyi": 1280, "Voi-Mariakani": 2960, "Voi-Mombasa": 3520,
        "Miasenyi-Mariakani": 1740, "Miasenyi-Mombasa": 2240,
        "Mariakani-Mombasa": 560
    };

    const stations = {
        "Express": ["Nairobi Terminus", "Mombasa Terminus", "Voi"],
        "Inter-County": ["Nairobi Terminus", "Athi River", "Emali", "Kibwezi", "Mtito Andei", "Voi", "Miasenyi", "Mariakani", "Mombasa Terminus"]
    };

    window.onload = function() {
        const params = new URLSearchParams(window.location.search);
        const from = params.get('from') || document.getElementById('res-from').innerText || "Nairobi Terminus";
        const to = params.get('to') || document.getElementById('res-to').innerText || "Mombasa Terminus";
        const date = params.get('date') || document.getElementById('res-date').innerText || "2026-05-07";

        document.getElementById('res-from').innerText = from;
        document.getElementById('res-to').innerText = to;
        document.getElementById('res-date').innerText = date;
        document.getElementById('p-route-header').innerText = from + " to " + to;
        document.getElementById('p-start-station').innerText = from;
        document.getElementById('p-end-station').innerText = to;
        document.getElementById('p-start-date').innerText = date;
        document.getElementById('side-route-title').innerText = from + " to " + to;

        document.getElementById('mod_departure_date').value = date;
        document.getElementById('mod_return_date').value = date;

        updateStationLists();

        if (Array.from(document.getElementById('mod_from_station').options).some(function(o){ return o.value === from; })) {
            document.getElementById('mod_from_station').value = from;
            updateStationLists();
        }
        if (Array.from(document.getElementById('mod_to_station').options).some(function(o){ return o.value === to; })) {
            document.getElementById('mod_to_station').value = to;
        }

        calculateTotal();
    };

    function updateStationLists() {
        const type = document.getElementById('mod_train_type').value;
        const retType = document.getElementById('ret_train_type').value;
        const fromS = document.getElementById('mod_from_station');
        const toS = document.getElementById('mod_to_station');

        document.getElementById('mod_dep_time_group').style.display = (type === 'Express') ? 'block' : 'none';
        document.getElementById('ret_time_group').style.display = (retType === 'Express') ? 'block' : 'none';

        const currentFrom = fromS.value;
        const currentTo = toS.value;

        fromS.innerHTML = '';
        stations[type].forEach(function(st){ fromS.add(new Option(st, st)); });
        if (currentFrom && Array.from(fromS.options).some(function(o){ return o.value === currentFrom; })) {
            fromS.value = currentFrom;
        }

        const selectedFrom = fromS.value;
        toS.innerHTML = '';
        stations[type].forEach(function(st) {
            if (st === selectedFrom) return;
            if (selectedFrom === "Nairobi Terminus" && st === "Athi River") return;
            if (selectedFrom === "Mombasa Terminus" && st === "Mariakani") return;
            toS.add(new Option(st, st));
        });

        if (currentTo && Array.from(toS.options).some(function(o){ return o.value === currentTo; })) {
            toS.value = currentTo;
        }
    }

    const coachFareRules = {
        'Premium': { multiplier: 1.0, childMultiplier: 0.5 },
        'First Class': { multiplier: 1.25, childMultiplier: 0.5 },
        'Economy Class': { multiplier: 0.75, childMultiplier: 0.5 }
    };

    function calculateTotal() {
        const from = document.getElementById('res-from').innerText.replace(" Terminus", "").trim();
        const to = document.getElementById('res-to').innerText.replace(" Terminus", "").trim();
        const route = from + "-" + to;
        const reverse = to + "-" + from;
        const base = premiumFareData[route] || premiumFareData[reverse] || 0;
        const coachType = document.getElementById('coach-type').value;
        const fareRule = coachFareRules[coachType] || coachFareRules['Premium'];
        const adultFare = base * fareRule.multiplier;
        const childFare = adultFare * fareRule.childMultiplier;

        const adults = parseInt(document.getElementById('adults').value, 10) || 0;
        const c12 = parseInt(document.getElementById('child1').value, 10) || 0;
        const c3 = parseInt(document.getElementById('child2').value, 10) || 0;

        document.getElementById('card-price-premium-a').innerText = adultFare > 0 ? "KSH " + Math.round(adultFare) : "N/A";
        document.getElementById('card-price-premium-c').innerText = childFare > 0 ? "KSH " + Math.round(childFare) : "N/A";

        const total = ((adults + c12) * adultFare) + (c3 * childFare);
        document.getElementById('total-price').innerText = "KSH " + total.toLocaleString();
    }

    function sendToWhatsApp() {
        const from = document.getElementById('res-from').innerText;
        const to = document.getElementById('res-to').innerText;
        const date = document.getElementById('res-date').innerText;
        const type = document.getElementById('mod_train_type').value;
        const coachType = document.getElementById('coach-type').value;
        const total = document.getElementById('total-price').innerText;
        const depTime = document.getElementById('dep-time-display').innerText;
        const arrTime = document.getElementById('arr-time-display').innerText;

        const adults = document.getElementById('adults').value || 0;
        const children12_17 = document.getElementById('child1').value || 0;
        const children3_11 = document.getElementById('child2').value || 0;

        let msg = '*MADARAKA EXPRESS PREMIUM BOOKING (ONE-WAY)*\n\n';
        msg += '*JOURNEY DETAILS*\n--------------------------\n';
        msg += '*Train Type:* ' + type + '\n*Route:* ' + from + ' to ' + to + '\n*Date:* ' + date + '\n';
        msg += '*Departure Time:* ' + depTime + '\n*Arrival Time:* ' + arrTime + '\n*Coach:* ' + coachType + '\n\n';
        msg += '*PASSENGER SUMMARY*\n--------------------------\n';
        msg += '*Adults:* ' + adults + '\n*Children (12-17 Yrs):* ' + children12_17 + '\n*Children (3-11 Yrs):* ' + children3_11 + '\n\n';
        msg += '*TOTAL FARE: ' + total + '*';

        window.open('https://wa.me/254789313930?text=' + encodeURIComponent(msg), '_blank');
    }

    function goToBookingDetails() {
        const params = new URLSearchParams({
            from: document.getElementById('res-from').innerText,
            to: document.getElementById('res-to').innerText,
            date: document.getElementById('res-date').innerText,
            train_type: document.getElementById('mod_train_type').value,
            coach_type: document.getElementById('coach-type').value,
            adults: document.getElementById('adults').value || '0',
            child1: document.getElementById('child1').value || '0',
            child2: document.getElementById('child2').value || '0',
            total: document.getElementById('total-price').innerText.replace('KSH ', '')
        });

        window.location.href = 'booking-details.php?' + params.toString();
    }

    function toggleModifySearch() {
        const c = document.getElementById('modify-search-content');
        const icon = document.getElementById('modify-icon');
        const isHidden = c.style.display === 'none';
        c.style.display = isHidden ? 'block' : 'none';
        icon.innerText = isHidden ? '-' : '+';
    }

    function handleTripChange() {
        const isR = document.querySelector('input[name="mod_trip"]:checked').value === 'return';
        document.getElementById('return-trip-row').style.display = isR ? 'block' : 'none';
        document.getElementById('btn-oneway-position').style.display = isR ? 'none' : 'block';
        updateStationLists();
    }

    function preventSameStation() {
        updateStationLists();
    }

    function executeNewPremiumSearch() {
        const isR = document.querySelector('input[name="mod_trip"]:checked').value === 'return';
        const p = new URLSearchParams({
            from: document.getElementById('mod_from_station').value,
            to: document.getElementById('mod_to_station').value,
            date: document.getElementById('mod_departure_date').value,
            mode: 'premium'
        });

        window.location.href = (isR ? 'search-view-results-return.php?' : 'search-view-results.php?') + p.toString();
    }
</script>
</body>
</html>
