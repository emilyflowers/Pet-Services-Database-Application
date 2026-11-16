<?php
session_start();

if (empty($_SESSION['OId'])) {
    header('Location: login.php');
    exit;
}

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
$userOId = (int)$_SESSION['OId'];
$result  = mysqli_query($con, "SELECT PId, Name FROM pet WHERE OId = $userOId");

$pets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pets[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Submit'])) {
    if ($_POST['pet_id'] === "newPet") {
        header("Location: addPet.php");
        exit;
    }
    $_SESSION['PId'] = (int)$_POST['pet_id'];
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
    <?php endif; ?>

    <form method="post">
        <select name="pet_id">
            <?php foreach ($pets as $pet): ?>
                <option value="<?= (int)$pet['PId'] ?>"><?= htmlspecialchars($pet['Name']) ?></option>
            <?php endforeach; ?>
            <option value="newPet">Add new pet</option>
        </select>
        <input type="submit" name="Submit" value="Submit">
    </form>
</body>
</html>
