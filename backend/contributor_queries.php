<?php
include("connection.php");
include("db_utils.php");
// fix the issue on why mission tuples are not displayed

function handleRequest()
{
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('checkboxes', $_POST)) {
            projectMission();
        } else if (array_key_exists('aggregateGroupByMission', $_POST)) {
            aggregateGroupByMission();
        }

        disconnectFromDB();
    }
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


function projectMission() {

    if (connectToDB()) {
        global $db_conn;
        $query = "SELECT ";
        if (isset($_POST['checkboxes']) && is_array($_POST['checkboxes'])) {
            // Loop through all selected checkbox values
            foreach ($_POST['checkboxes'] as $checkbox) {
                // adds to select query
                $query .= " " . $checkbox . ",";
            }
            // gets rid of leading ","
            $query = substr($query,0,-1);
            $query .= " FROM Mission";
        } else {
            echo "No checkboxes selected.";
        }
        $result = executePlainSQL($query);
        echo getTableString($result);
        disconnectFromDB();
    }
}

function aggregateGroupByMission() {
    if (connectToDB()) {
        global $db_conn;
        $result = executePlainSQL("SELECT MissionType, MAX(Priority) FROM Mission GROUP BY MissionType");
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