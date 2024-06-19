<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();


// // Debugging output
// echo "Session ID: " . session_id() . "<br>";
// echo "Role: " . $_SESSION['role'] . "<br>";

if (!isset($_SESSION['student_id']) || $_SESSION['account_type'] !== "0") {
    echo "Permission denied. <br>";
    echo "<a href='index.php'>Go to Home</a>";
    exit;
}


include("connection.php");
$conn = new mysqli($servername, $username, $database_password, $database_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected Successfully";

$sql = "SELECT u.student_id, u.first_name, u.last_name, u.student_email, p.Program, up.account_type
FROM users_info u
INNER JOIN users_program p ON u.student_id = p.student_id
INNER JOIN users_permissions up ON u.student_id = up.student_id";

$stmt = $conn->prepare($sql); 
$stmt->execute(); 
$result = $stmt->get_result();

//this is for the information section 
if (isset($_SESSION['student_id'])) {
    $stmt = $conn->prepare("
     SELECT u.first_name, u.last_name, u.student_email, up.program, ua.avatar
     FROM users_info u
     INNER JOIN users_program up ON u.student_id = up.student_id
     INNER JOIN users_avatar ua ON u.student_id = ua.student_id
     WHERE u.student_id = ?");
    $stmt->bind_param("i", $_SESSION['student_id']);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $student_email, $program, $avatar);
    $stmt->fetch(); // Fetch the result
 
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Users List</title>
   <link rel="stylesheet" href="assets/css/reset.css">
   <link rel="stylesheet" href="assets/css/style.css">
   <script src="assets/js/javascripts.js"></script>

</head>

<body>
   <header>
      <h1>SYSCX</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>
   <nav>
      <div class="sidebar">
         <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <?php 
                if($_SESSION['account_type'] == "0"){
                  echo '<li><a href="user_list.php">User List</a></li>';
                }
                ?>
               <li><a href="logout.php">Logout</a></li>
         </ul>
      </div>
   </nav>

   <main>
        <section>
            <h2>User List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Program</th>
                        <th>account_type"</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $row['student_id'] ?></td>
                            <td><?= $row['first_name'] ?></td>
                            <td><?= $row['last_name'] ?></td>
                            <td><?= $row['student_email'] ?></td>
                            <td><?= $row['Program'] ?></td>
                            <td><?=$row['account_type'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
    <section id="informationsection">
      <div class="information">
      <?php if (isset($_SESSION['student_id'])): ?>
         <p><?php echo $first_name; ?></p>
         <p><?php echo $last_name; ?></p>
         <?php if ($avatar == 1): ?>
            <img src="images/img_avatar1.png" class="avatar">
      <?php elseif ($avatar == 2): ?>
         <img src="images/img_avatar2.png" class="avatar">
      <?php elseif ($avatar == 3): ?>
         <img src="images/img_avatar3.png" class="avatar">
      <?php elseif ($avatar == 4): ?>
         <img src="images/img_avatar4.png" class="avatar">
      <?php elseif ($avatar == 5): ?>
         <img src="images/img_avatar5.png" class="avatar">
      <?php endif; ?>
         <p>Email: <?php echo $student_email; ?></p>
         <p>Program: <?php echo $program; ?></p>
      <?php endif; ?>
      </div>
   </section>
   
</body>

</html>