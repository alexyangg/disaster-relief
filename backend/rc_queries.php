<?php
include("db_utils.php");

$rc_name = 'Red Cross Center NO';
$rc_location = 'New Orleans, LA';

function handleRequest()
{
    if (connectToDB()) {
        if (array_key_exists('updateSupplyRequest', $_POST)) {
            handleUpdateSupplyRequest();
        } else if (array_key_exists('createMissionRequest', $_POST)) {
            handleCreateMissionRequest();
        } else if (array_key_exists('deleteSupplyRequest', $_POST)) {
            handleDeleteSupplyRequest();
        } else if (array_key_exists('missingHelpQuery', $_POST)) {
            handleMissingHelpQuery();
        }

        disconnectFromDB();
    }
}

function handleMissingHelpQuery() {
    global $db_conn, $missingHelpResult;
    global $rc_name, $rc_location;

    $query = "
    SELECT m.missionID, m.helpNeeded - COUNT(DISTINCT vf.name || vf.phoneNUmber) AS helpStillNeeded
    FROM VolunteersFor vf, Mission m
    WHERE m.missionID = vf.missionID
    GROUP BY m.missionID, m.helpNeeded
    HAVING m.helpNeeded > (
        SELECT COUNT(*) 
        FROM VolunteersFor vf2
        WHERE vf2.missionID = m.missionID
    )
    ";

    $result = executePlainSQL($query);
    oci_commit($db_conn);

    $missingHelpResult = getTableString($result);
}

function handleDeleteSupplyRequest() {
    global $db_conn;
    global $rc_name, $rc_location;

    if ($_POST['removeAmount'] <= 0) {
        echo "<script>alert('Must remove a positive amount');</script>";
        return;
    }

    deleteSupply($_POST['supply'], $_POST['removeAmount']);
}

function handleCreateMissionRequest() {
    global $db_conn, $missionTableResult;
    global $rc_name, $rc_location;

    $cmdStrInsert = "INSERT INTO Mission VALUES (:missionID, 
        :missionType, SYSDATE, :helpNeeded, 
        :name, :disasterDate, :location, 
        :rc_name, :rc_location, :priority)";

    list($name, $location, $disasterDate) = explode("@", $_POST['disaster']);
    $listInsert = [[
            ":missionID" => generateID(),
            ":missionType" => $_POST['missionType'],
            ":helpNeeded" => $_POST['helpNeeded'], // Typo fix: ":helpedNeeded" → ":helpNeeded"
            ":name" => $name,
            ":disasterDate" => $disasterDate,
            ":location" => $location,
            ":rc_name" => $rc_name,
            ":rc_location" => $rc_location,
            ":priority" => $_POST['priority']
    ]];
    
    executeBoundSQL($cmdStrInsert, $listInsert);

    $result = executePlainSQL("SELECT * FROM Mission");
    $missionTableResult = getTableString($result);
    oci_commit($db_conn);
}

function handleUpdateSupplyRequest() {
    global $db_conn;
    global $rc_name, $rc_location;

    $supplyName = $_POST['supplyName'];
    $supplyID = $_POST['supply'];
    $quantity = $_POST['quantity'];
    $quality = $_POST['quality'];
    $expDate = $_POST['expDate'];

    if ($quantity <= 0) {
        echo "<script>alert('Amount to Send Must Be a Positive Number!');</script>";
        return;
    }

    $updates = [];
    $bindings = [];

    if ($supplyName !== "") {
        $updates[] = "supplyName = :supplyName";
        $bindings[":supplyName"] = $supplyName;
    }
    if ($quantity !== "") {
        $updates[] = "quantity = quantity + :quantity";
        $bindings[":quantity"] = $quantity;
    }
    if ($quality !== "") {
        $updates[] = "quality = :quality";
        $bindings[":quality"] = $quality;
    }
    if ($expDate !== "") {
        $updates[] = "expirationDate = TO_DATE(:expDate, 'YYYY-MM-DD')";
        $bindings[":expDate"] = $expDate;
    }

    if (!empty($updates)) {
        $query = "UPDATE Supplies SET " . implode(", ", $updates) . " WHERE supplyID = :supplyID";
        $bindings[":supplyID"] = $supplyID;

        executeBoundSQL($query, [$bindings]); // Pass bindings inside an array
        oci_commit($db_conn);
    }

    if ($_POST['shelter'] !== '') {
        sendSupplyRequest();
    }
}

function sendSupplyRequest() {
    global $db_conn;
    global $rc_name, $rc_location;

    $supplyID = $_POST['supply'];
    list($shelterName, $shelterLocation) = explode("@", $_POST['shelter']);
    $sendAmount = $_POST['sendAmount'];
    
    if ($sendAmount <= 0) {
        echo "<script>alert('Amount to Send Must Be a Positive Number!');</script>";
        return;
    }

    $getQuery = "SELECT * FROM Supplies WHERE supplyID='{$supplyID}'";
    $result = executePlainSQL($getQuery);
    $supply = oci_fetch_row($result);
    if ($sendAmount > $supply[2]) {
        echo "<script>alert('Not enough supplies to send');</script>";
        return;
    }

    if ($shelterName == $supply[4] && $shelterLocation == $supply[5]) {
        echo "<script>alert('Must be sent to a different shelter!');</script>";
        return;
    }

    // Only send amount needs to be sanitized
    $listSend = [[":sendAmount" => $sendAmount]];

    // remove supply from shelter being sent from
    deleteSupply($supplyID, $sendAmount);
    
    // send supply to new shelter; 
    // if supply is already there we just update it, otherwise we add a new supply
    $checkQuery = "SELECT supplyID FROM Supplies WHERE 
        shelterName='{$shelterName}' AND shelterLocation='{$shelterLocation}' AND
        supplyName='{$supply[1]}'";
    $row = oci_fetch_assoc(executePlainSQL($checkQuery));
    if ($row) {
        $existingSupplyID = $row['SUPPLYID'];
        $updateQuery = "UPDATE Supplies SET quantity = quantity + :sendAmount
                        WHERE supplyID = {$existingSupplyID}";
        executeBoundSQL($updateQuery, $listSend);
    } else {
        $gen_id = generateID();
        $insertQuery = "INSERT INTO Supplies VALUES ({$gen_id}, '{$supply[1]}', 
            :sendAmount, '{$supply[3]}', '{$shelterName}', '{$shelterLocation}', 
            '{$supply[6]}', '{$supply[7]}', '{$supply[8]}')";
        executeBoundSQL($insertQuery, $listSend);
    }

    oci_commit($db_conn);
}

function displaySupplies() {
    global $rc_name, $rc_location;

    if (connectToDB()) {
        global $db_conn;
        $supplies = executePlainSQL("SELECT * FROM Supplies WHERE rcName='{$rc_name}' AND rcLocation='{$rc_location}'");
        echo getTableString($supplies, array("SUPPLYNAME", "QUANTITY", "EXPIRATIONDATE", "SHELTERNAME", "SHELTERLOCATION", "QUALITY"));

        disconnectFromDB();
    }
}

function getSupplyOptions() {
    global $rc_name, $rc_location;

    $supplies = array() ;
    if (connectToDB()) {
        global $db_conn;

        $query = "SELECT supplyID, supplyName, shelterName FROM Supplies WHERE rcName='{$rc_name}' AND rcLocation='{$rc_location}'";
        $result = executePlainSQL($query);
        oci_fetch_all($result, $supplies, 0, -1, OCI_ASSOC);

        disconnectFromDB();
    }

    return $supplies;
}

function getShelterOptions() {
    global $rc_name, $rc_location;

    $shelters = array() ;
    if (connectToDB()) {
        global $db_conn;

        $query = "SELECT name, location FROM Shelter WHERE rcName='{$rc_name}' AND rcLocation='{$rc_location}'";
        $result = executePlainSQL($query);
        oci_fetch_all($result, $shelters, 0, -1, OCI_ASSOC);

        disconnectFromDB();
    }

    return $shelters;
}

function getDisasterOptions() {
    global $rc_name, $rc_location;

    $disasters = array() ;
    if (connectToDB()) {
        global $db_conn;

        $query = "SELECT name, location, disasterDate FROM Disaster";
        $result = executePlainSQL($query);
        oci_fetch_all($result, $disasters, 0, -1, OCI_ASSOC);

        disconnectFromDB();
    }

    return $disasters;
}

function deleteSupply($supplyID, $removeAmount) {
    global $db_conn;
    global $rc_name, $rc_location;

    $cmdStrDelete = "UPDATE Supplies SET quantity=quantity-:removeAmount WHERE supplyID=:supplyID";
    $listDelete = [ [":removeAmount" => $removeAmount, ":supplyID" => $supplyID] ];
    executeBoundSQL($cmdStrDelete, $listDelete);

    // supplyID comes from dropdown (which comes directly from our DB), so no need to sanitize
    $getQuantityQuery = "SELECT quantity FROM Supplies WHERE supplyID='{$supplyID}'";
    if (oci_fetch_assoc(executePlainSQL($getQuantityQuery))["QUANTITY"] <= 0) {
        $deleteQuery = "DELETE FROM Supplies WHERE supplyID='{$supplyID}'";
        executePlainSQL($deleteQuery);
    }

    oci_commit($db_conn);
}
?>