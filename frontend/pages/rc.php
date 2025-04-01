<?php include "../components/navbar.php"; ?>
<html>
<head>
	<title>Disaster Relief Project</title>
	<link rel="stylesheet" href="../styles/index.css" type="text/css"/>
</head>
<?php
include("../../backend/rc_queries.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$rc_name = 'Red Cross Center NO';
$rc_location = 'New Orleans, LA';

function suppliesDropDown() {
	global $rc_name, $rc_location;

	$supplies = getSupplyOptions($rc_name, $rc_location);
	$dropdown = '<label for="supply">Select Supply:</label><select name="supply">';
	for ($i = 0; $i < count($supplies['SUPPLYID']); $i++) {
		$supplyID = $supplies['SUPPLYID'][$i];
		$supplyName = $supplies['SUPPLYNAME'][$i];
		$dropdown .= '<option value="' . htmlspecialchars($supplyID) . '">' . htmlspecialchars($supplyName) . '</option>';
	}
	$dropdown .= '</select>';

	return $dropdown;
}

function sheltersDropDown() {
	global $rc_name, $rc_location;

	$shelters = getShelterOptions($rc_name, $rc_location);
	$dropdown = '<label for="shelter">Send to:</label><select name="shelter">';
	for ($i = 0; $i < count($shelters['NAME']); $i++) {
		$name = $shelters['NAME'][$i] . '@' . $shelters['LOCATION'][$i];
		$dropdown .= '<option value="' . htmlspecialchars($name) . '">' . htmlspecialchars($name) . '</option>';
	}
	$dropdown .= '</select>';

	return $dropdown;
}

// every time a form is submitted, page is refreshed and code below is run
// the type of request and form data is stored in global var $_POST 
handleRequest();

?>
<body>
	<!-- TODO: only shows tuples from Disaster; no inserts or updates yet -->
	<div class="index_container">
		<div id="suppliesDisplay">
			<h2>Supplies For <?php echo $rc_name; ?> in <?php echo $rc_location; ?></h2>
			<?php displaySupplies($rc_name, $rc_location)?>
		</div>

		<!-- From current RC's supplies, update a supply's -->
		<h2>Update Supply Information</h2>
		<form method="POST" action="">
			<input type="hidden" id="updateSupplyRequest" name="updateSupplyRequest">
			<?php echo suppliesDropDown();?> <br /><br />
			<label for="quantity">Quantity:</label><input type="number" name="quantity"> <br /><br />
			<label for="quality">Quality:</label><input type="text" name="quality"> <br /><br />
			<label for="expDate">Expiration Date:</label><input type="text" name="expDate"> <br /><br />
			<input type="submit" value="Update" name="insertSubmit"></p>
		</form>

		<hr />

        <!-- From current RC's supplies, updates a supply's quantity, shelter FK -->
		<h2>Send Supplies to a Shelter</h2>
		<form method="POST" action="">
			<input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
			<?php echo suppliesDropDown();?> <br /><br />
			<?php echo sheltersDropDown();?> <br /><br />
			<label for="sendAmount">Amount to send:</label><input type="number" name="sendAmount"> <br /><br />
			<input type="submit" value="Insert" name="insertSubmit"></p>
		</form>

		<hr />

		<h2>Count the Tuples in Disaster</h2>
		<form method="GET" action="">
			<input type="hidden" id="countTupleRequest" name="countTupleRequest">
			<input type="submit" name="countTuples"></p>
		</form>
	</div>
</body>
</html>
