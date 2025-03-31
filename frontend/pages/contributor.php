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

function LocationsDropDown() {
	global $locations;
	$locations = getLocationOptions();
	$dropdown = '<label for="Locations">Select Location:</label><select name="location">';
	
	for ($i = 0; $i < count($locations['DISASTERLOCATION']); $i++) {
		$disasterLocation = $locations['DISASTERLOCATION'][$i];
		// $dropdown .= '<option value="' . htmlspecialchars($disasterLocation) . '">' '</option>';
		$dropdown .= '<option value="' . htmlspecialchars($disasterLocation) . '">' . htmlspecialchars($disasterLocation) . '</option>';
	}
	$dropdown .= '</select>';
	return $dropdown;
}
echo LocationsDropDown();



// function suppliesDropDown() {
// 	global $rc_name, $rc_location;

// 	$supplies = getSupplyOptions($rc_name, $rc_location);
// 	$dropdown = '<label for="supply">Select Supply:</label><select name="supply">';
// 	for ($i = 0; $i < count($supplies['SUPPLYID']); $i++) {
// 		$supplyID = $supplies['SUPPLYID'][$i];
// 		$supplyName = $supplies['SUPPLYNAME'][$i];
// 		$dropdown .= '<option value="' . htmlspecialchars($supplyID) . '">' . htmlspecialchars($supplyName) . '</option>';
// 	}
// 	$dropdown .= '</select>';

// 	return $dropdown;
// }

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

		<select> </select>

        <hr />
		<br /><br />

		<a href="index.php"> <button> Home </button></a>

	</div>
</body>
</html>

