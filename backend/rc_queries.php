<?php
include("connection.php");
include("db_utils.php");


function handleRequest()
{
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        }

        $suppliesTable = "Hello";
        disconnectFromDB();
    }
}

function displaySupplies($rc_name, $rc_location) {
    if (connectToDB()) {
        global $db_conn;
        $supplies = executePlainSQL("SELECT * FROM Supplies WHERE rcName='{$rc_name}' AND rcLocation='{$rc_location}'");
        echo getTableString($supplies);

        disconnectFromDB();
    }
}

function getSupplyOptions($rc_name, $rc_location) {
    $supplies = array() ;
    if (connectToDB()) {
        global $db_conn;

        $query = "SELECT supplyID, supplyName FROM Supplies WHERE rcName='{$rc_name}' AND rcLocation='{$rc_location}'";
        $result = executePlainSQL($query);
        oci_fetch_all($result, $supplies, 0, -1, OCI_ASSOC);

        disconnectFromDB();
    }

    return $supplies;
}

function getShelterOptions($rc_name, $rc_location) {
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