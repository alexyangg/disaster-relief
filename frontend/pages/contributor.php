<?php include "../components/navbar.php"; ?>
<html>
<head>
	<title>Disaster Relief Project</title>
	<link rel="stylesheet" href="../styles/index.css" type="text/css"/>
</head>
<br /> <br /> <br />
<?php
include("../../backend/contributor_queries.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
handleMissionDisplayRequest();

// function LocationsDropDown() {
// 	$locations = getLocationOptions();
// 	$dropdown = '<label for="Locations">Select Location:</label><select name="location">';
	
// 	for ($i = 0; $i < count($locations['DISASTERLOCATION']); $i++) {
// 		$disasterLocation = $locations['DISASTERLOCATION'][$i];
// 		$dropdown .= '<option value="' . htmlspecialchars($disasterLocation) . '">' . htmlspecialchars($disasterLocation) . '</option>';
// 	}
// 	$dropdown .= '</select>';
// 	return $dropdown;
// }
// echo LocationsDropDown();



// runs every time a form is submitted
// the type of request and form data is stored in global var $_POST 
handleRequest();

?>
<body>
	<div id = "missionDisplay"> 
		<?php //handleMissionDisplayRequest() ?>
	</div>
	<div class="index_container">
		<h2>Reset All Disaster Relief Tables</h2>
		<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

		<form method="POST" action="">
			<!-- "action" specifies the file or page that will receive the form data for processing. As with this example, it can be this same file. -->
			<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
			<p><input type="submit" value="Reset" name="reset"></p>
		</form>


		<hr />


		<!-- <h2>Display Tuples in Missons</h2>
		<form method="GET" action="">
			<input type="hidden" id="displayMissionTuplesRequest" name="displayMissionTuplesRequest">
			<input type="submit" name="displayMissionTuples"></p>
		</form> -->

		<!-- <h2>Filter Missions by</h2>
		<form method="GET" action="">
			<input type="hidden" id="filterMissionRequest" name="filterMissionRequest">
			<?php //echo LocationsDropDown();?> <br /><br />
			<label for="Help">Quantity:</label><input type="number" name="quantity"> <br /><br />
			<label for="quality">Quality:</label><input type="text" name="quality"> <br /><br />
			<label for="expDate">Expiration Date:</label><input type="text" name="expDate"> <br /><br />
			<input type="submit" value="Filter" name="insertSubmit"></p>
		</form> -->
		<!--MISSIONID	MISSIONTYPE	DATEPOSTED	HELPNEEDED	DISASTERNAME	DISASTERDATE	DISASTERLOCATION	RCNAME	RCLOCATION	PRIORITY -->
		<h2>Filter Missions by</h2>
		<form method="POST" action="">
			<label>
				<input type="checkbox" name="checkboxes[]" value="MissionType"> Mission Type
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="DatePosted"> Date Posted
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="HelpNeeded"> Help Needed
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="DisasterName"> Disaster Name
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="DisasterDate"> Disaster Date
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="DisasterLocation"> Disaster Location
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="RCName"> Relief Center Name
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="RCLocation"> Relief Center Location
			</label><br>
			<label>
				<input type="checkbox" name="checkboxes[]" value="Priority"> Priority
			</label><br>
			<input type="submit" value="Submit">
		</form>

		
        <hr />
		<br /><br />

		<a href="index.php"> <button> Home </button></a>

	</div>
</body>
</html>

