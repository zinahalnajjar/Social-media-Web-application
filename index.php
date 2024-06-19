<?php
session_start();
//Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
   header("Location: login.php");
   exit();
}

include("connection.php");
$conn = new mysqli($servername, $username, $database_password, $database_name);

if(!isset($_SESSION["account_type"])){
   try{
      $sql = "SELECT * FROM users_permissions WHERE student_id = '".$_SESSION['student_id']."';";
      $results =$conn->query($sql)->fetch_assoc();
      $_SESSION["account_type"] = $results["account_type"];
   }catch(mysqli_sql_exception $e){
      $error = $e->getMessage();
      echo $error;
   }
}

$posts = array(); // Initialize an empty array to store posts

if (isset($_POST["submit"])) {
   if (isset($_SESSION['student_id'])) {
       // Get the student ID of the currently logged-in user from the session
       $student_id = $_SESSION['student_id'];
       
       // Insert the new post using the retrieved student_id
       $new_post = $_POST['new_post'];
       $post_date = date('Y-m-d H:i:s');

       $stmt = $conn->prepare("INSERT INTO users_posts (student_id, new_post, post_date) VALUES (?, ?, ?)");
       $stmt->bind_param("iss", $student_id, $new_post, $post_date);
       if ($stmt->execute()) {
           echo "New record created successfully";
       } else {
           echo "Error: " . $stmt->error;
       }
   } else {
       echo "Error: Student ID not found in session";
   }
}

// displaying the posts 
$sql_fetch_posts = "SELECT * FROM users_posts WHERE student_id = ? ORDER BY post_date DESC LIMIT 10";
$stmt = $conn->prepare($sql_fetch_posts);
$stmt->bind_param("i", $_SESSION['student_id']); 
$stmt->execute(); 
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row; 
    }
} else {
    echo "No posts yet";
}


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


// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Register on SYSCX</title>
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
      <form method="POST" action="">

         <table>
            <tr>
               <th colspan="2" id="postHeader">NEW POST</th>
            </tr>
            <tr>
               <td colspan="2">
                  <textarea id="postContent" name="new_post" rows="4" cols="50"></textarea>
               </td>
            </tr>
            <tr class="button-container">
               <td>
                  <input type="submit" name = "submit" value="Post">
                  <input type="reset" name = "reset" value="Reset">
               </td>
            </tr>
         </table>
      </form>

      <section id="postedSection">
         <table>
            <?php
            foreach ($posts as $post){
            echo "<tr>";
            echo  "<td>";
            echo '<details>';
            echo '<summary>' . $post['new_post'] . '</summary>';
            echo '<p>Date: ' . $post['post_date'] . '</p>';
            echo '</details>';
            echo "</td>";
            echo "</tr>";
            }
            ?>
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

