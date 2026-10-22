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
			<li><a href="../userAccount/login.php">Login</a></li>
			<li><a href="../userAccount/register.php">Register</a></li>
            <b>Appointment will not be here, but will be on home page after successful login/register</b>
            <li><a href="../pages/appointment.php">Appointment</a></li>
		</ul>
	</nav>
	<main>
		<h2>Services</h2>
		<section>
			<?php
			echo "These will be clickable pages to explain what they do.<br>";
			echo "Bathing and Nail Trimming.<br>";
			echo "Boarding.<br>";
			echo "Training.<br>";
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
