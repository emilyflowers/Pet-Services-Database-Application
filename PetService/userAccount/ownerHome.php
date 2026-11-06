<?php
session_start();
if (empty($_SESSION['OId'])) {
    header("Location: login.php");
    exit;
}

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
$oid = (int)$_SESSION['OId'];

$owner = mysqli_fetch_assoc(mysqli_query($con, "SELECT Name FROM owner WHERE OId = $oid"));
$result = mysqli_query($con, "SELECT PId, Name, Species FROM pet WHERE OId = $oid");
$pets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pets[] = $row;
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
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($owner['Name'] ?? ''); ?></h1>
    <p><a href="logout.php">Log out</a></p>

    <h2>Our Services</h2>
    <ul>
        <li><strong>Grooming:</strong> bathing and nail trimming.</li>
        <li><strong>Boarding:</strong> overnight care while you're away.</li>
        <li><strong>Training:</strong> obedience and behavior training.</li>
    </ul>

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
            <input type="submit" value="Make Appointment">
        </form>
    <?php } ?>
    <p><a href="addPet.php">+ Add a new pet</a> | <a href="../pages/review.php">Leave a review</a></p>
</body>
</html>
