<!DOCTYPE html>
<html lang="en">
<head>
	<title>Pet Service Homepage</title>
	<meta charset="utf-8">
</head>
<body>
	<header>
		<h1>
			Welcome
		</h1>
	</header>
	<nav>
		<ul>
			<li><a href="../userAccount/login.php">User Login</a></li>
			<li><a href="../userAccount/register.php">User Register</a></li>
            <li><a href=" ../employeeAccount/login.php">Employee Login</a></li>
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
</body>
</html>
