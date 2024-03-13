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

// Check if content ID exists
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Prepare a select statement
    $sql = "SELECT FilePath FROM Content WHERE ContentID = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_content_id);

        // Set parameters
        $param_content_id = trim($_GET["id"]);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            // Check if content exists
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $filePath);
                if (mysqli_stmt_fetch($stmt)) {
                    // Close statement
                    mysqli_stmt_close($stmt);

                    // Prepare a delete statement
                    $sql = "DELETE FROM Content WHERE ContentID = ?";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "i", $param_content_id);

                        // Set parameters
                        $param_content_id = trim($_GET["id"]);

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                            // Close statement
                            mysqli_stmt_close($stmt);

                            // Delete the file from the server
                            if (unlink($filePath)) {
                                // File deleted successfully
                                header("location: index.php");
                                exit();
                            } else {
                                echo "Error: Unable to delete the file.";
                            }
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }
                }
            } else {
                // Content ID doesn't exist, redirect to error page
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
    // Content ID parameter is missing, redirect to error page
    header("location: error.php");
    exit();
}
