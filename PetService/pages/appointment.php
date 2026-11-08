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
        <label><input type="checkbox" name="Grooming"> Grooming</label><br>
        <label><input type="checkbox" name="Boarding"> Boarding</label><br>
        <label><input type="checkbox" name="Training"> Training</label><br><br>

        <label>Start date: <input type="date" name="StartDate" min="<?php echo date('Y-m-d'); ?>" required></label><br><br>
        <label>End date: <input type="date" name="EndDate" min="<?php echo date('Y-m-d'); ?>" required></label><br>
       
        <div class="button-container">
           <button type="submit" name="action" value="bookappt" class="user-action-button">Book Appointment</button>   
        </div>
        
        <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
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