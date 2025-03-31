<?php
include("connection.php");
include("db_utils.php");
// fix the issue on why mission tuples are not displayed

function handleRequest()
{
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        }  else if (array_key_exists('displayMissionTuples', $_GET)) {
            handleMissionDisplayRequest();
        }

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

function handleMissionDisplayRequest()
{
    if (connectToDB()) {
        global $db_conn;
        $result = executePlainSQL("SELECT * FROM Mission");
        printResult($result);

        disconnectFromDB();
    }
}

?>