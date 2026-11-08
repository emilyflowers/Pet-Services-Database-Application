<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }
$isAdmin = ($_SESSION['IsAdmin'] ?? 0) == 1;

$con = mysqli_connect("localhost", "root", "", "petservices_db");
$id = (int)($_GET['OId'] ?? 0);
$error = '';

if (isset($_POST['update'])) {
    try {
        if ($isAdmin && $_POST['Password'] !== '') {
            $stmt = mysqli_prepare($con, "UPDATE owner SET Name=?, Email=?, PhoneNumber=?, Address=?, Password=? WHERE OId=?");
            mysqli_stmt_bind_param($stmt, "sssssi", $_POST['Name'], $_POST['Email'], $_POST['PhoneNumber'], $_POST['Address'], $_POST['Password'], $id);
        } else {
            $stmt = mysqli_prepare($con, "UPDATE owner SET Name=?, Email=?, PhoneNumber=?, Address=? WHERE OId=?");
            mysqli_stmt_bind_param($stmt, "ssssi", $_POST['Name'], $_POST['Email'], $_POST['PhoneNumber'], $_POST['Address'], $id);
        }
        mysqli_stmt_execute($stmt);
        header("Location: empHome.php?action=owners");
        exit;
    } catch (mysqli_sql_exception $e) {
        $error = "Could not update. That email may already belong to another pet parent.";
    }
}

$data = mysqli_fetch_assoc(mysqli_query($con, "SELECT OId, Name, Email, PhoneNumber, Address FROM owner WHERE OId = $id"));
if (!$data) { die("Pet parent not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Edit Pet Parent</title></head>
<body>
<h3>Update Pet Parent #<?php echo $id; ?></h3>
<?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
<form method="POST">
    <p>Name: <input type="text" name="Name" value="<?php echo htmlspecialchars($data['Name']); ?>" maxlength="50" required></p>
    <p>Email: <input type="email" name="Email" value="<?php echo htmlspecialchars($data['Email']); ?>" required></p>
    <p>Phone: <input type="tel" name="PhoneNumber" value="<?php echo htmlspecialchars($data['PhoneNumber']); ?>" maxlength="15" required></p>
    <p>Address: <input type="text" name="Address" value="<?php echo htmlspecialchars($data['Address']); ?>" required></p>
    <?php if ($isAdmin) { ?>
        <p>New password: <input type="text" name="Password" placeholder="Leave blank to keep current"></p>
    <?php } ?>
    <input type="submit" name="update" value="Update">
    <a href="empHome.php?action=owners">Cancel</a>
</form>
</body>
</html>