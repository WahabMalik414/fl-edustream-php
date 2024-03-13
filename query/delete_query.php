<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}

// Include config file
require_once "../config.php";

// Check if query ID exists
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Prepare a select statement to delete the query
    $sql = "DELETE FROM QueryData WHERE QueryID = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind query ID parameter
        mysqli_stmt_bind_param($stmt, "i", $param_query_id);

        // Set parameter
        $param_query_id = trim($_GET["id"]);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Check if any rows were affected
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                // Query deleted successfully
                header("location: index.php");
                exit();
            } else {
                // No matching query found or unauthorized access
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
} else {
    // Query ID parameter is missing, redirect to error page
    header("location: error.php");
    exit();
}
?>
