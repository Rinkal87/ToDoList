<?php
session_start();

if ((!isset($_SESSION['loggedin'])) || $_SESSION['loggedin'] != true) {
  header("location: login.php");
  exit;
}

include '_dbconnect.php';

$showerror = false;
$isadded = false;
$isdel = false;
$iscompleted = false;

// Create the `user_data` table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS user_data (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  userdata TEXT NOT NULL,
  is_completed BOOLEAN DEFAULT FALSE,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $createTableSQL)) {
  die("Error creating table: " . mysqli_error($conn));
}

// Add task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
  $username = $_SESSION["username"];
  $userdata = $_POST["userdata"];

  if ($userdata == "") {
    $showerror = "Enter something before adding!";
  } else {
    $sql = "INSERT INTO `user_data` (`username`, `userdata`, `timestamp`) VALUES ('$username', '$userdata', current_timestamp());";
    $result = mysqli_query($conn, $sql);

    if ($result) {
      $isadded = true;
    } else {
      $showerror = "Something went wrong! Try Again.";
    }
  }
}

// Delete all tasks
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset"])) {
  $username = $_SESSION["username"];
  $sql = "DELETE FROM `user_data` WHERE `username`='$username'";
  $result = mysqli_query($conn, $sql);

  if ($result) {
    $isdel = true;
  }
}

// Mark task as complete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_complete"])) {
  $task_id = $_POST["task_id"];
  $sql = "UPDATE `user_data` SET `is_completed`=TRUE WHERE `id`='$task_id'";
  $result = mysqli_query($conn, $sql);

  if ($result) {
    $iscompleted = true;
  } else {
    $showerror = "Failed to mark task as complete!";
  }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ToDo List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .completed-task {
      text-decoration: line-through;
      color: gray;
    }
  </style>
</head>

<body>
  <?php require '_nav.php'; ?>

  <center>
    <h1 style="margin-top: 10px;">Welcome! <?php echo $_SESSION['username']; ?></h1>
  </center>

  <form action="index.php" method="post">
    <div class="input-group mb-3 mt-3 w-50 container">
      <input type="text" class="form-control" placeholder="Enter Your To Do List" style="border: 2px solid green" name="userdata">
      <input class="btn btn-success" type="submit" value="Add to To Do List" name="submit">
    </div>
  </form>

  <?php
  if ($showerror) {
    echo '<div class="alert alert-danger" role="alert">' . $showerror . '</div>';
  }
  if ($isadded) {
    echo '<div class="alert alert-success" role="alert">Task added successfully!</div>';
  }
  if ($isdel) {
    echo '<div class="alert alert-success" role="alert">All tasks deleted successfully!</div>';
  }
  if ($iscompleted) {
    echo '<div class="alert alert-success" role="alert">Task marked as complete!</div>';
  }
  ?>

  <div class="card container w-100 mb-1 mt-5">
    <div class="card-header">
      <strong>Your To Do List</strong>
    </div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <?php
        $username = $_SESSION["username"];
        $datafatchsql = "SELECT * FROM `user_data` WHERE `username`='$username'";
        $dataresult = mysqli_query($conn, $datafatchsql);
        $count = mysqli_num_rows($dataresult);

        if ($count >= 1) {
          while ($row = mysqli_fetch_assoc($dataresult)) {
            $task_id = $row['id'];
            $task_data = $row['userdata'];
            $is_completed = $row['is_completed'];

            $task_class = $is_completed ? 'completed-task' : '';
            echo "
              <li class='list-group-item'>
                <span class='$task_class'>$task_data</span>
                " . (!$is_completed ? "
                <form action='index.php' method='post' class='d-inline'>
                  <input type='hidden' name='task_id' value='$task_id'>
                  <button type='submit' name='mark_complete' class='btn btn-sm btn-outline-success'>Mark as Complete</button>
                </form>" : "") . "
              </li>";
          }
        } else {
          echo '<li class="list-group-item">Your To Do List is Empty!</li>';
        }
        ?>
      </ul>
    </div>
  </div>

  <form action="index.php" method="post">
    <div class="input-group mb-3 mt-5 w-50 container d-flex justify-content-center">
      <input class="btn btn-danger" type="submit" value="Delete Your To Do List" name="reset">
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
