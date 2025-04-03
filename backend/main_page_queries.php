<?php
include("connection.php");
include("db_utils.php");

// TODO: modify the SELECT clause to include user-input params,
// if user hasn't input any params, then select all (*)
// SELECTION query
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
            if (in_array($column, ['damageCost', 'casualties', 'severityLevel'])) {
                // use exact match for numbers
                $query .= " AND $column = :$param";
                $params[$param] = $_GET[$param];
            } elseif ($column == 'disasterDate') {
                $query .= " AND TO_CHAR($column, 'YYYY-MM-DD') = :$param";
                $params[$param] = $_GET[$param];
            } else {
                // use LIKE for text fields
                $query .= " AND LOWER($column) LIKE LOWER(:$param)";
                $params[$param] = "%" . $_GET[$param] . "%";
            }
        }
    }

    $result = oci_parse($db_conn, $query);
    foreach ($params as $param => $value) {
        oci_bind_by_name($result, ":$param", $params[$param]);
    }

    oci_execute($result);
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
    global $db_conn;
    // should probably be 2 subqueries
    // one for sum supplies and one for sum donations

    // TODO: tuples used in the query cannot be predetermined
    // can use user input?
    $query = "SELECT 
        m.disasterName,
        m.disasterLocation,
        m.disasterDate,
        m.helpNeeded,
        m.priority,
        SUM(s.quantity) AS totalSupplies,
        SUM(d.donationAmount) AS totalDonations
    FROM 
        Mission m
    JOIN 
        ReliefCenter rc ON m.rcName = rc.name AND m.rcLocation = rc.location
    LEFT JOIN 
        Supplies s ON rc.name = s.rcName AND rc.location = s.rcLocation
    LEFT JOIN 
        Donation d ON rc.name = d.rcName AND rc.location = d.rcLocation
    GROUP BY 
        m.disasterName,
        m.disasterLocation,
        m.disasterDate,
        m.helpNeeded,
        m.priority
    ORDER BY 
        m.priority DESC";

    $result = oci_parse($db_conn, $query);

    oci_execute($result);

    echo "<table border='1'>
        <tr>
            <th>Disaster Name</th>
            <th>Location</th>
            <th>Date</th>
            <th>Help Needed</th>
            <th>Priority</th>
            <th>Total Supplies</th>
            <th>Total Donations</th>
        </tr>";

    while ($row = oci_fetch_array($result, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo "<tr>";
        echo "<td>" . $row['DISASTERNAME'] . "</td>";
        echo "<td>" . $row['DISASTERLOCATION'] . "</td>";
        echo "<td>" . $row['DISASTERDATE'] . "</td>";
        echo "<td>" . $row['HELPNEEDED'] . "</td>";
        echo "<td>" . $row['PRIORITY'] . "</td>";
        echo "<td>" . $row['TOTALSUPPLIES'] ?? 0 . "</td>";
        echo "<td>" . $row['TOTALDONATIONS'] ?? 0 . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // oci_free_statement($result);  

}

// aggregation with HAVING
function handleReliefCenterDonationDisplayRequest() 
{
    global $db_conn;

    $amount = isset($_GET['donationAmount']) && is_numeric($_GET['donationAmount']) ? $_GET['donationAmount'] : PHP_INT_MAX;
    $query = "SELECT rcName, rcLocation, SUM(donationAmount) as total_donations
              FROM Donation
              GROUP BY rcName, rcLocation
              HAVING SUM(donationAmount) < :amount";

    $result = oci_parse($db_conn, $query);
    oci_bind_by_name($result, ":amount", $amount);
    oci_execute($result);

    echo "<table border='1'><tr><th>Relief Center Name</th><th>Location</th><th>Total Donations</th></tr>";

    while ($row = oci_fetch_assoc($result)) {
        echo "<tr><td>" . $row["RCNAME"] . "</td>";
        echo "<td>" . $row["RCLOCATION"] . "</td>";
        echo "<td>" . $row["TOTAL_DONATIONS"] . "</td></tr>";
    }

    echo "</table>";
}

function displayDisasterTuples() 
{
    global $db_conn;

    // $query = "SELECT * FROM Disaster WHERE 1=1";
    // $params = [];

    // $filters = [
    //     "name" => "disasterName",
    //     "disasterDate" => "disasterDate",
    //     "location" => "disasterLocation",
    //     "damageCost" => "damageCost",
    //     "casualties" => "casualties",
    //     "severityLevel" => "severityLevel",
    //     "type" => "type"
    // ];

    // // might need to split up the queries because of date etc.
    // foreach ($filters as $column => $param) {
    //     if (!empty($_GET[$param])) {
    //         $query .= " AND LOWER($column) LIKE LOWER(:$param)";
    //         $params[$param] = "%" . $_GET[$param] . "%";
    //     }
    // }

    // $result = oci_parse($db_conn, $query);
    // foreach ($params as $param => $value) {
    //     oci_bind_by_name($result, ":$param", $params[$param]);
    // }
    // echo getTableString($result, array("DISASTERNAME", "DISASTERLOCATION", "DISASTERDATE", "DAMAGECOST", "CASUALTIES", "SEVERITYLEVEL", "TYPE"));

    // // oci_execute($result);

    $query = executePlainSQL("SELECT * FROM Disaster");
    echo getTableString($query, array("DISASTERNAME", "DISASTERLOCATION", "DISASTERDATE", "DAMAGECOST", "CASUALTIES", "SEVERITYLEVEL", "TYPE"));

}

function handleReliefCenterMissionDisplayRequest()
{
    global $db_conn;

    $missionType = strtolower($_GET['missionType']);
    $query = "SELECT DISTINCT rc.name, rc.location, m.missionType, m.priority, m.datePosted
              FROM Mission m
              JOIN ReliefCenter rc ON rc.name = m.rcName AND rc.location = m.rcLocation
              WHERE LOWER(m.missionType) LIKE :missionType";


    $result = oci_parse($db_conn, $query);
    oci_bind_by_name($result, ":missionType", $missionType);
    oci_execute($result);

    echo "<table border='1'><tr><th>Relief Center Name</th><th>Relief Center Location</th><th>Mission Type</th><th>Mission Priority</th><th>Date Posted</th></tr>";

    // echo "<table border='1'><tr><th>Relief Center Name</th><th>Location</th><th>Total Donations</th></tr>";

    while ($row = oci_fetch_assoc($result)) {
        echo "<tr><td>" . $row["NAME"] . "</td>";
        echo "<td>" . $row["LOCATION"] . "</td>";
        echo "<td>" . $row["MISSIONTYPE"] . "</td>";
        echo "<td>" . $row["PRIORITY"] . "</td>";
        echo "<td>" . $row["DATEPOSTED"] . "</td></tr>";
    }

    echo "</table>";

}

function displayReliefCenterDonations()
{
    global $db_conn;

    $amount = isset($_GET['donationAmount']) && is_numeric($_GET['donationAmount']) ? $_GET['donationAmount'] : PHP_INT_MAX;
    $query = executePlainSQL("SELECT rcName, rcLocation, SUM(donationAmount) as total_donations
              FROM Donation
              GROUP BY rcName, rcLocation
              HAVING SUM(donationAmount) < :amount");
    echo getTableString($query, array("RCNAME", "RCLOCATION", "TOTAL_DONATIONS"));

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
            handleDisasterDisplayRequest();
        } else if (array_key_exists('displayMissionTuples', $_GET)) {
            handleMissionDisplayRequest();
        } else if (array_key_exists('displayDisasterReliefProgressRequest', $_GET)) {
            handleDisasterReliefProgressDisplayRequest();
        } else if (array_key_exists('displayReliefCenterDonationRequest', $_GET)) {
            handleReliefCenterDonationDisplayRequest();
        } else if (array_key_exists('displayReliefCenterMissionRequest', $_GET)) {
            handleReliefCenterMissionDisplayRequest();
        }

        disconnectFromDB();
    }
}
?>