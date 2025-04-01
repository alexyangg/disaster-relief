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
	for ($i = 0; $i < count($shelters['NAME']); $i++) {
		$name = $shelters['NAME'][$i] . '@' . $shelters['LOCATION'][$i];
		$dropdown .= '<option value="' . htmlspecialchars($name) . '">' . htmlspecialchars($name) . '</option>';
	}
	$dropdown .= '</select>';

	return $dropdown;
}

// every time a form is submitted, page is refreshed and code below is run
// the type of request and form data is stored in global var $_POST 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	handleRequest();
	// header("Location: ".$_SERVER['PHP_SELF']);
    // exit;
}

?>
<body>
	<!-- TODO: only shows tuples from Disaster; no inserts or updates yet -->
	<div class="index_container">
		<div id="suppliesDisplay">
			<h2>Supplies For <?php echo $rc_name; ?> in <?php echo $rc_location; ?></h2>
			<?php displaySupplies()?>
		</div>

		<!-- From current RC's supplies, update a supply's -->
		<h2>Update Supply Information</h2>
		<form method="POST" action="">
			<input type="hidden" id="updateSupplyRequest" name="updateSupplyRequest">
			<?php echo suppliesDropDown();?> <br /><br />
			<label for="quality">Set Quality:</label>
			<select name="quality">
				<option value="">--None Selected--</option>
				<option value="New">New</option>
				<option value="Excellent">Excellent</option>
				<option value="Good">Good</option>
				<option value="Fair">Fair</option>
				<option value="Poor">Poor</option>
			</select> <br /><br />
			<label>Update Quantity:</label>
			<select name="opType">
				<option value="add">Increase by</option>
				<option value="sub">Decrease by</option>
			</select>
			<input type="number" name="quantity">
			<br /><br />
			<label for="expDate">Set Expiration Date:</label><input type="date" name="expDate"> <br /><br />
			<input type="submit" value="Update" name="insertSubmit"></p>
		</form>

		<hr />

        <!-- From current RC's supplies, updates a supply's quantity, shelter FK -->
		<h2>Send Supplies to a Shelter</h2>
		<form method="POST" action="">
			<input type="hidden" id="sendSupplyRequest" name="sendSupplyRequest">
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
