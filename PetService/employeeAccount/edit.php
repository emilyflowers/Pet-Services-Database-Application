<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }
$isAdmin = ($_SESSION['IsAdmin'] ?? 0) == 1;

require __DIR__ . '/../database/booking.php';

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
$id = (int)($_GET['AId'] ?? 0);
$error = '';

$data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM appointment WHERE AId = $id"));
if (!$data) { die("Appointment not found."); }

if (isset($_POST['update'])) {
    $g = isset($_POST['Grooming']) ? 1 : 0;
    $b = isset($_POST['Boarding']) ? 1 : 0;
    $t = isset($_POST['Training']) ? 1 : 0;
    $start = $_POST['StartDate'];
    $end = $_POST['EndDate'];

    // Only re-check capacity if the dates actually changed (the appointment ignores itself)
    $datesChanged = $start !== substr($data['StartDate'], 0, 10) || $end !== substr($data['EndDate'], 0, 10);

    if ($end < $start) {
        $error = "The end date can't be before the start date.";
    } elseif ($datesChanged && ($fullDay = findFullDay($con, $start, $end, $id))) {
        $error = "Fully booked on " . date('F j, Y', strtotime($fullDay)) . " (" . MAX_PER_DAY . " appointments max per day).";
    } else {
        if ($isAdmin) {
            $e = (int)$_POST['EId'];
            $stmt = mysqli_prepare($con, "UPDATE appointment SET EId=?, Grooming=?, Boarding=?, Training=?, StartDate=?, EndDate=? WHERE AId=?");
            mysqli_stmt_bind_param($stmt, "iiiissi", $e, $g, $b, $t, $start, $end, $id);
        } else {
            $stmt = mysqli_prepare($con, "UPDATE appointment SET Grooming=?, Boarding=?, Training=?, StartDate=?, EndDate=? WHERE AId=?");
            mysqli_stmt_bind_param($stmt, "iiissi", $g, $b, $t, $start, $end, $id);
        }
        mysqli_stmt_execute($stmt);
        header("Location: empHome.php?action=appointments");
        exit;
    }

    // Validation failed: keep what the employee typed in the form
    $data['Grooming'] = $g; $data['Boarding'] = $b; $data['Training'] = $t;
    $data['StartDate'] = $start; $data['EndDate'] = $end;
    if ($isAdmin) { $data['EId'] = (int)$_POST['EId']; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Edit Appointment</title></head>
<body>
<h3>Update Appointment #<?php echo $id; ?></h3>
<?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
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
    <?php echo priceBox(); ?>
    <input type="submit" name="update" value="Update">
    <a href="empHome.php?action=appointments">Cancel</a>
</form>
</body>
</html>
