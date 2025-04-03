<?php
include("connection.php");
include("db_utils.php");

function handleRequest()
{
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('checkboxes', $_POST)) {
            projectMission();
        } else if (array_key_exists('aggregateGroupByMission', $_POST)) {
            aggregateGroupByMission();
        } else if (array_key_exists('greaterThan', $_POST)) {
            aggregateGreaterThan();
        } else if (array_key_exists('divisionMission', $_POST)) {
            divisionMission();
        }

        disconnectFromDB();
    }
}

function divisionMission() {
    if (connectToDB()) {
        global $db_conn;
        $query = "SELECT DISTINCT D.name, D.disasterDate, D.location
                    FROM Disaster D
                    WHERE NOT EXISTS (
                        SELECT M.missionType
                        FROM Mission M
                        WHERE NOT EXISTS (
                            SELECT *
                            FROM Mission M2
                            WHERE M2.disasterName = D.name
                            AND M2.disasterDate = D.disasterDate
                            AND M2.disasterLocation = D.location
                            AND M2.missionType = M.missionType
                        )
                    )";
        $result = executePlainSQL($query);
        echo getTableString($result);
        disconnectFromDB();
    }


}

function aggregateGreaterThan() {
    if (connectToDB()) {
        global $db_conn;
        $value = 0;
        if (isset($_POST['greaterThan']) && is_numeric($_POST['greaterThan'])) {
            $value = intval($_POST['greaterThan']); 
        } else {
            echo "No value was submitted.";
            disconnectFromDB();
            return;
        }    
        $query = "SELECT disasterName, SUM(helpNeeded) AS totalHelp FROM Mission GROUP BY disasterName HAVING SUM(helpNeeded) > {$value} ORDER BY SUM(helpNeeded) DESC";
        $result = executePlainSQL($query);
        echo getTableString($result);
        disconnectFromDB();
    }
}


function handleMissionDisplayRequest()
{
    if (connectToDB()) {
        global $db_conn;
        $result = executePlainSQL("SELECT * FROM Mission");
        echo getTableString($result);
        disconnectFromDB();
    }
}


function projectMission() {

    if (connectToDB()) {
        global $db_conn;
        $validColumns = ['MissionID', 'MissionType', 'DatePosted', 'HelpNeeded', 'DisasterName', 'DisasterDate', 'DisasterLocation', 'RCName', 'RCLocation', 'Priority'];
        $query = "SELECT DISTINCT";
        if (isset($_POST['checkboxes']) && is_array($_POST['checkboxes'])) {
            // Loop through all selected checkbox values
            foreach ($_POST['checkboxes'] as $checkbox) {
                if (in_array($checkbox, $validColumns)) {
                // adds to select query
                $query .= " " . $checkbox . ",";
                }
            }
            // gets rid of leading ","
            $query = substr($query,0,-1);
            $query .= " FROM Mission";
        } else {
            echo "No checkboxes selected.";
            disconnectFromDB();
            return;
        }
        $result = executePlainSQL($query);
        echo getTableString($result);
        disconnectFromDB();
    }
}

function aggregateGroupByMission() {
    if (connectToDB()) {
        global $db_conn;
        $result = executePlainSQL("SELECT MissionType, AVG(Priority) AS AvgPriority, COUNT(*) AS MissionCount FROM Mission GROUP BY MissionType");
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