<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }

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
    <link rel="stylesheet" href="../pages/style.css">
    <h1 class="header">Pet Services</h1>  
</head>
<body>
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
        <p>Services:
            <p><label><input type="checkbox" id="grooming" name="Grooming"> Grooming</label></p>
            <p><label><input type="checkbox" id="boarding" name="Boarding"> Boarding</label></p>
            <p><label><input type="checkbox" id="training" name="Training"> Training</label></p>
        </p>
        <p>
            <p><label>Start Date: <input type="date" id="start-date" name="StartDate" required></label></p>
            <p><label>End Date: <input type="date" id="end-date" name="EndDate" required></label></p>
        </p>
        
        <div class="button-container">
            <button type="submit" name="action" value="Schedule" class="user-action-button">Schedule Appointment</button>
        </div>
    </form>
    <label id="cost-per-day">Cost Per Day: $0</label>
    <label id="total-cost">Input start and end dates for total price</label>

    <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
    <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>

        <div class="button-container">
            <form method="GET" action="empHome.php?action=appointments" style="display:inline;">
                <button type="submit" name="action" value="appointments" class="login-menu-button">View Appointments</button>
            </form>

            <form method="GET" action="empHome.php?action=appointments" style="display:inline;">
                <button type="submit" name="action" value="appointments" class="login-menu-button">Back to Home</button>
            </form>
        </div>
    </div>
</body>
<script>
const checkboxes = document.querySelectorAll('input[type="checkbox"]');

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateLabel);
});
const dates = document.querySelectorAll('input[type="date"]');

dates.forEach(date => {
    date.addEventListener('change', updateLabel);
});

function updateLabel() {
    let costPerDay = 0;

    if (document.getElementById("grooming").checked) costPerDay += 50;
    if (document.getElementById("boarding").checked) costPerDay += 65;
    if (document.getElementById("training").checked) costPerDay += 45;

    document.getElementById("cost-per-day").textContent = "Cost Per Day: $" + costPerDay;

    if (!document.getElementById("start-date").value ||
    !document.getElementById("end-date").value) {
    	document.getElementById("total-cost").textContent = "Input start and end dates for total price";
    } else {

    	const start = new Date(document.getElementById("start-date").value);
    	const end = new Date(document.getElementById("end-date").value);

    	if (end < start) {
    		document.getElementById("total-cost").textContent = "Invalid date range";
    	} else {

    		const difference = end - start;
    		const days = difference / (1000 * 60 * 60 * 24) + 1;

    		document.getElementById("total-cost").textContent = "Total Cost: $" + costPerDay * days;
	}
    }
}
</script>
</html>
