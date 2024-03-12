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

// Get logged-in user's ID
$educatorId = $_SESSION["id"];

// Define variables and initialize with empty values
$description = "";
$description_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Check input errors before inserting into database
    if (empty($description_err)) {
        // File upload directory
        $targetDir = "uploads/";
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowTypes = array('pdf', 'doc', 'docx', 'txt');

        if (in_array($fileType, $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                // Insert file details into database
                $sql = "INSERT INTO Content (EducatorID, Title, Description, Type, FilePath, UploadDate) VALUES (?, ?, ?, ?, ?, NOW())";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "issss", $educatorId, $fileName, $description, $fileType, $targetFilePath);
                    if (mysqli_stmt_execute($stmt)) {
                        header("location: index.php");
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Sorry, only PDF, DOC, DOCX, & TXT files are allowed.";
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upload Content</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Upload Content</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
                <span class="help-block"><?php echo $description_err; ?></span>
            </div>
            <div class="form-group">
                <label>Choose File</label>
                <input type="file" name="file" class="form-control-file">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Upload">
            </div>
        </form>
    </div>
</body>

</html>