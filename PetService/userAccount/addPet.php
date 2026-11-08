<?php
session_start();

// Must be logged in
if (empty($_SESSION['OId'])) {
    header("Location: ../userAccount/login.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "petservices_db");
if (!$con) {
    die("DB error: " . mysqli_connect_error());
}
mysqli_set_charset($con, "utf8mb4");

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $Name         = trim($_POST['Name']        ?? '');
    $Birthdate    = $_POST['Birthdate']        ?? '';
    $Species      = trim($_POST['Species']     ?? '');
    $IsFixed      = $_POST['IsFixed']          ?? '0';
    $LastVetApp   = $_POST['LastVetApp']       ?? '';
    // Checkbox: unchecked = not sent at all → default 0
    $IsVaccinated = isset($_POST['IsVaccinated']) ? 1 : 0;

    if ($Name === '' || $Birthdate === '' || $Species === '' || $LastVetApp === '') {
        $error = "Please fill out all required fields.";
    } else {
        $stmt = mysqli_prepare($con,
            "INSERT INTO pet (OId, IsVaccinated, Name, Birthdate, Species, IsFixed, LastVetApp, LastVisit)
             VALUES (?, ?, ?, ?, ?, ?, ?, NULL)");
        mysqli_stmt_bind_param($stmt, "iisssis",
            $_SESSION['OId'], $IsVaccinated, $Name, $Birthdate, $Species, $IsFixed, $LastVetApp);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['PId'] = mysqli_insert_id($con);
            header("Location: ownerHome.php");
            exit;
        } else {
            error_log("Pet insert failed: " . mysqli_error($con));
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../pages/style.css"> 
    <h1 class="header">Add Pet</h1>  
</head>
<body>
    <div class='main-content'>
    
        <div>
            <h1>Register a Pet</h1>
        </div>

        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="content">
            <form action="" method="POST">
                <div class="pet-details">

                    <div class="input-box">
                        <span class="details">Pet's Name: </span>
                        <input type="text" placeholder="Name"
                               name="Name" id="Name" required>
                    </div>

                    <div class="input-box">
                        <span class="details">Birthdate: </span>
                        <input type="date" name="Birthdate" id="Birthdate" required>
                    </div>

                    <div class="input-box">
                        <span class="details">Species: </span>
                        <select name="Species" id="Species" required>
                            <option value="">-- Select --</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Bird">Bird</option>
                            <option value="Rabbit">Rabbit</option>
                            <option value="Reptile">Reptile</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="input-box">
                        <span class="details">Is your pet fixed (neutered/spayed)?</span>
                        <label>
                            <input type="radio" name="IsFixed" value="1" required> Yes
                        </label>
                        <label>
                            <input type="radio" name="IsFixed" value="0"> No
                        </label>
                    </div>

                    <div class="input-box">
                        <span class="details">Date of last vet appointment: </span>
                        <input type="date" name="LastVetApp" id="LastVetApp" required>
                    </div>

                    <div class="input-box">
                        <label for="IsVaccinated">
                            Has your pet been vaccinated in the last year?
                        </label>
                        <input type="checkbox" name="IsVaccinated" id="IsVaccinated" value="1">
                    </div>

                </div>

                <div class="button-container">
                    <button type="submit" name="action" value="registerpet" class="user-action-button">Add Pet</button>   
                </div>
            </form>

            <div class="button-container">
        <form method="GET" action="../userAccount/ownerHome.php" style="display:inline;">
                <button type="submit" name="action" value="home" class="login-menu-button">Return to Home</button>
        </form>
    </div>
        </div>
    </div>
</body>
</html>