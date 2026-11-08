<?php
session_start();

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $Username    = trim($_POST['Username']    ?? '');
    $Email       = trim($_POST['Email']       ?? '');
    $Password    = $_POST['Password']         ?? '';
    $PhoneNumber = trim($_POST['PhoneNumber'] ?? '');
    $Address     = trim($_POST['Address']     ?? '');

    if ($Username === '' || $Email === '' || $Password === '') {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = mysqli_prepare($con,
            "INSERT INTO owner (Name, Email, Password, PhoneNumber, Address)
             VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss",
            $Username, $Email, $Password, $PhoneNumber, $Address);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['OId'] = mysqli_insert_id($con);
            header("Location: ../userAccount/addPet.php");
            exit;                                        
        } else {
            error_log("Insert failed: " . mysqli_error($con));
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>
<body>
    <div class="container">
        <div class="title">Registration</div>
        <div class="content">
            <form action="" method="POST">
                <div class="user-details">
                    <div class="input-box">
                        <span class="details">Username</span>
                        <input type="text" placeholder="Enter your username" name="Username" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Email</span>
                        <input type="email" placeholder="Enter your email" name="Email" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Password</span>
                        <input type="password" placeholder="Enter your password" name="Password" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Phone Number</span>
                        <input type="tel" placeholder="Enter phone number" name="PhoneNumber" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Address</span>
                        <input type="text" placeholder="Enter Home Address" name="Address" required>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" value="Register">
                    <p class="login"><strong>Already have an account?</strong>
                        <a href="../userAccount/login.php">Login.</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>