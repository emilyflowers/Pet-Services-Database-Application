<?php
session_start();
if (empty($_SESSION['OId'])) {
    header("Location: login.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "petservices_db");
$oid = (int)$_SESSION['OId'];

$owner = mysqli_fetch_assoc(mysqli_query($con, "SELECT Name FROM owner WHERE OId = $oid"));
$result = mysqli_query($con, "SELECT PId, Name, Species, Birthdate, IsFixed, IsVaccinated, LastVetApp, LastVisit FROM pet WHERE OId = $oid");
$pets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pets[] = $row;
}

// Fetch past and future appointments joined with pet and employee details
$apptQuery = "
    SELECT 
        a.AId,
        DATE(a.StartDate) as StartDate,
        DATE(a.EndDate) as EndDate,
        a.Grooming,
        a.Boarding,
        a.Training,
        p.Name AS PetName,
        e.Name AS EmployeeName
    FROM appointment a
    JOIN pet p ON a.PId = p.PId
    JOIN employee e ON a.EId = e.EId
    WHERE p.OId = $oid
    ORDER BY a.StartDate DESC
";
$apptResult = mysqli_query($con, $apptQuery);

$upcomingAppointments = [];
$pastAppointments = [];
$now = date('Y-m-d');

while ($row = mysqli_fetch_assoc($apptResult)) {
    if ($row['EndDate'] < $now) {
        $pastAppointments[] = $row;
    } else {
        $upcomingAppointments[] = $row;
    }
}

// Owner picked a pet -> remember it and go book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = (int)($_POST['pet_id'] ?? 0);
    foreach ($pets as $p) {
        if ($p['PId'] == $pid) {   // only allow their own pets
            $_SESSION['PId'] = $pid;
            header("Location: ../pages/appointment.php");
            exit;
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
    <title>Home - Pet Parent</title>
    <h1 class="header">Home</h1>  
</head>
<body>
    <div class='main-content'>
    <div>
        <h1>Welcome, <?php echo htmlspecialchars($owner['Name'] ?? ''); ?></h1>
    </div>

    <div class='body-section'>
    <h2>Our Services</h2>
    <ul>
        <li><strong>Grooming:</strong> Bath and Nail Trim</li>
        <li><strong>Boarding:</strong> Overnight Care While You're Away</li>
        <li><strong>Training:</strong> Obedience and Behavior Training</li>
    </ul>
    

    <div class='pets'>
    <h2>Your Pets</h2>
    <?php if (empty($pets)) { ?>
        <p>No pets registered yet.</p>
    <?php } else { ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Species</th>
                    <th>Birthdate</th>
                    <th>Fixed</th>
                    <th>Vaccinations Up To Date</th>
                    <th>Last Vet Appt</th>
                    <th>Last Visit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pets as $p) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['Name']); ?></td>
                        <td><?php echo htmlspecialchars($p['Species'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($p['Birthdate']); ?></td>
                        <td><?php echo $p['IsFixed'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $p['IsVaccinated'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $p['LastVetApp'] ? htmlspecialchars($p['LastVetApp']) : 'None'; ?></td>
                        <td><?php echo $p['LastVisit'] ? htmlspecialchars($p['LastVisit']) : 'None'; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    
        <form method="GET" action="addPet.php" style="display:inline;">
         <button type="submit" name="action" value="addpet" class="user-action-button">+ Add a New Pet</button>
        </form>
    </div>

    <div class='book-appointment'>
    <h2>Book an Appointment</h2>
    <?php if (count($pets) === 0) { ?>
        <p>You have no pets registered yet.</p>
    <?php } else { ?>
        <form method="POST">
            <label>Which pet is this for?
                <select name="pet_id" required>
                    <?php foreach ($pets as $p) { ?>
                        <option value="<?php echo (int)$p['PId']; ?>">
                            <?php echo htmlspecialchars($p['Name'] . " (" . $p['Species'] . ")"); ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
            <button type="submit" name="action" value="addpet" class="user-action-button">Schedule Appointment</button>
        </form>
    <?php } ?>
    </div>

    <div class='appointments'>
    <h2>Upcoming Appointments</h2>
    <?php if (empty($upcomingAppointments)) { ?>
        <p>No upcoming appointments scheduled.</p>
    <?php } else { ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Assigned Staff</th>
                    <th>Services</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcomingAppointments as $a) { 
                    $services = [];
                    if ($a['Grooming']) $services[] = 'Grooming';
                    if ($a['Boarding']) $services[] = 'Boarding';
                    if ($a['Training']) $services[] = 'Training';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['PetName']); ?></td>
                        <td><?php echo htmlspecialchars($a['EmployeeName']); ?></td>
                        <td><?php echo htmlspecialchars(implode(', ', $services) ?: 'General'); ?></td>
                        <td><?php echo htmlspecialchars($a['StartDate']); ?></td>
                        <td><?php echo htmlspecialchars($a['EndDate']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <h2>Past Appointments</h2>
    <?php if (empty($pastAppointments)) { ?>
        <p>No past appointments found.</p>
    <?php } else { ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Assigned Staff</th>
                    <th>Services</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pastAppointments as $a) { 
                    $services = [];
                    if ($a['Grooming']) $services[] = 'Grooming';
                    if ($a['Boarding']) $services[] = 'Boarding';
                    if ($a['Training']) $services[] = 'Training';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['PetName']); ?></td>
                        <td><?php echo htmlspecialchars($a['EmployeeName']); ?></td>
                        <td><?php echo htmlspecialchars(implode(', ', $services) ?: 'General'); ?></td>
                        <td><?php echo htmlspecialchars($a['StartDate']); ?></td>
                        <td><?php echo htmlspecialchars($a['EndDate']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    
    <form method="GET" action="../pages/review.php" style="display:inline;">
         <button type="submit" name="action" value="addreview" class="user-action-button">Leave a Review</button>
        </form>
    </div>
    </div>

    <div class='button-container'>
     <form method="GET" action="logout.php" style="display:inline;">
      <button type="submit" name="action" value="logout" class="emp-menu-button">Logout</button>
     </form>
    </div>
 </div>
</body>
</html>