<?php include "../components/navbar.php"; ?>
<html>
<head>
	<title>Disaster Relief Project</title>
	<link rel="stylesheet" href="../styles/global.css" type="text/css"/>
</head>
<br /> <br /> <br />
<?php
include("../../backend/contributor_queries.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
handleMissionDisplayRequest();

// runs every time a form is submitted
// the type of request and form data is stored in global var $_POST 
handleRequest();

?>
<body>
	<div id = "missionDisplay"> 
		<?php //handleMissionDisplayRequest() ?>
	</div>
	<div class="index_container">


		<h2>Compare Missions by (PROJECTION)</h2>
		<form method="POST" action="">
			<label>
				<input type="checkbox" name="checkboxes[]" value="MissionID"> Mission ID^
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="MissionType"> Mission Type^
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="DatePosted"> Date Posted^
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="HelpNeeded"> Help Needed^
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="DisasterName"> Disaster Name^
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="DisasterDate"> Disaster Date^
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="DisasterLocation"> Disaster Location^
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="RCName"> Relief Center Name^
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="RCLocation"> Relief Center Location^
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="Priority"> Priority^
			</label><br>
			<br>
			<input type="submit" value="Submit">
		</form>

		<hr />

		<h2>Find Average Priority of Each Mission Type (AGGREGATION with GROUP BY)</h2>
		<form method = "POST" action = ""> 
			<input type="submit" name="aggregateGroupByMission" value="Aggregate!">
			</input><br>
		</form>
		

		<hr />
		<br />
		<h2> Find Disasters Names with Total Help Needed Greater Than (AGGREGATION with HAVING): </h2>
		<form method="POST" action="">
			<label for="greaterThan"></label>
			<input type="number" id="greaterThan" name="greaterThan" required> <br /><br />
			<input type="submit" value="Submit"></p>

		</form>

		<hr />
		<br />
		<h2>Find Disasters with All Mission Types (DIVISION): </h2>
		<form method="POST" action="">
			<input type="submit" name="divisionMission" value="Submit">
			</input><br>
		</form>
        <hr />
		<br /><br />

		<a href="index.php"> <button> Home </button></a>

	</div>
</body>
</html>

