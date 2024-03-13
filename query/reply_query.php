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

// Define variables and initialize with empty values
$question = $reply = "";
$reply_err = "";

// Check if query ID is provided in the URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Get the query ID from the URL
    $queryId = trim($_GET["id"]);

    // Retrieve the question associated with the query ID
    $sql = "SELECT Question FROM QueryData WHERE QueryID = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $queryId);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            // Check if query exists with the provided ID
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result); // Fetch query details
                $question = $row["Question"];
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

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate reply
    if (empty(trim($_POST["reply"]))) {
        $reply_err = "Please enter a reply.";
    } else {
        $reply = trim($_POST["reply"]);
    }

    // Check input errors before inserting into database
    if (empty($reply_err)) {
        // Prepare an update statement
        $sql = "UPDATE QueryData SET Answer = ? WHERE QueryID = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_reply, $param_query_id);

            // Set parameters
            $param_reply = $reply;
            $param_query_id = $queryId;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to index page after reply is added
                header("location: index.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reply to Query</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Reply to Query</h2>
        <p>Question: <?php echo $question; ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $queryId; ?>" method="post">
            <div class="form-group">
                <label>Reply</label>
                <textarea class="form-control <?php echo (!empty($reply_err)) ? 'is-invalid' : ''; ?>" name="reply" rows="3"><?php echo $reply; ?></textarea>
                <span class="invalid-feedback"><?php echo $reply_err; ?></span>
            </div>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>
