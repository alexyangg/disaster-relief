<?php include "../components/navbar.php"; ?>
<html>
<head>
	<title>Disaster Relief Project</title>
	<link rel="stylesheet" href="../styles/global.css" type="text/css"/>
</head>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../backend/rc_queries.php");

function suppliesDropDown() {
	$supplies = getSupplyOptions();
	$dropdown = '<label for="supply">Select Supply:</label><select name="supply">';
	for ($i = 0; $i < count($supplies['SUPPLYID']); $i++) {
		$supplyID = $supplies['SUPPLYID'][$i];
		$label = "{$supplies['SUPPLYNAME'][$i]} ({$supplies['SHELTERNAME'][$i]})";
		$dropdown .= '<option value="' . htmlspecialchars($supplyID) . '">' . $label . '</option>';
	}
	$dropdown .= '</select>';

	return $dropdown;
}

function sheltersDropDown() {
	$shelters = getShelterOptions();
	$dropdown = '<label for="shelter">Send to:</label><select name="shelter">';
	$dropdown .= '<option value="">--None Selected--</option>';
	for ($i = 0; $i < count($shelters['NAME']); $i++) {
		$name = $shelters['NAME'][$i] . '@' . $shelters['LOCATION'][$i];
		$dropdown .= '<option value="' . htmlspecialchars($name) . '">' . htmlspecialchars($name) . '</option>';
	}
	$dropdown .= '</select>';

	return $dropdown;
}

function disasterDropdown() {
	$disasters = getDisasterOptions();

	$dropdown = '<label for="disaster">Assign to a Disaster:</label><select name="disaster">';
	for ($i = 0; $i < count($disasters['NAME']); $i++) {
		$key = $disasters['NAME'][$i] . '@' . $disasters['LOCATION'][$i] . '@' . $disasters['DISASTERDATE'][$i];
		$name = $disasters['NAME'][$i] . " @{$disasters['LOCATION'][$i]}  (Began on {$disasters['DISASTERDATE'][$i]})";
		$dropdown .= '<option value="' . htmlspecialchars($key) . '">' . htmlspecialchars($name) . '</option>';
	}
	$dropdown .= '</select>';

	return $dropdown;
}

$missingHelpResult = NULL;
$missionTableResult = NULL;

// every time a form is submitted, page is refreshed and code below is run
// the type of request and form data is stored in global var $_POST 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	handleRequest();
	// header("Location: ".$_SERVER['PHP_SELF']);
    // exit;
}

?>
<body>
	<div class="index_container">
		<h1>Admin Page for <?php echo $rc_name; ?>, <?php echo $rc_location; ?></h2>
		<div>
			<h2>Initiate a Mission (INSERT)</h2>
			<form method="POST" action="">
				<input type="hidden" id="createMissionRequest" name="createMissionRequest">
				<?php echo disasterDropDown();?> <br /><br />
				<label for="priority">Mission Priority:</label><input type="range" id="priority" name="priority" min="1" max="10" value="5" step="1"><br /><br />
				<label for="helpNeeded">Volunteers Needed:</label><input type="number" name="helpNeeded"> <br /><br />
				<label for="missionType">Type of Mission:</label><input name="missionType"> <br /><br />
				<input type="submit" value="Create Mission" name="insertSubmit"></p>
			</form>
			<?php echo $missionTableResult ?>
		</div>
		<hr/>
		<div>
			<h2>Query for Understaffed Missions (Nested Aggregation with GROUP BY)</h2>
			<p>Finds missions that have less volunteers than the amount of help needed and shows the difference.</p>
			<form method="post">
				<input type="hidden" id="missingHelpQuery" name="missingHelpQuery">
				<button type="submit" name="btnClick">Click Me!</button>
			</form>
			<?php echo $missingHelpResult ?>
		</div>

		<hr />

		<div id="suppliesDisplay">
			<h2>Supplies For <?php echo $rc_name; ?> in <?php echo $rc_location; ?></h2>
			<?php displaySupplies()?>
		</div>

		<hr />

		<h2>Update/Send Supplies (UPDATE)</h2>
		<form method="POST" action="">
			<input type="hidden" id="updateSupplyRequest" name="updateSupplyRequest">
			<?php echo suppliesDropDown();?> <br /><br />
			<label for="supplyName">Change Name: </label><input name="supplyName"> <br /><br />
			<label for="quality">Change Quality:</label>
			<select name="quality">
				<option value="">--None Selected--</option>
				<option value="New">New</option>
				<option value="Excellent">Excellent</option>
				<option value="Good">Good</option>
				<option value="Fair">Fair</option>
				<option value="Poor">Poor</option>
			</select> <br /><br />
			<label>Add Quantity:</label>
			<input type="number" name="quantity">
			<br /><br />		
			<label for="expDate">Change Expiration Date:</label><input type="date" name="expDate"> <br /><br />
			<div>
				<h3>Send Supply?</h3>
				<?php echo sheltersDropDown();?> <br /><br />
				<label for="sendAmount">Send Quantity:</label><input type="number" name="sendAmount"> <br /><br />
				<input type="submit" value="Update/Send Supply" name="insertSubmit"></p>
			</div>
		</form>

		<hr />

		<!-- if amount to remove is less than current amount, deletes supply from DB -->
		<h2>Remove Supplies (DELETE QUERY)</h2>
		<form method="POST" action="">
			<input type="hidden" id="deleteSupply" name="deleteSupplyRequest">
			<?php echo suppliesDropDown();?> <br /><br />
			<label for="removeAmount">Amount to remove:</label><input type="number" name="removeAmount"> <br /><br />
			<input type="submit" value="Remove Supply" name="insertSubmit"></p>
		</form>

		<hr />
	</div>
</body>
</html>
