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
    } else {
        // Assign a random employee
        $emp = mysqli_fetch_assoc(mysqli_query($con, "SELECT EId FROM employee ORDER BY RAND() LIMIT 1"));
        $eid = (int)$emp['EId'];

        $stmt = mysqli_prepare($con, "INSERT INTO appointment (PId, EId, Grooming, Boarding, Training, StartDate, EndDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiiiiss", $pid, $eid, $g, $b, $t, $start, $end);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Appointment booked for " . htmlspecialchars($pet['Name']) . "!";
        } else {
            $error = "Could not book the appointment. Please try again.";
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
    <h1 class="header">Schedule Appointment</h1>  
    <title>Schedule Appointment</title>
</head>
<body>
    <div class="main-content">
    <h2>Appointment for <?php echo htmlspecialchars($pet['Name']); ?></h2>

    

    <form method="POST">
        <h3>Services:</h3>
        <label><input type="checkbox" id="grooming" name="Grooming"> Grooming</label><br>
        <label><input type="checkbox" id="boarding" name="Boarding"> Boarding</label><br>
        <label><input type="checkbox" id="training" name="Training"> Training</label><br><br>

        <label>Start date: <input type="date" id="start-date" name="StartDate" min="<?php echo date('Y-m-d'); ?>" required></label><br><br>
        <label>End date: <input type="date" id="end-date" name="EndDate" min="<?php echo date('Y-m-d'); ?>" required></label><br>
       
        <div class="button-container">
           <button type="submit" name="action" value="bookappt" class="user-action-button">Book Appointment</button>   
        </div>
        
        <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
        <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>
    </form>
    <label id="cost-per-day">Cost Per Day: $0</label>
    <label id="total-cost">Input start and end dates for total price</label>
    <div class="button-container">
        <form method="GET" action="../userAccount/ownerHome.php" style="display:inline;">
                <button type="submit" name="action" value="home" class="login-menu-button">Return to Home</button>
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
