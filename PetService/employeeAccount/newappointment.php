<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }

require_once __DIR__ . '/../database/booking.php';

$con = mysqli_connect("localhost", "root", "", "petservices_db");
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = (int)$_POST['PId'];
    $eid = (int)$_POST['EId'];
    $g = isset($_POST['Grooming']) ? 1 : 0;
    $b = isset($_POST['Boarding']) ? 1 : 0;
    $t = isset($_POST['Training']) ? 1 : 0;
    $start = $_POST['StartDate'];
    $end = $_POST['EndDate'];

    if (!$g && !$b && !$t) {
        $error = "Please choose at least one service.";
    } elseif ($end < $start) {
        $error = "The end date can't be before the start date.";
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
        try {
            $stmt = mysqli_prepare($con, "INSERT INTO appointment (PId, EId, Grooming, Boarding, Training, StartDate, EndDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiiiiss", $pid, $eid, $g, $b, $t, $start, $end);
            mysqli_stmt_execute($stmt);
            $totals = calcTotal($g, $b, $t, $start, $end);
            $success = "Appointment scheduled. Total: $" . number_format($totals['total'], 2);
        } catch (mysqli_sql_exception $e) {
            $error = "Could not schedule the appointment. Please check the details.";
        }
    }
}

$pets = mysqli_query($con, "SELECT p.PId, p.Name AS PetName, o.Name AS OwnerName
                            FROM pet p JOIN owner o ON p.OId = o.OId
                            ORDER BY o.Name, p.Name");
$employees = mysqli_query($con, "SELECT EId, Name FROM employee ORDER BY Name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Appointment</title>
    <link rel="stylesheet" href="../pages/style.css">
</head>
<body>
    <h1 class="header">Pet Services</h1>
    <div class="main-content">
        <h2>Schedule New Appointment</h2>

        <form method="POST">
            <p>Pet:
                <select name="PId" required>
                    <option value="">-- Select pet --</option>
                    <?php while ($p = mysqli_fetch_assoc($pets)) { ?>
                        <option value="<?php echo $p['PId']; ?>"><?php echo htmlspecialchars($p['PetName'] . " (owner: " . $p['OwnerName'] . ")"); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p>Employee:
                <select name="EId" required>
                    <?php while ($e = mysqli_fetch_assoc($employees)) { ?>
                        <option value="<?php echo $e['EId']; ?>" <?php if ($e['EId'] == $_SESSION['Eid']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($e['Name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </p>
            <p>Services:</p>
            <p><label><input type="checkbox" id="grooming" name="Grooming"> Grooming</label></p>
            <p><label><input type="checkbox" id="boarding" name="Boarding"> Boarding</label></p>
            <p><label><input type="checkbox" id="training" name="Training"> Training</label></p>

            <p><label>Start Date: <input type="date" id="start-date" name="StartDate" required></label></p>
            <p><label>End Date: <input type="date" id="end-date" name="EndDate" required></label></p>

            <?php echo priceBox(); ?>

            <div class="form-submit-button">
                <button type="submit" name="action" value="Schedule">Schedule Appointment</button>
            </div>
        </form>

        <?php if ($error) { ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php } ?>
        <?php if ($success) { ?><p style="color:green"><?php echo htmlspecialchars($success); ?></p><?php } ?>

        <div class="button-container">
            <form method="GET" action="empHome.php" style="display:inline;">
                <button type="submit" name="action" value="appointments" class="login-menu-button">View Appointments</button>
            </form>
            <form method="GET" action="empHome.php" style="display:inline;">
                <button type="submit" name="action" value="appointments" class="login-menu-button">Back to Home</button>
            </form>
        </div>
    </div>
</body>
</html>