<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="../pages/style.css"> 
<head>
	<title>Pet Service Homepage</title>
	<meta charset="utf-8">
</head>
<body>
    <div class="main-content">
    <h1>Welcome</h1>
	    <nav>
		    <ul>
			    <a href="../userAccount/login.php">User Login</a>
			    <a href="../userAccount/register.php">User Register</a>
                <a href=" ../employeeAccount/login.php">Employee Login</a>
		    </ul>
	    </nav>
    
	<main>
	    <h2>Services</h2>
	    <section>
		    <?php
			echo "Grooming: $40.<br>";
			echo "Boarding: $50 per night<br>";
			echo "Training: $60<br>";
			?>
	    </section>
	    <section>
		    <h3>About us</h3>
		    <?php
		    echo "We do animal stuff.<br>";
		    ?>
	    </section>
	    
	    <section>
		    <h3>Contact Info</h3>
		    <?php
		    echo "Here is our contact info.<br>";
		    ?>
	    </section>
	</main>
    </div>
</body>
</html>
