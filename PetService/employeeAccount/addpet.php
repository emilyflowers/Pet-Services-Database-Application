<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oid = (int)$_POST['OId'];
    $name = trim($_POST['Name']);
    $birth = $_POST['Birthdate'];
    $species = $_POST['Species'];
    $fixed = (int)$_POST['IsFixed'];
    $vacc = isset($_POST['IsVaccinated']) ? 1 : 0;
    $vet = $_POST['LastVetApp'] !== '' ? $_POST['LastVetApp'] : null;

    try {
        $stmt = mysqli_prepare($con, "INSERT INTO pet (OId, IsVaccinated, Name, Birthdate, Species, IsFixed, LastVetApp, LastVisit) VALUES (?, ?, ?, ?, ?, ?, ?, NULL)");
        mysqli_stmt_bind_param($stmt, "iisssis", $oid, $vacc, $name, $birth, $species, $fixed, $vet);
        mysqli_stmt_execute($stmt);
        $success = "Pet added.";
    } catch (mysqli_sql_exception $e) {
        $error = "Could not add the pet. Please check the details.";
    }
}

$owners = mysqli_query($con, "SELECT OId, Name, Email FROM owner ORDER BY Name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Pet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Add New Pet</h2>
    <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
    <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>

    <form method="POST">
        <p>Owner:
            <select name="OId" required>
                <option value="">-- Select owner --</option>
                <?php while ($o = mysqli_fetch_assoc($owners)) { ?>
                    <option value="<?php echo $o['OId']; ?>"><?php echo htmlspecialchars($o['Name'] . " (" . $o['Email'] . ")"); ?></option>
                <?php } ?>
            </select>
        </p>
        <p><input type="text" name="Name" placeholder="Pet name" maxlength="50" required></p>
        <p>Birthdate: <input type="date" name="Birthdate" required></p>
        <p>Species:
            <select name="Species" required>
                <option value="">-- Select --</option>
                <option value="dog">Dog</option>
                <option value="cat">Cat</option>
                <option value="bird">Bird</option>
                <option value="rabbit">Rabbit</option>
                <option value="reptile">Reptile</option>
                <option value="other">Other</option>
            </select>
        </p>
        <p>Spayed/neutered?
            <label><input type="radio" name="IsFixed" value="1" required> Yes</label>
            <label><input type="radio" name="IsFixed" value="0"> No</label>
        </p>
        <p>Last vet appointment (optional): <input type="date" name="LastVetApp"></p>
        <p><label><input type="checkbox" name="IsVaccinated"> Vaccinated in the last year</label></p>
        <input type="submit" value="Add Pet">
    </form>
    <p><a href="newappointment.php">Schedule an appointment</a> | <a href="empHome.php">Back to home</a></p>
</body>
</html>
