<?php
// Shared booking rules: capacity, vet-visit check, pricing.
// Used by pages/appointment.php, employeeAccount/newappointment.php and employeeAccount/edit.php

const MAX_PER_DAY = 5;

const PRICE_GROOMING = 40;   // flat per appointment
const PRICE_TRAINING = 60;   // flat per appointment
const PRICE_BOARDING = 50;   // per night (minimum 1 night)

// First day in the range that already has MAX_PER_DAY appointments, or null if every day has room.
// An appointment counts toward every day from its start date to its end date.
// $excludeAid lets an appointment being edited ignore itself.
function findFullDay($con, $start, $end, $excludeAid = 0) {
    $stmt = mysqli_prepare($con, "SELECT COUNT(*) FROM appointment
                                  WHERE DATE(StartDate) <= ? AND DATE(EndDate) >= ? AND AId <> ?");
    $day = new DateTime($start);
    $last = new DateTime($end);
    while ($day <= $last) {
        $d = $day->format('Y-m-d');
        mysqli_stmt_bind_param($stmt, "ssi", $d, $d, $excludeAid);
        mysqli_stmt_execute($stmt);
        $count = (int)mysqli_fetch_row(mysqli_stmt_get_result($stmt))[0];
        if ($count >= MAX_PER_DAY) {
            return $d;
        }
        $day->modify('+1 day');
    }
    return null;
}

// Pet may only be booked if its last vet appointment was within the past year.
function vetStatus($con, $pid) {
    $pid = (int)$pid;
    $row = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT LastVetApp, (LastVetApp >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) AS ok FROM pet WHERE PId = $pid"));
    return ['ok' => !empty($row['ok']), 'last' => $row['LastVetApp'] ?? null];
}

function nights($start, $end) {
    return max(1, (new DateTime($start))->diff(new DateTime($end))->days);
}

// Returns ['lines' => [label => amount], 'total' => amount]
function calcTotal($g, $b, $t, $start, $end) {
    $lines = [];
    if ($g) { $lines['Grooming'] = PRICE_GROOMING; }
    if ($t) { $lines['Training'] = PRICE_TRAINING; }
    if ($b) {
        $n = nights($start, $end);
        $lines["Boarding ($n night" . ($n > 1 ? 's' : '') . ")"] = PRICE_BOARDING * $n;
    }
    return ['lines' => $lines, 'total' => array_sum($lines)];
}

// Live "estimated total" box for booking forms (needs inputs named Grooming, Boarding, Training, StartDate, EndDate)
function priceBox() {
    $g = PRICE_GROOMING; $b = PRICE_BOARDING; $t = PRICE_TRAINING;
    return <<<HTML
<div id="price-box" style="border:1px solid #888; padding:8px; margin:10px 0; max-width:320px;">
    <strong>Estimated total: \$<span id="total">0.00</span></strong>
    <div id="breakdown" style="font-size:0.9em;"></div>
    <small>Grooming \${$g}, Training \${$t}, Boarding \${$b}/night</small>
</div>
<script>
function updateTotal() {
    var q = function (n) { return document.querySelector('[name=' + n + ']'); };
    var lines = [], total = 0;
    if (q('Grooming').checked) { lines.push('Grooming: \${$g}'); total += {$g}; }
    if (q('Training').checked) { lines.push('Training: \${$t}'); total += {$t}; }
    if (q('Boarding').checked) {
        var s = q('StartDate').value, e = q('EndDate').value, n = 1;
        if (s && e) { n = Math.max(1, Math.round((new Date(e) - new Date(s)) / 86400000)); }
        lines.push('Boarding: ' + n + ' night' + (n > 1 ? 's' : '') + ' x \${$b} = \$' + (n * {$b}));
        total += n * {$b};
    }
    document.getElementById('total').textContent = total.toFixed(2);
    document.getElementById('breakdown').innerHTML = lines.join('<br>');
}
['Grooming', 'Boarding', 'Training', 'StartDate', 'EndDate'].forEach(function (n) {
    document.querySelector('[name=' + n + ']').addEventListener('change', updateTotal);
});
updateTotal();
</script>
HTML;
}
