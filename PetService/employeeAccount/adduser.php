<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['Name']);
    $email = trim($_POST['Email']);
    $password = $_POST['Password'];
    $phone = trim($_POST['PhoneNumber']);
    $address = trim($_POST['Address']);

    try {
        $stmt = mysqli_prepare($con, "INSERT INTO owner (Name, Email, Password, PhoneNumber, Address) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $password, $phone, $address);
        mysqli_stmt_execute($stmt);
        $success = "Pet parent added. You can now add their pet.";
    } catch (mysqli_sql_exception $e) {
        $error = "Could not add the pet parent. That email may already be registered.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Pet Parent</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Add New Pet Parent</h2>
    <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
    <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>

    <form method="POST">
        <p><input type="text" name="Name" placeholder="Full name" maxlength="50" required></p>
        <p><input type="email" name="Email" placeholder="Email" required></p>
        <p><input type="text" name="Password" placeholder="Temporary password" required></p>
        <p><input type="tel" name="PhoneNumber" placeholder="Phone number" maxlength="15" required></p>
        <p><input type="text" name="Address" placeholder="Address" required></p>
        <input type="submit" value="Add Pet Parent">
    </form>
    <p><a href="addpet.php">Add a pet</a> | <a href="empHome.php">Back to home</a></p>
</body>
</html>
