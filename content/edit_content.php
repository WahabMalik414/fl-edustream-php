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

// Define variables and initialize with empty values
$title = $description = $Type = $FilePath = "";
$title_err = $description_err = $Type_err = $FilePath_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate type
    if (empty(trim($_POST["type"]))) {
        $Type_err = "Please enter a type.";
    } else {
        $Type = trim($_POST["type"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate file path
    if (empty(trim($_POST["FilePath"]))) {
        $FilePath_err = "Please enter a file path.";
    } else {
        $FilePath = trim($_POST["FilePath"]);
    }

    // Check input errors before updating the database
    if (empty($title_err) && empty($description_err) && empty($Type_err) && empty($FilePath_err)) {
        // Prepare an update statement
        $sql = "UPDATE Content SET Title = ?, Description = ?, Type = ?, FilePath = ? WHERE ContentID = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssi", $param_title, $param_description, $param_Type, $param_FilePath, $param_content_id);

            // Set parameters
            $param_title = $title;
            $param_description = $description;
            $param_Type = $Type;
            $param_FilePath = $FilePath;

            $param_content_id = $_POST["content_id"];

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Content updated successfully, redirect to index page
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
} else {
    // Check existence of content ID parameter before processing further
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Prepare a select statement
        $sql = "SELECT * FROM Content WHERE ContentID = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_content_id);

            // Set parameters
            $param_content_id = trim($_GET["id"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    // Fetch result row as an associative array
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    // Retrieve individual field value
                    $title = $row["Title"];
                    $description = $row["Description"];
                    $Type = $row["Type"];
                    $FilePath = $row["FilePath"];
                } else {
                    // Content ID not found, redirect to error page
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);

        // Close connection
        mysqli_close($link);
    } else {
        // Content ID parameter is missing, redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Content</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Edit Content</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
                <span class="text-danger"><?php echo $title_err; ?></span>
            </div>
            <div class="form-group">
                <label>Type</label>
                <input type="text" name="type" class="form-control" value="<?php echo $Type; ?>">
                <span class="text-danger"><?php echo $Type_err; ?></span>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
                <span class="text-danger"><?php echo $description_err; ?></span>
            </div>
            <div class="form-group">
                <label>File path</label>
                <input type="text" name="FilePath" class="form-control" value="<?php echo $FilePath; ?>">
                <span class="text-danger"><?php echo $FilePath_err; ?></span>
            </div>
            <input type="hidden" name="content_id" value="<?php echo $_GET["id"]; ?>">
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>