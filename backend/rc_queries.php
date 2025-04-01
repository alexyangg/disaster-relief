<?php
include("connection.php");
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
        }

        disconnectFromDB();
    }
}

function handleDeleteSupplyRequest() {
    global $db_conn;
    global $rc_name, $rc_location;

    $supplyID = $_POST['supply'];
    $removeAmount = $_POST['removeAmount'];
    if ($removeAmount <= 0) {
        echo "<script>alert('Must remove a positive amount');</script>";
        return;
    }

    $updateQuery = "UPDATE Supplies SET quantity=quantity-{$removeAmount} WHERE supplyID='{$supplyID}'";
    $result = executePlainSQL($updateQuery);

    $getQuantityQuery = "SELECT quantity FROM Supplies WHERE supplyID='{$supplyID}'";
    if (oci_fetch_assoc(executePlainSQL($getQuantityQuery))["QUANTITY"] <= 0) {
        $deleteQuery = "DELETE FROM Supplies WHERE supplyID='{$supplyID}'";
        executePlainSQL($deleteQuery);
    }

    oci_commit($db_conn);
}

function handleCreateMissionRequest() {
    global $db_conn;
    global $rc_name, $rc_location;

    $missionID = generateID();
    list($name, $location, $disasterDate) = explode("@", $_POST['disaster']);
    $helpNeeded = $_POST['helpNeeded'];
    $missionType = $_POST['missionType'];
    $priority = $_POST['priority'];

    $query = "INSERT INTO Mission VALUES ({$missionID}, 
        '{$missionType}', SYSDATE, {$helpNeeded}, '{$name}', 
        '{$disasterDate}', '{$location}', 
        '{$rc_name}', '{$rc_location}', '{$priority}')";

    executePlainSQL($query);
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

    $updates = [];
    if ($supplyName !== "") $updates[] = "supplyName='{$supplyName}'";
    if ($quantity !== "") $updates[] = "quantity=quantity+{$quantity}";
    if ($quality !== "") $updates[] = "quality='{$quality}'";
    if ($expDate !== "") $updates[] = "expirationDate=TO_DATE('{$expDate}', 'YYYY-MM-DD')";

    if (!empty($updates)) {
        $query = "UPDATE Supplies SET " . implode(", ", $updates) . " WHERE supplyID='{$supplyID}'";
        executePlainSQL($query);
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
        echo "<script>alert('Amount to Sent Must Be a Postive Number!');</script>";
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

    // remove supply from shelter being sent from
    $removeQuery = "UPDATE Supplies SET quantity=quantity-{$sendAmount} WHERE supplyID={$supplyID}";
    executePlainSQL($removeQuery);
    
    // send supply to new shelter; 
    // if supply is already there we just update it, otherwise we add a new supply
    $checkQuery = "SELECT supplyID FROM Supplies WHERE 
        shelterName='{$shelterName}' AND shelterLocation='{$shelterLocation}' AND
        supplyName='{$supply[1]}'";
    $row = oci_fetch_assoc(executePlainSQL($checkQuery));
    if ($row) {
        $existingSupplyID = $row['SUPPLYID'];
        $updateQuery = "UPDATE Supplies SET quantity = quantity + {$sendAmount} 
                        WHERE supplyID = {$existingSupplyID}";
        executePlainSQL($updateQuery);
    } else {
        $gen_id = generateID();
        $insertQuery = "INSERT INTO Supplies VALUES ({$gen_id}, '{$supply[1]}', 
            {$sendAmount}, '{$supply[3]}', '{$shelterName}', '{$shelterLocation}', 
            '{$supply[6]}', '{$supply[7]}', '{$supply[8]}')";
        executePlainSQL($insertQuery);
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
?>