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
		<form method="GET" action="">
			<label> 
				<input type="checkbox" id="MissionTypeCheck" name="MissionTypeCheck" value="MissionType"></input> Mission Type 
			</label>
			<label> 
				<input type="checkbox" id="DatePostedCheck" name="DatePostedCheck" value="DatePosted"></input> Date Posted 
			</label>
			<label> 
				<input type="checkbox" id="HelpNeededCheck" name="HelpNeededCheck" value="HelpNeeded"></input> Help Needed 
			</label>
			<label> 
				<input type="checkbox" id="DisasterNameCheck" name="DisasterNameCheck" value="DisasterName"></input> Disaster Name
			</label>
			<label> 
				<input type="checkbox" id="DisasterDateCheck" name="DisasterDateCheck" value="DisasterDate"></input> Disaster Date 
			</label>
			<label> 
				<input type="checkbox" id="DisasterLocationCheck" name="DisasterLocationCheck" value="DisasterLocation"></input> Disaster Location 
			</label>
			<label> 
				<input type="checkbox" id="RCNameCheck" name="RCNameCheck" value="RCName"></input> Relief Center Name
			</label>
			<label> 
				<input type="checkbox" id="RCLocationCheck" name="RCLocationCheck" value="RCLocation"></input> Relief Center Location
			</label>
			<label> 
				<input type="checkbox" id="PriorityCheck" name="PriorityCheck" value="Priority"></input> Priority 
			</label>
		</form>

		
        <hr />
		<br /><br />

		<a href="index.php"> <button> Home </button></a>

	</div>
</body>
</html>

