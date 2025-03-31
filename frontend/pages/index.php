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

		<h2>Display Tuples in Disaster</h2>
		<form method="GET" action="">
			<input type="hidden" id="displayTuplesRequest" name="displayTuplesRequest">
			<input type="submit" name="displayTuples"></p>
		</form>

		<hr />
		<p> test </p>

		<h2>Display Tuples in Missons</h2>
		<form method="GET" action="">
			<input type="hidden" id="displayMissionTuplesRequest" name="displayMissionTuplesRequest">
			<input type="submit" name="displayMissionTuples"></p>
		</form>

		<select> </select>
	</div>
</body>
</html>
<?php
include("../../backend/backend.php");
// runs every time a form is submitted
// the type of request and form data is stored in global var $_POST 
handleRequest();
?>
