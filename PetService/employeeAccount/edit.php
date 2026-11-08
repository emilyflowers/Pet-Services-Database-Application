<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }
$isAdmin = ($_SESSION['IsAdmin'] ?? 0) == 1;

$con = mysqli_connect("localhost", "root", "", "petservices_db");
$id = (int)($_GET['AId'] ?? 0);

if (isset($_POST['update'])) {
    $g = isset($_POST['Grooming']) ? 1 : 0;
    $b = isset($_POST['Boarding']) ? 1 : 0;
    $t = isset($_POST['Training']) ? 1 : 0;

    if ($isAdmin) {
        $e = (int)$_POST['EId'];
        $stmt = mysqli_prepare($con, "UPDATE appointment SET EId=?, Grooming=?, Boarding=?, Training=?, StartDate=?, EndDate=? WHERE AId=?");
        mysqli_stmt_bind_param($stmt, "iiiissi", $e, $g, $b, $t, $_POST['StartDate'], $_POST['EndDate'], $id);
    } else {
        $stmt = mysqli_prepare($con, "UPDATE appointment SET Grooming=?, Boarding=?, Training=?, StartDate=?, EndDate=? WHERE AId=?");
        mysqli_stmt_bind_param($stmt, "iiissi", $g, $b, $t, $_POST['StartDate'], $_POST['EndDate'], $id);
    }
    mysqli_stmt_execute($stmt);
    header("Location: empHome.php?action=appointments");
    exit;
}

$data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM appointment WHERE AId = $id"));
if (!$data) { die("Appointment not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Edit Appointment</title></head>
<body>
<h3>Update Appointment #<?php echo $id; ?></h3>
<form method="POST">
    <?php if ($isAdmin) { ?>
        <p><label>Employee Id: <input type="number" name="EId" value="<?php echo $data['EId']; ?>" required></label></p>
    <?php } else { ?>
        <p>Assigned employee: #<?php echo $data['EId']; ?> (only an admin can change this)</p>
    <?php } ?>
    <p>
        <label><input type="checkbox" name="Grooming" <?php if ($data['Grooming']) echo 'checked'; ?>> Grooming</label>
        <label><input type="checkbox" name="Boarding" <?php if ($data['Boarding']) echo 'checked'; ?>> Boarding</label>
        <label><input type="checkbox" name="Training" <?php if ($data['Training']) echo 'checked'; ?>> Training</label>
    </p>
    <p>
        <label>Start: <input type="date" name="StartDate" value="<?php echo substr($data['StartDate'], 0, 10); ?>" required></label>
        <label>End: <input type="date" name="EndDate" value="<?php echo substr($data['EndDate'], 0, 10); ?>" required></label>
    </p>
    <input type="submit" name="update" value="Update">
    <a href="empHome.php?action=appointments">Cancel</a>
</form>
</body>
</html>