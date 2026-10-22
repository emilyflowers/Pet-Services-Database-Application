
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Appointment</title>
    <meta charset="utf-8">
    <link href="styles.css" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Book an Appointment</h1>
    </header>
    <main>
        <h2>Appointment Form</h2>

		<section>
			<?php
                if (isset($_POST["sub"])) {
                    $nailTrim = isset($_POST["isNailTrim"]) ? 1 : 0;
                    $bathing  = isset($_POST["isBathing"])  ? 1 : 0;
                    $boarding = isset($_POST["isBoarding"]) ? 1 : 0;
                    $training = isset($_POST["isTraining"]) ? 1 : 0;

                    $startTime = $_POST["startTime"];
                    if ($nailTrim == 1)
                    $endTime   = $_POST["endTime"];

                    $sql = "INSERT INTO appointment
                                (NailTrim, Bathing, Boarding, Training, StartTime, EndTime, Aid, Pid, Eid)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $stmt = $myDBconnection->prepare($sql);
                    $stmt->execute([$nailTrim, $bathing, $boarding, $training,
                                    $startTime, $endTime, $aid, $pid, $eid]);

                    echo "<p>Appointment added.</p>";
                }
            ?>
        </section>

        <section>
            <h3>What would you like done?</h3>
            <form action="#" method="POST">
                <fieldset>
                    <legend>Services</legend>

                    <label for="nailTrim">Nail Trim</label>
                    <input type="checkbox" name="isNailTrim" id="nailTrim" value="1"><br>

                    <label for="bathing">Bathing</label>
                    <input type="checkbox" name="isBathing" id="bathing" value="1"><br>

                    <label for="boarding">Boarding</label>
                    <input type="checkbox" name="isBoarding" id="boarding" value="1"><br>

                    <label for="training">Training</label>
                    <input type="checkbox" name="isTraining" id="training" value="1"><br>

                    <label for="startTime">Start Time</label>
                    <input type="datetime-local" name="startTime" id="startTime" required><br>

                  
                </fieldset>

                <input type="submit" name="sub" value="Add Appointment">
            </form>
        </section>
    </main>
</body>
</html>
