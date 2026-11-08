<?php
session_start();
if (!isset($_SESSION['Eid'])) { header("Location: login.php"); exit; }

$con = mysqli_connect("localhost", "root", "", "petservices_db");
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
    <link rel="stylesheet" href="../pages/style.css">
    <h1 class="header">Pet Services</h1>  
</head>
<body>
<div class="main-content">
    <h2>Add New Pet Parent</h2>
  
    <form method="POST" class="add-owner-form">
        <div class="input-section">
            <span class="details">First and Last Name:</span>
            <input type="text" name="Name" placeholder="" maxlength="50" required>
        </div>

        <div class="input-section">
            <span class="details">Email:</span>
            <input type="email" name="Email" placeholder="" required>
        </div>

        <div class="input-section">
            <span class="details">Password:</span>
            <input type="text" name="Password" placeholder="" required>
        </div>

        <div class="input-section">
            <span class="details">Phone Number:</span>
            <input type="tel" name="PhoneNumber" placeholder="" maxlength="15" required>
        </div>

        <div class="input-section">    
            <span class="details">Address:</span>
            <input type="text" name="Address" placeholder="" required>
        </div>
        <div class="button-container">
            <button type="submit" name="action" value="Add Pet Parent" class="user-action-button">Add Pet Parent</button>
        </div>
    </form>
    
    <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
    <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>

        <div class="button-container">
            <form method="GET" action="addpet.php" style="display:inline;">
                <button type="submit" name="action" value="addpet" class="login-menu-button">Add a Pet</button>
            </form>

            <form method="GET" action="empHome.php" style="display:inline;">
                <button type="submit" name="action" value="appointments" class="login-menu-button">Back to Home</button>
            </form>
        </div>
    </div>
</body>
</html>