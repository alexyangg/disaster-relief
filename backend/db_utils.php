<?php
function debugAlertMessage($message)
{
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr)
{ //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = oci_parse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = oci_execute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For oci_execute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function executeBoundSQL($cmdstr, $list)
{
    /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
    In this case you don't need to create the statement several times. Bound variables cause a statement to only be
    parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
    See the sample code below for how this function is used */

    global $db_conn, $success;
    $statement = oci_parse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            oci_bind_by_name($statement, $bind, $val);
            unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = oci_execute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}

function connectToDB()
{
    global $db_conn;
    global $config;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    
    $db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For oci_connect errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function getTableString($result, $show_rows=NULL, $limit=1000)
{
    $output = "<table class='sql-result-table'>";
    
    $first_row = true;
    $i = 0;
    while (($row = OCI_Fetch_Array($result, OCI_ASSOC)) && $i < $limit) {
        if ($first_row) {
            $output .= "<tr>";
            foreach ($row as $column_name => $value) {
                if ($show_rows == NULL || in_array($column_name, $show_rows)) {
                    $output .= "<th>" . htmlspecialchars($column_name) . "</th>";
                }
            }
            $output .= "</tr>";
            $first_row = false;
        }

        $output .= "<tr>";
        foreach ($row as $column_name => $value) {
            if ($show_rows == NULL || in_array($column_name, $show_rows)) {
                $output .= "<td>" . htmlspecialchars($value) . "</td>";
            }
        }
        $output .= "</tr>";
        $i++;
    }
    $output .= "</table>";

    return $output;
}

function executeSQLFile($path) {
    global $db_conn;
    
    $sqlContent = file_get_contents(__DIR__ . '/' . $path);
    if ($sqlContent === false) {
        echo "Error reading the SQL file." . error_get_last()['message'];
    }

    foreach (explode(';', $sqlContent) as $sqlCommand) {
        $sqlCommand = trim($sqlCommand);
        if (empty($sqlCommand)) {
            continue;
        }

        executePlainSQL($sqlCommand, $db_conn);
    }
    oci_commit($db_conn);
}

function disconnectFromDB()
{
    global $db_conn;

    if ($db_conn) {
        debugAlertMessage("Disconnecting from the Database");
        try {
            oci_close($db_conn);
            $db_conn = null;
        } catch (Exception $e) {
            debugAlertMessage("Error closing database connection: " . $e->getMessage());
        }
    } else {
        debugAlertMessage("Database connection is already closed or not initialized.");
    }

}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// WILL DO FOR NOW!!
function generateID() {
    return hexdec(substr(hash('sha256', time()), 0, 16));
}

function console_error($message) {
    echo "<script>console.error('". addslashes($message) ."');</script>";
}
?>