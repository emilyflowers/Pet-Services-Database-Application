<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
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
    } else {
        try {
            $stmt = mysqli_prepare($con, "INSERT INTO appointment (PId, EId, Grooming, Boarding, Training, StartDate, EndDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiiiiss", $pid, $eid, $g, $b, $t, $start, $end);
            mysqli_stmt_execute($stmt);
            $success = "Appointment scheduled.";
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Schedule New Appointment</h2>
    <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
    <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>

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
        <p>Services:
            <label><input type="checkbox" name="Grooming"> Grooming</label>
            <label><input type="checkbox" name="Boarding"> Boarding</label>
            <label><input type="checkbox" name="Training"> Training</label>
        </p>
        <p>
            <label>Start: <input type="date" name="StartDate" required></label>
            <label>End: <input type="date" name="EndDate" required></label>
        </p>
        <input type="submit" value="Schedule">
    </form>
    <p><a href="empHome.php?action=appointments">View appointments</a> | <a href="empHome.php">Back to home</a></p>
</body>
</html>
