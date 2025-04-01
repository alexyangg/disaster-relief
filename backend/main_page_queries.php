<?php
include("connection.php");
include("db_utils.php");

function handleResetRequest()
{
    global $db_conn;
    echo "<br> creating new disaster relief tables...<br>";
    executeSQLFile('../disasterrelief.sql');
}

function handleCountRequest()
{
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM Disaster");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in Disaster: " . $row[0] . "<br>";
    }
}

// TODO: modify the SELECT clause to include user-input params,
// if user hasn't input any params, then select all (*)
function handleDisasterDisplayRequest()
{
    global $db_conn;

    $query = "SELECT * FROM Disaster WHERE 1=1";
    $params = [];

    $filters = [
        "name" => "disasterName",
        "disasterDate" => "disasterDate",
        "location" => "disasterLocation",
        "damageCost" => "damageCost",
        "casualties" => "casualties",
        "severityLevel" => "severityLevel",
        "type" => "type"
    ];

    foreach ($filters as $column => $param) {
        if (!empty($_GET[$param])) {
            $query .= " AND LOWER($column) LIKE LOWER(:$param)";
            $params[$param] = "%" . $_GET[$param] . "%";
        }
    }

    $result = oci_parse($db_conn, $query);
    foreach ($params as $param => $value) {
        oci_bind_by_name($result, ":$param", $params[$param]);
    }
    echo $query;

    oci_execute($result);

    // if (!empty($_GET["disasterName"])) {
    //     $query .= " AND disasterName LIKE :disasterName";
    //     $params[":disasterName"] = "%" . $_GET["disasterName"] . "%";
    // }

    // // $result = executePlainSQL("SELECT * FROM Disaster");
    // $result = $db_conn->prepare($query);
    // $result->execute($params);
    printResult($result);
}

// TODO: modify the SELECT clause to include user-input params,
// if user hasn't input any params, then select all (*)
function handleMissionDisplayRequest()
{
    // TODO: mission primary key is a the missionId, but user query doesn't
    // include missionId. How to find the correct missions
    global $db_conn;
    $result = executePlainSQL("SELECT * FROM Mission");
    printResult($result);
}

function printResult($result)
{
    echo "<br>Retrieved data from table Disaster:<br>";
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

function handleDisasterReliefProgressDisplayRequest()
{
    //TODO
}

function handleInsertRequest()
{
    global $db_conn;

    //Getting the values from user and insert data into the table
    // $tuple = array(
    //     ":bind1" => $_POST['insNo'],
    //     ":bind2" => $_POST['insName']
    // );

    // $alltuples = array(
    //     $tuple
    // );

    // executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
    // oci_commit($db_conn);
}

function handleUpdateRequest()
{
    global $db_conn;

    // $old_name = $_POST['oldName'];
    // $new_name = $_POST['newName'];

    // you need the wrap the old name and new name values with single quotations
    // executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
    // oci_commit($db_conn);
}

// HANDLE ALL ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.

// need 3 queries for the frontend: query all disasters, query filtered disasters (select)
// using user-passed in arguments, 
// 2. view disaster relief progress, aggregation query
// 3. Calculate the average damageCost of disasters across all disasters (aggregation with group by) 
function handleRequest()
{
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        } else if (array_key_exists('countTuples', $_GET)) {
            handleCountRequest();
        } else if (array_key_exists('displayDisasterTuplesRequest', $_GET)) {
            echo "hit";
            handleDisasterDisplayRequest();
        } else if (array_key_exists('displayMissionTuples', $_GET)) {
            handleMissionDisplayRequest();
        } else if (array_key_exists('displayDisasterReliefProgress')) {
            handleDisasterReliefProgressDisplayRequest();
        }

        disconnectFromDB();
    }
}
?>