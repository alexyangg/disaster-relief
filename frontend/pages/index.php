<?php include "../components/navbar.php"; ?>
<?php error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../../backend/main_page_queries.php");

$missionTableResult = NULL;

// runs every time a form is submitted
// the type of request and form data is stored in global var $_POST 
handleRequest();
?>
<html>
<head>
	<title>Disaster Relief Project</title>
	<link rel="stylesheet" href="../styles/global.css" type="text/css"/>
	<link rel="stylesheet" href="../styles/index.css" type="text/css"/>
</head>
<body>
	<div class="index_container">
	
		<div class="disaster_container" style="display: flex; flex-direction: row;">
			<div class="disaster_form" style="display: flex; flex-direction: column; justify-content: center; padding-left: 1rem;">
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
						<option value="Earthquake">Earthquake</option>
						<option value="Hurricane">Hurricane</option>
						<option value="Wildfire">Wildfire</option>
						<option value="Tsunami">Tsunami</option>
						<option value="Flood">Flood</option>
						<option value="Tornado">Tornado</option>
					</select>

					
					<input type="hidden" id="displayDisasterTuplesRequest" name="displayDisasterTuplesRequest">
					<input type="submit" name="displayDisasterTuples"></p>
				</form>
			</div>
			<div class="disaster_results_container">
				<h2>Disaster Results</h2>
				<div id="disaster_results">
					<?php
						if (connectToDB() && $_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET['displayDisasterTuples'])) {
							handleDisasterDisplayRequest(); 
						}
					?>
				</div>
			</div>
		</div>

		<hr />

		<h2>View Missons</h2>
		<h4>Display missions associated with a disaster</h4>
		<form method="GET" action="">
			<input id="displayMissionTuples" name="displayMissionTuples" type="hidden">
			<label for="missionID">Mission ID:</label>
			<input type="text" id="missionID" name="missionID"><br><br>

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
			<input type="submit" name="insertSubmit">
		</form>
		<?php echo $missionTableResult ?>
		<hr />

		<div class="relief_center_mission_container">
			<h2>Discover which relief centers are assisting with a given mission (JOIN)</h2>
			<form method="GET" action="">
				<h4>Enter a Mission Type (Rescue, Evacuation, Firefighting, Medical Aid, Reconstruction):</h4>
				<input type="text" id="missionType" name="missionType" required>
				<input type="hidden" id="displayReliefCenterMissionRequest" name="displayReliefCenterMissionRequest">
				<input type="submit" name="displayReliefCenterMission" value="Search">
			</form>
			<?php 
				if (connectToDB() && $_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET['displayReliefCenterMission'])) {
					handleReliefCenterMissionDisplayRequest(); 
				}
			?>
		</div>

		<hr />
		
		<div class="relief_center_donation_container">
			<form method="GET" action="" id="donationForm">
				<h2 style="display: inline;">Find Relief Centers with less than</h2>
				<input type="number" id="donationAmount" name="donationAmount">
				<input type="hidden" id="displayReliefCenterDonationRequest" name="displayReliefCenterDonationRequest">
				<h2 style="display: inline;">in donations (aggregation with HAVING)</h2>
				<input type="submit" name="displayReliefCenterDonation" value="Search">
				<h4>(leave input blank to see all relief centers)</h4>
			</form>
			<?php 
				if (connectToDB() && $_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET['displayReliefCenterDonation'])) {
					handleReliefCenterDonationDisplayRequest(); 
				}
			?>
		</div>

		<hr />
		
		<!-- <a href="contributor.php"> <button type="button"> Contribute </button></a>
	<div>
		<h2>Donate to help our cause!</h2>
		<button onclick="window.location.href='donationPage.php'">Donate Here</button>
	</div> -->
</div>
</body>
</html>
