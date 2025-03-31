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

function getLocationOptions() {
    $locations = array();
    if (connectToDB()) {
        global $db_conn;

        $query = "SELECT DISTINCT disasterLocation FROM Mission";
        $result = executePlainSQL($query);
        oci_fetch_all($result, $locations, 0, -1, OCI_ASSOC);

        disconnectFromDB();
    }

    return $locations;
}


function handleMissionDisplayRequest()
{
    if (connectToDB()) {
        global $db_conn;
        $result = executePlainSQL("SELECT * FROM Mission");
        // printResult($result);
        echo getTableString($result);
        disconnectFromDB();
    }
}

function printResult($result)
{
    echo "<br>Retrieved data from table Mission:<br>";
    echo "<table>";
    $first_row = true;
    while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
        // For the first row, generate headers dynamically
        if ($first_row) {
            echo "<tr>";
            foreach ($row as $column_name => $value) {
                echo "<th>" . htmlspecialchars($column_name) . "</th>";
            }
            echo "</tr>";
            $first_row = false;
        }

        // Print the row data
        echo "<tr>";
        foreach ($row as $column_name => $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

function handleResetRequest()
{
    global $db_conn;
    echo "<br> creating new disaster relief tables...<br>";
    executeSQLFile('../disasterrelief.sql');
}

?>