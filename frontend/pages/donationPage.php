<?php include "../components/navbar.php"; ?>
<?php include("../../backend/donation_page_queries.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/global.css" type="text/css"/>
    <title>Donation Page</title>
</head>
<body>
    <div class="index_container">
        <form method="POST" action="">
			<input type="hidden" id="donationRequest" name="donationRequest">
			<label for="donorName">Name:</label>
			<input type="text" id="donorName" name="donorName"><br><br>

			<label for="donorPhoneNumber">Phone Number:</label>
			<input type="tel" id="donorPhoneNumber" name="donorPhoneNumber"><br><br>
            
            <label for="donationItem">Donation Item:</label>
			<input type="text" id="donationItem" name="donationItem"><br><br>

            <!-- Relief center will handle the donation distribution? -->
			<!-- <label for="rcName">Relief Center Name:</label>
			<input type="text" id="rcName" name="rcName"><br><br> -->

			<!-- <label for="rcLocation">Relief Center Location:</label>
			<input type="text" id="rcLocation" name="rcLocation"><br><br> -->

			<label for="donationAmount">Donation Amount:</label>
			<input type="number" id="donationAmount" name="donationAmount"><br><br>


			<input type="submit" value="Donate"></p>
		</form>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["donorName"]) && !empty($_POST["donationAmount"]) && !empty($_POST["rcName"]) && !empty($_POST["rcLocation"])): ?>
            <div class="thank_you_message">
                <h4>Thank you for your generosity!</h4>
                <h4>We appreciate your support in helping those in need.</h4>
                <h4>We will send you a confirmation email shortly.</h4>
            </div>

            <div class="donation_summary">
                <h2>Donation Summary</h2>
                <p>Donor Name: <?php echo $_POST['donorName'] ?? ''; ?></p>
                <p>Donor Phone Number: <?php echo $_POST['donorPhoneNumber'] ?? ''; ?></p>
                <p>Donation Item: <?php echo $_POST['donationItem'] ?? ''; ?></p>
                <p>Donation Amount: $<?php echo $_POST['donationAmount'] ?? ''; ?></p>
                <p>Date Sent: <?php echo date("Y-m-d H:i:s"); ?></p>
                <!-- <p>Relief Center Name: <?php echo $_POST['rcName'] ?? ''; ?></p>
                <p>Relief Center Location: <?php echo $_POST['rcLocation'] ?? ''; ?></p> -->
            </div>
        <?php endif; ?>
        
    </div>
    
</body>
</html>

<?php handleRequest(); ?>