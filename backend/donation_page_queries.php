<?php
include("db_utils.php");

function handleDonationRequest() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        global $db_conn;

        // Get form input values
        $donorName = trim($_POST['donorName']);
        $donorPhoneNumber = trim($_POST['donorPhoneNumber']);
        $donationItem = trim($_POST['donationItem']);
        $donationAmount = trim($_POST['donationAmount']);
        $rcName = trim($_POST['rcName']);
        $rcLocation = trim($_POST['rcLocation']);
        $dateSent = date("Y-m-d"); // Current date

        // Check if donor exists in Contributor (Donor must reference Contributor)
        $stmt = $db_conn->prepare("SELECT name, phoneNumber FROM Contributor WHERE name = ? AND phoneNumber = ?");
        $stmt->bind_param("ss", $donorName, $donorPhoneNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result->fetch_assoc()) {
            die("Error: Donor must be a registered Contributor first.");
        }

        // Check if donor exists in Donor table
        $stmt = $db_conn->prepare("SELECT name, phoneNumber FROM Donor WHERE name = ? AND phoneNumber = ?");
        $stmt->bind_param("ss", $donorName, $donorPhoneNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result->fetch_assoc()) {
            // Insert into Donor if not exists
            $stmt = $db_conn->prepare("INSERT INTO Donor (name, phoneNumber, totalDonated) VALUES (?, ?, ?)");
            $initialDonation = $donationAmount;
            $stmt->bind_param("ssi", $donorName, $donorPhoneNumber, $initialDonation);
            if (!$stmt->execute()) {
                die("Error inserting donor: " . $stmt->error);
            }
        }

        // Check if donation item exists in DonationType
        $stmt = $db_conn->prepare("SELECT itemName FROM DonationType WHERE itemName = ?");
        $stmt->bind_param("s", $donationItem);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result->fetch_assoc()) {
            die("Error: The specified donation item is not recognized.");
        }

        // Check if relief center exists
        $stmt = $db_conn->prepare("SELECT name, location FROM ReliefCenter WHERE name = ? AND location = ?");
        $stmt->bind_param("ss", $rcName, $rcLocation);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result->fetch_assoc()) {
            die("Error: The specified relief center does not exist.");
        }

        // Generate a unique donation ID
        $donationID = mt_rand(100000, 999999);

        // Insert donation record
        $stmt = $db_conn->prepare("
            INSERT INTO Donation (donationID, donationAmount, dateSent, itemName, donorName, donorPhoneNumber, rcName, rcLocation) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssss", $donationID, $donationAmount, $dateSent, $donationItem, $donorName, $donorPhoneNumber, $rcName, $rcLocation);

        if (!$stmt->execute()) {
            die("Error inserting donation: " . $stmt->error);
        }

        // Update totalDonated for Donor
        $stmt = $db_conn->prepare("UPDATE Donor SET totalDonated = totalDonated + ? WHERE name = ? AND phoneNumber = ?");
        $stmt->bind_param("iss", $donationAmount, $donorName, $donorPhoneNumber);
        if (!$stmt->execute()) {
            die("Error updating total donations: " . $stmt->error);
        }

        echo "<script>alert('Donation successfully recorded!');</script>";
    }
}

function handleRequest()
{
    if (connectToDB()) {
        if (array_key_exists('donationRequest', $_POST)) {
            echo "donated";
            handleDonationRequest();
        }

        disconnectFromDB();
    }
}
?>