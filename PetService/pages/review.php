<?php
session_start();
if (empty($_SESSION['OId'])) {
    header("Location: ../userAccount/login.php");
    exit;
}

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
$oid = (int)$_SESSION['OId'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aid = (int)$_POST['AId'];
    $rating = (int)$_POST['Rating'];
    $body = trim($_POST['Body']);

    // Appointment must belong to this owner, be finished, and not already reviewed
    $check = mysqli_query($con, "SELECT a.AId FROM appointment a
                                 JOIN pet p ON a.PId = p.PId
                                 LEFT JOIN review r ON r.AId = a.AId
                                 WHERE a.AId = $aid AND p.OId = $oid
                                   AND a.EndDate <= NOW() AND r.AId IS NULL");
    if ($rating < 1 || $rating > 5) {
        $error = "Please choose a rating from 1 to 5.";
    } elseif (mysqli_num_rows($check) === 0) {
        $error = "That appointment can't be reviewed.";
    } else {
        $stmt = mysqli_prepare($con, "INSERT INTO review (AId, Rating, Body) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iis", $aid, $rating, $body);
        mysqli_stmt_execute($stmt);
        $success = "Thank you for your review!";
    }
}

$appts = mysqli_query($con, "SELECT a.AId, a.StartDate, a.EndDate, p.Name AS PetName
                             FROM appointment a
                             JOIN pet p ON a.PId = p.PId
                             LEFT JOIN review r ON r.AId = a.AId
                             WHERE p.OId = $oid AND a.EndDate <= NOW() AND r.AId IS NULL
                             ORDER BY a.EndDate DESC");
$count = mysqli_num_rows($appts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave a Review</title>
</head>
<body>
    <h2>Leave a Review</h2>
    <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
    <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>

    <?php if ($count === 0) { ?>
        <p>You have no completed appointments waiting for a review.</p>
    <?php } else { ?>
        <form method="POST">
            <p>Appointment:
                <select name="AId" required>
                    <?php while ($a = mysqli_fetch_assoc($appts)) { ?>
                        <option value="<?php echo $a['AId']; ?>">
                            <?php echo htmlspecialchars($a['PetName']) . " - " . substr($a['StartDate'], 0, 10) . " to " . substr($a['EndDate'], 0, 10); ?>
                        </option>
                    <?php } ?>
                </select>
            </p>
            <p>Rating:
                <select name="Rating" required>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Okay</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Terrible</option>
                </select>
            </p>
            <p><textarea name="Body" rows="5" cols="50" placeholder="Tell us about your experience (optional)"></textarea></p>
            <input type="submit" value="Submit Review">
        </form>
    <?php } ?>
    <p><a href="../userAccount/ownerHome.php">Back to home</a></p>
</body>
</html>
