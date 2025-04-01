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
        } else if (array_key_exists('sendSupplyRequest', $_POST)) {
            handleSendSupplyRequest();
        }

        disconnectFromDB();
    }
}

function handleUpdateSupplyRequest() {
    global $db_conn;
    global $rc_name, $rc_location;

    $supplyID = $_POST['supply'];
    $quantity = $_POST['quantity'];
    $quality = $_POST['quality'];
    $expDate = $_POST['expDate'];

    $updates = [];
    if ($quantity !== "") {
        if ($_POST["opType"] == "sub") {
            $updates[] = "quantity=quantity-'{$quantity}'";
        } else {
            $updates[] = "quantity=quantity+'{$quantity}'";
        }
    }
    if ($quality !== "") $updates[] = "quality='{$quality}'";
    if ($expDate !== "") $updates[] = "expirationDate=TO_DATE('{$expDate}', 'YYYY-MM-DD')";

    if (empty($updates)) {
        return;
    }

    $query = "UPDATE Supplies SET " . implode(", ", $updates) . " WHERE supplyID='{$supplyID}'";
    executePlainSQL($query);
    oci_commit($db_conn);
}

function handleSendSupplyRequest() {
    global $db_conn;
    global $rc_name, $rc_location;
    error_log("HELLO");

    $supplyID = $_POST['supply'];
    list($shelterName, $shelterLocation) = explode("@", $_POST['shelter']);
    $sendAmount = $_POST['sendAmount'];

    if ($sendAmount <= 0) {
        echo "<script>alert('Must send a positive amount');</script>";
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
    
    $gen_id = generateID();
    $query = "INSERT INTO Supplies VALUES ({$gen_id}, '{$supply[1]}', {$sendAmount}, '{$supply[3]}', '{$shelterName}', '{$shelterLocation}', '{$supply[6]}', '{$supply[7]}', '{$supply[8]}')";
    executePlainSQL($query);

    $query = "UPDATE Supplies SET quantity=quantity-{$sendAmount}, shelterName='{$shelterName}', shelterLocation='{$shelterLocation}' WHERE supplyID={$supplyID}";
    executePlainSQL($query);
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
?>