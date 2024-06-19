<?php

session_start();

/*
//if the user is already looged in 
if (isset($_SESSION['student_id'])) {
    header("Location: profile.php");
    exit;
}
*/

include("connection.php");
$conn = new mysqli($servername, $username, $database_password, $database_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected Successfully";



if (isset($_POST["login"])) {
    $student_email = $_POST['student_email'];
    $password = $_POST['password'];

    $sql = "SELECT up.student_id, up.password
    FROM users_passwords up
    INNER JOIN users_info ui ON up.student_ID = ui.student_ID
    WHERE ui.student_email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            $_SESSION['student_id'] = $row['student_id'];
            //$_SESSION['role'] = $row['account_type'] == 0 ? 'admin' : 'user';
            header("Location: index.php");
            exit;
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Invalid email or password.";
    }

    $conn->close();
}

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
      <p>Social media for SYSC students at Carleton University</p>
   </header>
   <main>
   <form id ="LoginForm" action="" method="POST">
         <section>
            <h2>Profile Information</h2>
            <table>
               <tr>
                <td>
                  <label for="email">Email Address:</label>
                  <input type="email" id="student_email" name="student_email">
                </td>
               </tr>
               <tr>
                  <td>
                     <label for="password">Password:</label>
                     <input type="password" id="password" name="password" required>
                  </td>
                  </tr>
            </table>
         </section>
         <button type="submit" name = login>Login</button>
      </form>
      <p>Don't have an account? <a href="register.php">Register</a></p>
   </main>

</body>


</html>