<?php include "../components/navbar.php"; ?>
<html>
<head>
	<title>Disaster Relief Project</title>
	<link rel="stylesheet" href="../styles/index.css" type="text/css"/>
</head>
<body>
	<!-- TODO: only shows tuples from Disaster; no inserts or updates yet -->
	<div class="index_container">
		<h2>Reset All Disaster Relief Tables</h2>
		<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

		<form method="POST" action="">
			<!-- "action" specifies the file or page that will receive the form data for processing. As with this example, it can be this same file. -->
			<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
			<p><input type="submit" value="Reset" name="reset"></p>
		</form>

		<hr />

		<h2>Insert Values into ??</h2>
		<form method="POST" action="">
			<input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
			Number: <input type="text" name="insNo"> <br /><br />
			Name: <input type="text" name="insName"> <br /><br />
			<input type="submit" value="Insert" name="insertSubmit"></p>
		</form>

		<hr />

		<h2>Update Name in ??</h2>
		<p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

		<form method="POST" action="">
			<input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
			Old Name: <input type="text" name="oldName"> <br /><br />
			New Name: <input type="text" name="newName"> <br /><br />

			<input type="submit" value="Update" name="updateSubmit"></p>
		</form>

		<hr />

		<h2>Count the Tuples in Disaster</h2>
		<form method="GET" action="">
			<input type="hidden" id="countTupleRequest" name="countTupleRequest">
			<input type="submit" name="countTuples"></p>
		</form>


		<hr />

		<h2>View Disasters</h2>
		<form method="GET" action="">
			<label for="disasterName">Disaster Name:</label>
			<input type="text" id="disasterName" name="disasterName"><br><br>

			<label for="disasterDate">Disaster Date:</label>
			<input type="date" id="disasterDate" name="disasterDate"><br><br>

			<label for="disasterLocation">Disaster Location:</label>
			<input type="text" id="disasterLocation" name="disasterLocation"><br><br>

			<label for="damageCost">Damage Cost:</label>
			<input type="number" id="damageCost" name="damageCost"><br><br>

			<label for="casualties">Casualties:</label>
			<input type="number" id="casualties" name="casualties"><br><br>

			<label for="severityLevel">Severity Level:</label>
			<input type="number" id="severityLevel" name="severityLevel"><br><br>

			<label for=type>Disaster Type:</label>
			<select id="type" name="type">
				<option value="">Select a type:</option>
				<option value="Flood">Flood</option>
				<option value="Earthquake">Earthquake</option>
				<option value="Tornado">Tornado</option>
				<option value="Hurricane">Hurricane</option>
				<option value="Fire">Fire</option>
			</select>

			
			<input type="hidden" id="displayDisasterTuplesRequest" name="displayDisasterTuplesRequest">
			<input type="submit" name="displayDisasterTuples"></p>
		</form>

		<hr />

		
		<h2>View Missons</h2>
		<h4>Display missions asociated with a disaster</h4>
		<form method="GET" action="">
			<label for="missionName">Mission Name:</label>
			<input type="text" id="missionName" name="missionName"><br><br>

			<label for="datePosted">Date Posted:</label>
			<input type="date" id="datePosted" name="datePosted"><br><br>

			<label for="helpNeeded">Help Needed:</label>
			<input type="number" id="helpNeeded" name="helpNeeded"><br><br>

			<label for="disasterName">Disaster Name</label>
			<input type="text" id="disasterName" name="disasterName"><br><br>

			<label for="disasterDate">Disaster Date:</label>
			<input type="date" id="disasterDate" name="disasterDate"><br><br>

			<label for="disasterLocation">Disaster Location:</label>
			<input type="text" id="disasterLocation" name="disasterLocation"><br><br>

			<label for="rcName">Relief Center Name</label>
			<input type="text" id="rcName" name="rcName"><br><br>

			<label for="rcLocation">Relief Center Location:</label>
			<input type="text" id="rcLocation" name="rcLocation"><br><br>

			<label for="priority">Priority</label>
			<input type="number" id="priority" name="priority"><br><br>

			<input type="hidden" id="displayMissionTuplesRequest" name="displayMissionTuplesRequest">
			<input type="submit" name="displayMissionTuples"></p>
		</form>

		<h2>Display Disaster Relief Progress</h2>
		<form method="GET" action="">
			<input type="hidden" id="displayDisasterReliefProgressRequest" name="displayDisasterReliefProgressRequest">
			<input type="submit" name="displayDisasterReliefProgress"></p>
		</form>

	</div>
</body>
</html>
<?php
include("../../backend/main_page_queries.php");
// runs every time a form is submitted
// the type of request and form data is stored in global var $_POST 
handleRequest();
?>
