<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include config file
require_once "../config.php";

// Check if content ID is provided in the URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Get the content ID from the URL
    $contentId = trim($_GET["id"]);

    // Retrieve content details for the specified content ID
    $sql = "SELECT * FROM Content WHERE ContentID = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $contentId);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            // Check if content exists with the provided ID
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result); // Fetch content details
            } else {
                // Redirect to error page if content does not exist
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    mysqli_stmt_close($stmt);
} else {
    // Redirect to error page if content ID is not provided in the URL
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
    <title>Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Details</h1>
        <div>
            <h4>Content</h4>
            <hr />
            <dl class="row">
                <dt class="col-sm-2">Title</dt>
                <dd class="col-sm-10"><?php echo $row['Title']; ?></dd>
                <dt class="col-sm-2">File Path</dt>
                <dd class="col-sm-10"><?php echo $row['FilePath']; ?></dd>
                <dt class="col-sm-2">Type</dt>
                <dd class="col-sm-10"><?php echo $row['Type']; ?></dd>
                <dt class="col-sm-2">Upload Date</dt>
                <dd class="col-sm-10"><?php echo $row['UploadDate']; ?></dd>
            </dl>
            <div>
                <a href="./edit_content.php?id=<?php echo $row['ContentID']; ?>" class="btn btn-secondary">Edit</a>
                <a href="./index.php" class="btn btn-outline-danger">Back to List</a>
            </div>
        </div>
    </div>
</body>

</html>