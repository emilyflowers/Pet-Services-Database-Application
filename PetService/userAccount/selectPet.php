<?php
session_start();

if (empty($_SESSION['OId'])) {
    header('Location: ../userAccount/login.php');
    exit;
}

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
if (!$con) die("DB error: " . mysqli_connect_error());
mysqli_set_charset($con, "utf8mb4");

$userOId = (int) $_SESSION['OId'];
$result  = mysqli_query($con, "SELECT * FROM pet WHERE OId = $userOId");

$pets = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pets[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Submit'])) {
    if ($_POST['pet_id'] === "newPet") {
        header("Location: ../userAccount/addPet.php");
        exit;
    }
    $_SESSION['PId'] = $_POST['pet_id'];
    header("Location: ../pages/appointment.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Pet</title>
    <meta charset="utf-8">
</head>
<body>
    <header>Select Pet</header>

    <?php if (count($pets) === 0): ?>
        <p>You have no pets registered yet.</p>
    <?php else: ?>
        <form id="L" method="post">
            <select name="pet_id">
                <?php foreach ($pets as $pet): ?>
                    <option value="<?= (int)$pet['pet_id'] ?>">
                        <?= htmlspecialchars($pet['Name']) ?>
                    </option>
                <?php endforeach; ?>
                <option value="newPet">Add new pet</option>
            </select>
            <input type="submit" name="Submit" value="Submit">
        </form>
    <?php endif; ?>
</body>
</html>