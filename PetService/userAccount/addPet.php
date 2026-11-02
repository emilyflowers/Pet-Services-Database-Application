<?php
session_start();

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $IsVaccinated = $_POST['IsVaccinated'] ?? '';
    $Name = trim($_POST['Name']  ?? '');
    $Birthdate = $_POST['Birthdate'] ?? '';
    $Species = $_POST['Species'] ?? '';
    $IsFixed = $_POST['IsFixed'] ?? '';
    $LastVetApp = $_POST['LastVetApp'] ?? '';

    if ($Name === '' || $Birthdate === '' || $Species === '' || $IsFixed === '' || $LastVetApp === '') {
        $error = "Please fill out required fields.";
    } else {
        $stmt = mysqli_prepare($con, "INSERT INTO pet (IsVaccinated, Name, Birthdate, Species, IsFixed, LastVetApp, LastVisit)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isssiss", $IsVaccinated, $Name, $Birthdate, $Species, 
            $IsFixed, $LastVetApp);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['PId'] = mysqli_insert_id($con);
            header("Location: ../pages/appointment.php");
            exit;
        } else {
            error_log("Insert failed: " . mysqli_error($con));
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Pet</title>
    <meta charset="utf-8">
</head>
<body>
    <div class="container">
        <div class="title">Registration</div>
        <div class="content">
            <form action="" method="POST">
                <div class="pet-details">

                    <label for="IsVaccinated">Has your pet been vaccinated in the last year?</label>
                    <input type="checkbox" name="IsVaccinated" id="IsVaccinated" value="1"><br>

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