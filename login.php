<?php

$login = false;
$showError = false;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '_dbconnect.php';

    // Sanitize inputs
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = $_POST["password"];

    // Prepare the SQL query
    $sql = "SELECT * FROM `users` WHERE `username`='$username';";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Process the result
    $count = mysqli_num_rows($result);
    if ($count == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $login = true;
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;

            header("Location: index.php");
            exit;
        } else {
            $showError = "Invalid Username/Password!";
        }
    } else {
        $showError = "Invalid Credentials!";
    }
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LogIn</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f8ff;
      font-family: Arial, sans-serif;
    }

    .login-container {
      margin-top: 100px;
      background-color: #ffffff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }

    h2 {
      color: #28a745;
      text-align: center;
      font-weight: bold;
    }

    .btn-success {
      background-color: #28a745;
      border: none;
      width: 100%;
    }

    .btn-success:hover {
      background-color: #218838;
    }

    .form-label {
      color: #495057;
      font-weight: bold;
    }

    .text-success a {
      text-decoration: none;
      font-weight: bold;
    }

    .text-success a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <?php require '_nav.php'; ?>

  <?php
  if ($showError) {
    echo '<div class="alert alert-danger text-center" role="alert">
            <strong>ERROR:</strong> ' . $showError . '
          </div>';
  }
  ?>

  <div class="container d-flex justify-content-center">
    <div class="login-container col-md-6">
      <h2 class="mb-4">LogIn</h2>
      <form action="/mini/login.php" method="post">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <div class="mb-3">
          <p class="text-success text-center">Don't have an account? <a href="signup.php">SignUp</a></p>
        </div>
        <div class="d-flex justify-content-center">
          <button type="submit" class="btn btn-success">LogIn</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
