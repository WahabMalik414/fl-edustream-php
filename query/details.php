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

// Check if query ID is provided in the URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Get the query ID from the URL
    $queryId = trim($_GET["id"]);

    // Retrieve query details for the specified query ID
    $sql = "SELECT Question, Answer, Date FROM QueryData WHERE QueryID = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $queryId);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            // Check if query exists with the provided ID
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result); // Fetch query details
            } else {
                // Redirect to error page if query does not exist
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    mysqli_stmt_close($stmt);
} else {
    // Redirect to error page if query ID is not provided in the URL
    header("location: error.php");
    exit();
}

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Query Details</h1>
        <div>
            <h4>Query</h4>
            <hr />
            <dl class="row">
                <dt class="col-sm-2">Question</dt>
                <dd class="col-sm-10"><?php echo $row['Question']; ?></dd>
                <dt class="col-sm-2">Answer</dt>
                <dd class="col-sm-10"><?php echo $row['Answer']; ?></dd>
                <dt class="col-sm-2">Date</dt>
                <dd class="col-sm-10"><?php echo $row['Date']; ?></dd>
            </dl>
            <div>
                <a href="./index.php" class="btn btn-outline-danger">Back to List</a>
            </div>
        </div>
    </div>
</body>

</html>
