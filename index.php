<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      font: 14px sans-serif;
      text-align: center;
    }
  </style>
</head>

<body>
  <nav class="display-4 navbar navbar-expand-lg navbar-light bg-light">
    <div class="collapse navbar-collapse " style="gap:10px; justify-content: center;" id="navbarNavAltMarkup">
      <div class="navbar-nav" style="gap:20px;">
        <a class="nav-item nav-link active" href="#">Home <span class="sr-only">(current)</span></a>
        <a class="nav-item nav-link" href="./content/">Content</a>
        <a class="nav-item nav-link" href="#">Feedback</a>
        <a class="nav-item nav-link" href="#">Analytics</a>
        <a class="nav-item nav-link" href="./query/">Query</a>
        <a class="nav-item nav-link" href="#">Profile</a>

      </div>
    </div>
  </nav>
  <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to Educator module.</h1>
  <p>
    <a href="profile-management.php" class="btn btn-success">Profile management</a>

    <a href="reset-password.php" class="btn btn-warning ml-3">Reset Your Password</a>
    <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>

  </p>
</body>

</html>