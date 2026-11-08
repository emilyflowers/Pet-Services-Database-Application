
<?php
session_start();
if (($_SESSION['IsAdmin'] ?? 0) != 1) { header("Location: login.php"); exit; }

$con = mysqli_connect("localhost", "root", "", "petservices_db");
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAdminNew = isset($_POST['IsAdmin']) ? 1 : 0;
    try {
        $stmt = mysqli_prepare($con, "INSERT INTO employee (Name, IsAdmin, Password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sis", $_POST['Name'], $isAdminNew, $_POST['Password']);
        mysqli_stmt_execute($stmt);
        $success = "Employee added! Their employee ID is " . mysqli_insert_id($con) . " (they login with this).";
    } catch (mysqli_sql_exception $e) {
        $error = "Could not add the employee.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Add Employee</title>
    <link rel="stylesheet" href="../pages/style.css">
    <h1 class="header">Pet Services</h1>  
</head>
<body>
<div class="main-content">
    <h2>Add New Employee</h2>
    
    <form method="POST">
        <p>First and Last Name: <input type="text" name="Name" placeholder="" maxlength="50" required></p>
        <p>Password: <input type="text" name="Password" placeholder="" required></p>
        <p>Admin? <label><input type="checkbox" name="IsAdmin"></label></p>
        
        <div class="button-container">
            <button type="submit" name="action" value="Add Employee" class="user-action-button">Add Employee</button>
        </div>
    </form>

    <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
    <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>

        <div class="button-container">
            <form method="GET" action="empHome.php?action=employees" style="display:inline;">
                <button type="submit" name="action" value="employees" class="login-menu-button">Back to Home</button>
            </form>
        </div>
    </div>
</body>
</html>