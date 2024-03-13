<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$name = $email = $bio = $specialization = $address = $phoneNumber = "";
$name_err = $email_err = $bio_err = $specialization_err = $address_err = $phoneNumber_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate bio
    $bio = trim($_POST["bio"]);

    // Validate specialization
    $specialization = trim($_POST["specialization"]);

    // Validate address
    $address = trim($_POST["address"]);

    // Validate phone number
    $phoneNumber = trim($_POST["phoneNumber"]);

    // Check input errors before updating the database
    if (empty($name_err) && empty($email_err)) {
        // Prepare an update statement
        $sql = "UPDATE Educator SET Name = ?, Email = ?, Bio = ?, Specialization = ?, Address = ?, PhoneNumber = ? WHERE EducatorID = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssi", $param_name, $param_email, $param_bio, $param_specialization, $param_address, $param_phoneNumber, $param_educator_id);

            // Set parameters
            $param_name = $name;
            $param_email = $email;
            $param_bio = $bio;
            $param_specialization = $specialization;
            $param_address = $address;
            $param_phoneNumber = $phoneNumber;
            $param_educator_id = $_SESSION["id"]; // Assuming EducatorID is stored in session upon login

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Educator profile updated successfully, redirect to profile page
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
    // Retrieve educator data from the database
    $sql = "SELECT Name, Email, Bio, Specialization, Address, PhoneNumber FROM Educator WHERE EducatorID = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_educator_id);

        // Set parameters
        $param_educator_id = $_SESSION["id"]; // Assuming EducatorID is stored in session upon login

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            // Check if educator exists, if yes then retrieve and set values
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $name, $email, $bio, $specialization, $address, $phoneNumber);
                mysqli_stmt_fetch($stmt);
            } else {
                // Educator not found, redirect to error page
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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                <span class="text-danger"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="text-danger"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" class="form-control"><?php echo $bio; ?></textarea>
            </div>
            <div class="form-group">
                <label>Specialization</label>
                <input type="text" name="specialization" class="form-control" value="<?php echo $specialization; ?>">
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" value="<?php echo $address; ?>">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phoneNumber" class="form-control" value="<?php echo $phoneNumber; ?>">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>
