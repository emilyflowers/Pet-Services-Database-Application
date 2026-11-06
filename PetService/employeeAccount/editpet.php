<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
$id = (int)($_GET['PId'] ?? 0);
$error = '';

if (isset($_POST['update'])) {
    $oid = (int)$_POST['OId'];
    $fixed = (int)$_POST['IsFixed'];
    $vacc = isset($_POST['IsVaccinated']) ? 1 : 0;
    $vet = $_POST['LastVetApp'] !== '' ? $_POST['LastVetApp'] : null;
    $visit = $_POST['LastVisit'] !== '' ? $_POST['LastVisit'] : null;

    try {
        $stmt = mysqli_prepare($con, "UPDATE pet SET OId=?, Name=?, Birthdate=?, Species=?, IsFixed=?, IsVaccinated=?, LastVetApp=?, LastVisit=? WHERE PId=?");
        mysqli_stmt_bind_param($stmt, "isssiissi", $oid, $_POST['Name'], $_POST['Birthdate'], $_POST['Species'], $fixed, $vacc, $vet, $visit, $id);
        mysqli_stmt_execute($stmt);
        header("Location: empHome.php?action=pets");
        exit;
    } catch (mysqli_sql_exception $e) {
        $error = "Could not update the pet. Please check the details.";
    }
}

$data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM pet WHERE PId = $id"));
if (!$data) { die("Pet not found."); }
$owners = mysqli_query($con, "SELECT OId, Name, Email FROM owner ORDER BY Name");
$species = ['dog', 'cat', 'bird', 'rabbit', 'reptile', 'other'];
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Edit Pet</title></head>
<body>
<h3>Update Pet #<?php echo $id; ?></h3>
<?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
<form method="POST">
    <p>Owner:
        <select name="OId" required>
            <?php while ($o = mysqli_fetch_assoc($owners)) { ?>
                <option value="<?php echo $o['OId']; ?>" <?php if ($o['OId'] == $data['OId']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($o['Name'] . " (" . $o['Email'] . ")"); ?>
                </option>
            <?php } ?>
        </select>
    </p>
    <p>Name: <input type="text" name="Name" value="<?php echo htmlspecialchars($data['Name']); ?>" maxlength="50" required></p>
    <p>Birthdate: <input type="date" name="Birthdate" value="<?php echo $data['Birthdate']; ?>" required></p>
    <p>Species:
        <select name="Species" required>
            <?php foreach ($species as $s) { ?>
                <option value="<?php echo $s; ?>" <?php if ($s === $data['Species']) echo 'selected'; ?>><?php echo ucfirst($s); ?></option>
            <?php } ?>
        </select>
    </p>
    <p>Spayed/neutered?
        <label><input type="radio" name="IsFixed" value="1" <?php if ($data['IsFixed']) echo 'checked'; ?>> Yes</label>
        <label><input type="radio" name="IsFixed" value="0" <?php if (!$data['IsFixed']) echo 'checked'; ?>> No</label>
    </p>
    <p><label><input type="checkbox" name="IsVaccinated" <?php if ($data['IsVaccinated']) echo 'checked'; ?>> Vaccinated</label></p>
    <p>Last vet appointment: <input type="date" name="LastVetApp" value="<?php echo $data['LastVetApp']; ?>"></p>
    <p>Last visit: <input type="date" name="LastVisit" value="<?php echo $data['LastVisit']; ?>"></p>
    <input type="submit" name="update" value="Update">
    <a href="empHome.php?action=pets">Cancel</a>
</form>
</body>
</html>
