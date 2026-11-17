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

// Pet may only be booked if its last vet appointment was within the past year and it is vaccinated.
function vetStatus($con, $pid) {
    $pid = (int)$pid;
    $row = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT LastVetApp,
                IsVaccinated,
                (LastVetApp >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) AS vet_ok
         FROM pet WHERE PId = $pid"));
    $vetOk = !empty($row['vet_ok']);
    $vaccinated = !empty($row['IsVaccinated']);

    return [
        'ok' => ($vetOk && $vaccinated),
        'vet_ok' => $vetOk,
        'vaccinated' => $vaccinated,
        'last' => $row['LastVetApp'] ?? null,
    ];
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
<div id="price-box">
    <strong>Estimated total: \$<span id="total">0.00</span></strong>
    <div id="breakdown" style="font-size:0.9em;"></div>
    <h5>(Grooming \${$g}, Training \${$t}, Boarding \${$b}/night)</h5>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var q = function (n) { return document.querySelector('[name="' + n + '"]'); };
    function updateTotal() {
        var lines = [], total = 0;
        var gEl = q('Grooming'), tEl = q('Training'), bEl = q('Boarding');
        if (gEl && gEl.checked) { lines.push('Grooming: \${$g}'); total += {$g}; }
        if (tEl && tEl.checked) { lines.push('Training: \${$t}'); total += {$t}; }
        if (bEl && bEl.checked) {
            var s = q('StartDate') ? q('StartDate').value : '';
            var e = q('EndDate') ? q('EndDate').value : '';
            var n = 1;
            if (s && e) {
                var startDay = new Date(s.slice(0, 10));
                var endDay = new Date(e.slice(0, 10));
                n = Math.max(1, Math.round((endDay - startDay) / 86400000));
            }
            lines.push('Boarding: ' + n + ' night' + (n > 1 ? 's' : '') + ' x \${$b} = \$' + (n * {$b}));
            total += n * {$b};
        }
        document.getElementById('total').textContent = total.toFixed(2);
        document.getElementById('breakdown').innerHTML = lines.join('<br>');
    }
    ['Grooming', 'Boarding', 'Training', 'StartDate', 'EndDate'].forEach(function (n) {
        var el = q(n);
        if (el) { el.addEventListener('change', updateTotal); }
    });
    updateTotal();
});
</script>
HTML;
}