<?php

$showAlert = false;
$showError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	include '_dbconnect.php';
	$createTableSQL = "CREATE TABLE IF NOT EXISTS users (
        sno INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
	if (!mysqli_query($conn, $createTableSQL)) {
        die("Error creating table: " . mysqli_error($conn));
    }
	$username = $_POST["username"];
	$password = $_POST["password"];
	$conf_password = $_POST["conf_password"];

	$userExistSQL = "SELECT * FROM `users` WHERE `username`='$username'";
	$result = mysqli_query($conn, $userExistSQL);
	if (!$result) {
		// Query execution failed, show an error message or log the error
		echo "Error: " . mysqli_error($conn);
	} else {
		// Query executed successfully, proceed with checking the number of rows
		$numOfRows = mysqli_num_rows($result);
	
	if ($numOfRows > 0) {
		$showError = "Username Already Exist!";
	}
	else {
		if ($password == $conf_password) {

			$passwordHash = password_hash($password, PASSWORD_DEFAULT);

			$sql = "INSERT INTO users (username, password, timestamp) VALUES ('$username', '$passwordHash', current_timestamp());";
			if ($conn->query($sql) === TRUE) {
				$showAlert = true;
			} else {
				$showError = "Error inserting record: " . $conn->error;
			}
		} else {
			$showError = "Passwords do not match!";
		}
	}
	}	
}
?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SignUp</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
	<style>
		body {
			background-color: #f8f9fa;
		}
		.container {
			background-color: white;
			border-radius: 10px;
			box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
			padding: 30px;
			max-width: 500px;
			margin: auto;
			margin-top: 50px;
		}
		.form-control {
			border: 2px solid #198754;
			border-radius: 5px;
		}
		.form-control:focus {
			border-color: #0f5132;
			box-shadow: 0 0 8px rgba(31, 142, 91, 0.4);
		}
		.btn-success {
			background-color: #198754;
			border: none;
		}
		.btn-success:hover {
			background-color: #145c41;
		}
		h2 {
			color: #198754;
		}
	</style>
</head>

<body>
	<?php
	require '_nav.php';
	?>

	<div class="container mb-3">
		<h2 class="mb-3 text-center">Sign Up</h2>

		<?php
		if ($showAlert) {
			echo '<div class="alert alert-success" role="alert">
			<strong>SUCCESS!</strong> Your account has been created successfully!
			</div>';
		}
		if ($showError) {
			echo '<div class="alert alert-danger" role="alert">
			<strong>ERROR:</strong> ' . $showError . '
			</div>';
		}
		?>

		<form action="signup.php" method="post">
			<div class="mb-3">
				<label for="username" class="form-label">Username</label>
				<input type="text" class="form-control" id="username" name="username" required>
			</div>
			<div class="mb-3">
				<label for="password" class="form-label">Password</label>
				<input type="password" class="form-control" id="password" name="password" required>
			</div>
			<div class="mb-3">
				<label for="conf_password" class="form-label">Confirm Password</label>
				<input type="password" class="form-control" id="conf_password" name="conf_password" required>
			</div>
			<div class="text-center">
				<p>Already have an account? <a href="login.php" class="text-success">Log In</a></p>
			</div>
			<div class="d-flex justify-content-center">
				<button type="submit" class="btn btn-success">Sign Up</button>
			</div>
		</form>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>
