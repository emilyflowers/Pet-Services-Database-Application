<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }
$isAdmin = ($_SESSION['IsAdmin'] ?? 0) == 1;

require_once __DIR__ . '/../database/booking.php';

$con = mysqli_connect("localhost", "root", "", "petservices_db");
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
    $e = $isAdmin ? (int)$_POST['EId'] : (int)$data['EId'];

    // Preserve submitted edits in the form if validation fails
    $data['EId'] = $e;
    $data['Grooming'] = $g;
    $data['Boarding'] = $b;
    $data['Training'] = $t;
    $data['StartDate'] = $start;
    $data['EndDate'] = $end;

    if (!$g && !$b && !$t) {
        $error = "Please choose at least one service.";
    } elseif ($end < $start) {
        $error = "The end date can't be before the start date.";
    } elseif (!($vet = vetStatus($con, $data['PId']))['ok']) {
        $issues = [];
        if (!$vet['vaccinated']) {
            $issues[] = "be up-to-date on vaccinations";
        }
        if (!$vet['vet_ok']) {
            $last = $vet['last'] ? "last visit: {$vet['last']}" : "no vet visit on file";
            $issues[] = "have a vet visit within the past year ({$last})";
        }
        $error = "This pet must " . implode(" and ", $issues) . " before booking.";
    } elseif (($fullDay = findFullDay($con, $start, $end, $id)) !== null) {
        $error = "Fully booked on {$fullDay} (maximum " . MAX_PER_DAY . " appointments per day).";
    } else {
        if ($isAdmin) {
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="../pages/style.css">
</head>
<body>
    <h1 class="header">Pet Services</h1>
    <div class="main-content">
        <h3>Update Appointment #<?php echo $id; ?></h3>

        <?php if ($error) { ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php } ?>

        <form method="POST">
            <?php if ($isAdmin) { ?>
                <p><label>Employee Id: <input type="number" name="EId" value="<?php echo (int)$data['EId']; ?>" required></label></p>
            <?php } else { ?>
                <p>Assigned employee: #<?php echo (int)$data['EId']; ?> (only an admin can change this)</p>
            <?php } ?>

            <p>
                <label><input type="checkbox" name="Grooming" <?php if ($data['Grooming']) echo 'checked'; ?>> Grooming</label><br>
                <label><input type="checkbox" name="Boarding" <?php if ($data['Boarding']) echo 'checked'; ?>> Boarding</label><br>
                <label><input type="checkbox" name="Training" <?php if ($data['Training']) echo 'checked'; ?>> Training</label><br>
            </p>
            <p>
                <label>Start: <input type="date" name="StartDate" value="<?php echo htmlspecialchars(substr($data['StartDate'], 0, 10)); ?>" required></label><br>
                <label>End: <input type="date" name="EndDate" value="<?php echo htmlspecialchars(substr($data['EndDate'], 0, 10)); ?>" required></label>
            </p>

            <?php echo priceBox(); ?>

            <div class="form-submit-button">
                <button type="submit" name="update" value="Update" class="">Update Appointment</button>
            </div>
            <div class="return-option">
                <a href="empHome.php?action=appointments">Cancel Changes</a>
            </div>
        </form>
    </div>
</body>
</html>