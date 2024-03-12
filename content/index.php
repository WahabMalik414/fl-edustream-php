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

// Retrieve content uploaded by the logged-in educator
$sql = "SELECT * FROM Content WHERE EducatorID = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $educatorId);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}

// Close statement
mysqli_stmt_close($stmt);

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Content</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Content Uploaded by You</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>File path</th>
                    <th>Upload date</th>
                    <!-- Add more columns as needed -->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row["Title"]; ?></td>
                        <td><?php echo $row["Type"]; ?></td>
                        <td><?php echo $row["Description"]; ?></td>
                        <td><?php echo $row["FilePath"]; ?></td>
                        <td><?php echo $row["UploadDate"]; ?></td>

                        <td style="display: flex; justify-content: flex-end;gap: 10px">
                            <a href="" type="button" class="btn btn-secondary">Edit</a>
                            <a href="" type="button" class="btn btn-secondary">Details</a>
                            <a href="" type="button" class="btn btn-success">Analytics</a>
                            <a href="delete_content.php?id=<?php echo $row["ContentID"]; ?>" class="btn btn-danger">Delete</a>
                        </td>

                        <!-- Add more columns as needed -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>