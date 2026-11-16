<?php
session_start();
if (($_SESSION['IsAdmin'] ?? 0) != 1) { header("Location: login.php"); exit; }

$con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAdminNew = isset($_POST['IsAdmin']) ? 1 : 0;
    try {
        $stmt = mysqli_prepare($con, "INSERT INTO employee (Name, IsAdmin, Password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sis", $_POST['Name'], $isAdminNew, $_POST['Password']);
        mysqli_stmt_execute($stmt);
        $success = "Employee added. Their employee ID is " . mysqli_insert_id($con) . " (they log in with this).";
    } catch (mysqli_sql_exception $e) {
        $error = "Could not add the employee.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Add Employee</title></head>
<body>
    <h2>Add New Employee</h2>
    <?php if ($error) { ?><p style="color:red"><?php echo $error; ?></p><?php } ?>
    <?php if ($success) { ?><p style="color:green"><?php echo $success; ?></p><?php } ?>
    <form method="POST">
        <p><input type="text" name="Name" placeholder="Full name" maxlength="50" required></p>
        <p><input type="text" name="Password" placeholder="Password" required></p>
        <p><label><input type="checkbox" name="IsAdmin"> Admin</label></p>
        <input type="submit" value="Add Employee">
    </form>
    <p><a href="empHome.php?action=employees">Back to employees</a></p>
</body>
</html>
