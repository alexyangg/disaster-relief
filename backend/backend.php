<?php
include("connection.php");
include("db_utils.php");

function handleResetRequest()
{
    global $db_conn;
    echo "<br> creating new disaster relief tables...<br>";

    $sqlContent = file_get_contents('disasterrelief.sql');
    foreach (explode(';', $sqlContent) as $sqlCommand) {
        $sqlCommand = trim($sqlCommand);
        if (empty($sqlCommand)) {
            continue;
        }

        executePlainSQL($sqlCommand, $db_conn);
    }
    oci_commit($db_conn);
}

function handleCountRequest()
{
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM Disaster");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in Disaster: " . $row[0] . "<br>";
    }
}

function handleDisplayRequest()
{
    global $db_conn;
    $result = executePlainSQL("SELECT * FROM Disaster");
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
        } else if (array_key_exists('displayTuples', $_GET)) {
            handleDisplayRequest();
        }

        disconnectFromDB();
    }
}
?>