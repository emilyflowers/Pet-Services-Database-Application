<?php
session_start();
if (($_SESSION['IsAdmin'] ?? 0) != 1) { header("Location: login.php"); exit; }

$con = mysqli_connect("localhost", "root", "", "petservices_db");
$id = (int)($_GET['EId'] ?? 0);
$self = ($id == $_SESSION['Eid']);

if (isset($_POST['update'])) {
    // an admin can't remove their own admin rights (avoids locking everyone out)
    $adm = $self ? 1 : (isset($_POST['IsAdmin']) ? 1 : 0);

    if ($_POST['Password'] !== '') {
        $stmt = mysqli_prepare($con, "UPDATE employee SET Name=?, IsAdmin=?, Password=? WHERE EId=?");
        mysqli_stmt_bind_param($stmt, "sisi", $_POST['Name'], $adm, $_POST['Password'], $id);
    } else {
        $stmt = mysqli_prepare($con, "UPDATE employee SET Name=?, IsAdmin=? WHERE EId=?");
        mysqli_stmt_bind_param($stmt, "sii", $_POST['Name'], $adm, $id);
    }
    mysqli_stmt_execute($stmt);
    header("Location: empHome.php?action=employees");
    exit;
}

$data = mysqli_fetch_assoc(mysqli_query($con, "SELECT EId, Name, IsAdmin FROM employee WHERE EId = $id"));
if (!$data) { die("Employee not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Edit Employee</title></head>
<body>
<h3>Update Employee #<?php echo $id; ?></h3>
<form method="POST">
    <p>Name: <input type="text" name="Name" value="<?php echo htmlspecialchars($data['Name']); ?>" maxlength="50" required></p>
    <p>New password: <input type="text" name="Password" placeholder="Leave blank to keep current"></p>
    <p><label><input type="checkbox" name="IsAdmin" <?php if ($data['IsAdmin']) echo 'checked'; ?> <?php if ($self) echo 'disabled'; ?>> Admin</label>
       <?php if ($self) echo "(you can't remove your own admin rights)"; ?></p>
    <input type="submit" name="update" value="Update">
    <a href="empHome.php?action=employees">Cancel</a>
</form>
</body>
</html>