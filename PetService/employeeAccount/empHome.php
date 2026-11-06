<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }
$isAdmin = ($_SESSION['IsAdmin'] ?? 0) == 1;
$action = $_GET['action'] ?? 'appointments';
if ($action === 'employees' && !$isAdmin) { $action = 'appointments'; }  // admin only
$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");

function e($v) { return htmlspecialchars((string)$v); }
function yn($v) { return $v ? 'Yes' : 'No'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="header"><?php echo $isAdmin ? "Admin - Home" : "Employee - Home"; ?></h1>

    <div class="emp-menu">
        <a href="empHome.php?action=appointments"><button class="emp-menu-button">Appointments</button></a>
        <a href="empHome.php?action=pets"><button class="emp-menu-button">Pets</button></a>
        <a href="empHome.php?action=owners"><button class="emp-menu-button">Pet Parents</button></a>
        <a href="empHome.php?action=reviews"><button class="emp-menu-button">Reviews</button></a>
        <?php if ($isAdmin) { ?>
            <a href="empHome.php?action=employees"><button class="emp-menu-button">Employees</button></a>
        <?php } ?>
    </div>
    <div class="emp-menu">
        <a href="adduser.php"><button class="emp-menu-button">Add New Pet Parent</button></a>
        <a href="addpet.php"><button class="emp-menu-button">Add New Pet</button></a>
        <a href="newappointment.php"><button class="emp-menu-button">Schedule New Appointment</button></a>
        <?php if ($isAdmin) { ?>
            <a href="addEmployee.php"><button class="emp-menu-button">Add New Employee</button></a>
        <?php } ?>
        <a href="logout.php"><button class="emp-menu-button">Log out</button></a>
    </div>

<?php if ($action === 'appointments') {
    $result = mysqli_query($con, "SELECT * FROM appointment ORDER BY StartDate DESC"); ?>
    <h2>Appointments</h2>
    <table border="1">
        <tr><th>AId</th><th>PId</th><th>EId</th><th>Grooming</th><th>Boarding</th><th>Training</th><th>Start</th><th>End</th><th>Actions</th></tr>
        <?php while ($d = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $d['AId']; ?></td><td><?php echo $d['PId']; ?></td><td><?php echo $d['EId']; ?></td>
            <td><?php echo yn($d['Grooming']); ?></td><td><?php echo yn($d['Boarding']); ?></td><td><?php echo yn($d['Training']); ?></td>
            <td><?php echo e($d['StartDate']); ?></td><td><?php echo e($d['EndDate']); ?></td>
            <td>
                <a href="edit.php?AId=<?php echo $d['AId']; ?>">Edit</a>
                <?php if ($isAdmin) { ?> | <a href="delete.php?type=appointment&id=<?php echo $d['AId']; ?>" onclick="return confirm('Delete this appointment and its review?')">Delete</a><?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if ($action === 'pets') {
    $result = mysqli_query($con, "SELECT p.*, o.Name AS OwnerName FROM pet p JOIN owner o ON p.OId = o.OId ORDER BY p.Name"); ?>
    <h2>Pets</h2>
    <table border="1">
        <tr><th>PId</th><th>Name</th><th>Species</th><th>Owner</th><th>Birthdate</th><th>Fixed</th><th>Vaccinated</th><th>Last Vet</th><th>Last Visit</th><th>Actions</th></tr>
        <?php while ($d = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $d['PId']; ?></td><td><?php echo e($d['Name']); ?></td><td><?php echo e($d['Species']); ?></td>
            <td><?php echo e($d['OwnerName']); ?></td><td><?php echo e($d['Birthdate']); ?></td>
            <td><?php echo yn($d['IsFixed']); ?></td><td><?php echo yn($d['IsVaccinated']); ?></td>
            <td><?php echo e($d['LastVetApp']); ?></td><td><?php echo e($d['LastVisit']); ?></td>
            <td>
                <a href="editpet.php?PId=<?php echo $d['PId']; ?>">Edit</a>
                <?php if ($isAdmin) { ?> | <a href="delete.php?type=pet&id=<?php echo $d['PId']; ?>" onclick="return confirm('Delete this pet and all its appointments and reviews?')">Delete</a><?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if ($action === 'owners') {
    $result = mysqli_query($con, "SELECT OId, Name, Email, PhoneNumber, Address FROM owner ORDER BY Name"); ?>
    <h2>Pet Parents</h2>
    <table border="1">
        <tr><th>OId</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Actions</th></tr>
        <?php while ($d = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $d['OId']; ?></td><td><?php echo e($d['Name']); ?></td><td><?php echo e($d['Email']); ?></td>
            <td><?php echo e($d['PhoneNumber']); ?></td><td><?php echo e($d['Address']); ?></td>
            <td>
                <a href="editowner.php?OId=<?php echo $d['OId']; ?>">Edit</a>
                <?php if ($isAdmin) { ?> | <a href="delete.php?type=owner&id=<?php echo $d['OId']; ?>" onclick="return confirm('Delete this pet parent and all their pets, appointments and reviews?')">Delete</a><?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if ($action === 'reviews') {
    $avg = mysqli_fetch_assoc(mysqli_query($con, "SELECT AVG(Rating) AS AvgRating FROM review WHERE Date >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH)"));
    $averageRating = $avg['AvgRating'] ? number_format((float)$avg['AvgRating'], 1) : 'N/A';
    $reviews = mysqli_query($con, "SELECT AId, Rating, Date, Body FROM review WHERE Date >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH) ORDER BY Date DESC"); ?>
    <h2>Review Dashboard</h2>
    <h3>Average Rating Over Past 3 Months: <strong><?php echo $averageRating; ?> / 5</strong></h3>
    <?php if (mysqli_num_rows($reviews) > 0) { ?>
        <h3>Reviews From Past 3 Months:</h3>
        <ul>
        <?php while ($r = mysqli_fetch_assoc($reviews)) { ?>
            <li><strong>Rating: <?php echo $r['Rating']; ?>/5</strong> - <?php echo date("F j, Y", strtotime($r['Date'])); ?>
                (appointment #<?php echo $r['AId']; ?>)
                <?php if ($isAdmin) { ?> <a href="delete.php?type=review&id=<?php echo $r['AId']; ?>" onclick="return confirm('Delete this review?')">Delete</a><?php } ?>
                <br><?php echo e($r['Body']); ?></li>
        <?php } ?>
        </ul>
    <?php } else { ?>
        <p>No reviews found from the past 3 months.</p>
    <?php } ?>
<?php } ?>

<?php if ($action === 'employees' && $isAdmin) {
    $result = mysqli_query($con, "SELECT EId, Name, IsAdmin FROM employee ORDER BY EId"); ?>
    <h2>Employees</h2>
    <table border="1">
        <tr><th>EId</th><th>Name</th><th>Admin</th><th>Actions</th></tr>
        <?php while ($d = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $d['EId']; ?></td><td><?php echo e($d['Name']); ?></td><td><?php echo yn($d['IsAdmin']); ?></td>
            <td>
                <a href="editemployee.php?EId=<?php echo $d['EId']; ?>">Edit</a>
                <?php if ($d['EId'] != $_SESSION['Eid']) { ?>
                    | <a href="delete.php?type=employee&id=<?php echo $d['EId']; ?>" onclick="return confirm('Delete this employee?')">Delete</a>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
<?php } ?>
</body>
</html>
