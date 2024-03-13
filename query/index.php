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

// Get logged-in user's ID
$educatorId = $_SESSION["id"];

// Retrieve queries related to the logged-in educator
$sql = "SELECT * FROM QueryData WHERE EducatorID = ?";
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
    <title>Queries</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <nav class="display-4 navbar navbar-expand-lg navbar-light bg-light">
        <div class="collapse navbar-collapse " style="gap:10px; justify-content: center;" id="navbarNavAltMarkup">
            <div class="navbar-nav" style="gap:20px;">
                <a class="nav-item nav-link active" href="../index.php">Home <span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link" href="content.php">Content</a>
                <a class="nav-item nav-link" href="#">Feedback</a>
                <a class="nav-item nav-link" href="#">Analytics</a>
                <a class="nav-item nav-link" href="#">Query</a>

            </div>
        </div>
    </nav>
    <div class="container">

        <h2 class="mt-5 text-center mb-5">Queries Received by <b><?php echo ($_SESSION["username"]); ?></b></h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Date</th>
                    <th>Links</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row["Question"]; ?></td>
                        <td><?php echo $row["Date"]; ?></td>
                        <td style="display: flex;gap: 10px">
                            <a href="details.php?id=<?php echo $row["QueryID"]; ?>" type="button" class="btn btn-secondary">Details</a>
                            <a href="delete_query.php?id=<?php echo $row["QueryID"]; ?>" type="button" class="btn btn-outline-danger">Delete</a>
                            <a href="reply_query.php?id=<?php echo $row["QueryID"]; ?>"type="button" class="btn btn-success">Reply</a>

                        </td>
                    </tr>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>