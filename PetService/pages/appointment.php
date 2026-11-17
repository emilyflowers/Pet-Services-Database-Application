<?php
session_start();
if (empty($_SESSION['OId'])) {
    header("Location: ../userAccount/login.php");
    exit;
}
if (empty($_SESSION['PId'])) {
    header("Location: ../userAccount/ownerHome.php");
    exit;
}

require_once __DIR__ . '/../database/booking.php';

$con = mysqli_connect("localhost", "root", "", "petservices_db");
$oid = (int)$_SESSION['OId'];
$pid = (int)$_SESSION['PId'];

// Make sure this pet belongs to the logged-in owner
$pet = mysqli_fetch_assoc(mysqli_query($con, "SELECT Name FROM pet WHERE PId = $pid AND OId = $oid"));
if (!$pet) {
    header("Location: ../userAccount/ownerHome.php");
    exit;
}

$error = '';
$success = '';
$g = 0;
$b = 0;
$t = 0;
$start = '';
$end = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $g = isset($_POST['Grooming']) ? 1 : 0;
    $b = isset($_POST['Boarding']) ? 1 : 0;
    $t = isset($_POST['Training']) ? 1 : 0;
    $start = $_POST['StartDate'] ?? '';
    $end = $_POST['EndDate'] ?? '';

    if (!$g && !$b && !$t) {
        $error = "Please choose at least one service.";
    } elseif ($start === '' || $end === '') {
        $error = "Please choose a start and end date.";
    } elseif ($end < $start) {
        $error = "The end date can't be before the start date.";
    } elseif ($start < date('Y-m-d')) {
        $error = "The start date can't be in the past.";
    } elseif (!($vet = vetStatus($con, $pid))['ok']) {
        $issues = [];
        if (!$vet['vaccinated']) {
            $issues[] = "be up-to-date on vaccinations";
        }
        if (!$vet['vet_ok']) {
            $last = $vet['last'] ? "last visit: {$vet['last']}" : "no vet visit on file";
            $issues[] = "have a vet visit within the past year ({$last})";
        }
        $error = "This pet must " . implode(" and ", $issues) . " before booking.";
    } elseif (($fullDay = findFullDay($con, $start, $end)) !== null) {
        $error = "Fully booked on {$fullDay} (maximum " . MAX_PER_DAY . " appointments per day).";
    } else {
        // Assign a random employee
        $emp = mysqli_fetch_assoc(mysqli_query($con, "SELECT EId FROM employee ORDER BY RAND() LIMIT 1"));
        if (!$emp) {
            $error = "No employees are currently available to assign.";
        } else {
            $eid = (int)$emp['EId'];
            $stmt = mysqli_prepare($con, "INSERT INTO appointment (PId, EId, Grooming, Boarding, Training, StartDate, EndDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiiiiss", $pid, $eid, $g, $b, $t, $start, $end);

            if (mysqli_stmt_execute($stmt)) {
                $totals = calcTotal($g, $b, $t, $start, $end);
                $success = "Appointment booked for " . htmlspecialchars($pet['Name']) . "! Total: $" . number_format($totals['total'], 2);
                $g = $b = $t = 0;
                $start = $end = '';
            } else {
                $error = "Could not book the appointment. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../pages/style.css">
    <title>Schedule Appointment</title>
</head>
<body>
    <h1 class="header">Schedule Appointment</h1>
    <div class="main-content">
        <h2>Appointment for <?php echo htmlspecialchars($pet['Name']); ?></h2>

        <form method="POST">
            <h3>Services:</h3>
            <label><input type="checkbox" id="grooming" name="Grooming" <?php if ($g) echo 'checked'; ?>> Grooming</label><br>
            <label><input type="checkbox" id="boarding" name="Boarding" <?php if ($b) echo 'checked'; ?>> Boarding</label><br>
            <label><input type="checkbox" id="training" name="Training" <?php if ($t) echo 'checked'; ?>> Training</label><br><br>

            <label>Start date: <input type="date" id="start-date" name="StartDate" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($start); ?>" required></label><br><br>
            <label>End date: <input type="date" id="end-date" name="EndDate" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($end); ?>" required></label><br><br>

            <?php echo priceBox(); ?>

            <div class="form-submit-button">
                <button type="submit" name="action" value="bookappt">Book Appointment</button>
            </div>

            <?php if ($error) { ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php } ?>
            <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>
        </form>

        <div class="button-container">
            <form method="GET" action="../userAccount/ownerHome.php" style="display:inline;">
                <button type="submit" name="action" value="home" class="login-menu-button">Return to Home</button>
            </form>
        </div>
    </div>
</body>
</html>