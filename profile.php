<?php
session_start();

//  if (!isset($_SESSION['student_id'])) {
//       header("Location: login.php");
//        exit;
//   }

// if (isset($_SESSION['account_type'])) {
//    $account_type = $_SESSION['account_type'];
// } else {
//    echo "Error: 'account_type' is not set in the session.";
// }

include("connection.php");
$conn = new mysqli($servername, $username, $database_password, $database_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected Successfully";

//if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (isset($_POST["Register"])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $DOB = $_POST['DOB'];
    $student_email = $_POST['student_email'];
    $program = $_POST['program'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
   

    // checking if the email address already exists
    $stmt_check_email = $conn->prepare("SELECT student_id FROM users_info WHERE student_email = ?");
    $stmt_check_email->bind_param("s", $student_email);
    $stmt_check_email->execute();
    $result_check_email = $stmt_check_email->get_result();
    $stmt_check_email->close();

    if ($result_check_email->num_rows > 0) {
      $error_message = "Email address already exists. Please register with a new email.";
      header("Location: register.php?error=" . urlencode($error_message));
      exit();

    }else{

      $stmt_info = $conn->prepare("INSERT INTO users_info (first_name, last_name, dob, student_email) VALUES (?, ?, ?, ?)");
      $stmt_info->bind_param("ssss", $first_name, $last_name, $DOB, $student_email);
      $stmt_info->execute();
      $student_id = $stmt_info->insert_id;
      $stmt_info->close();

      $account_type = 1; 
      $stmt_permissions = $conn->prepare("INSERT INTO users_permissions (student_ID, account_type) VALUES (?, ?)");
      $stmt_permissions->bind_param("ii", $student_id, $account_type);
      $stmt_permissions->execute();
      $stmt_permissions->close();

      //$sql_program = "INSERT INTO users_program (student_id, program) 
                        // VALUES ('$student_id', '$program')";
      $stmt_program = $conn->prepare("INSERT INTO users_program (student_id, program) VALUES (?, ?)");
      $stmt_program->bind_param("is", $student_id, $program);
      $stmt_program->execute();
      $stmt_program->close();

      // Insert a placeholder record into users_address table
      //$sql_address = "INSERT INTO users_address (student_id, street_number, street_name, city, province, postal_code)
               //VALUES ('$student_id', '0', '', '', '', '')";
      $stmt_address = $conn->prepare("INSERT INTO users_address (student_id, street_number, street_name, city, province, postal_code) VALUES (?, '0', '', '', '', '')");
      $stmt_address->bind_param("i", $student_id);
      $stmt_address->execute();
      $stmt_address->close();
               
               
      // Insert a placeholder record into users_avatar table
      //$sql_avatar = "INSERT INTO users_avatar (student_id, avatar)
                              //VALUES ('$student_id', '0')";
      $stmt_avatar = $conn->prepare("INSERT INTO users_avatar (student_id, avatar) VALUES (?, '0')");
      $stmt_avatar->bind_param("i", $student_id);
      $stmt_avatar->execute();
      $stmt_avatar->close();

      $stmt_password = $conn->prepare("INSERT INTO users_passwords (student_id, password) VALUES (?, ?)");
      $stmt_password->bind_param("is", $student_id, $hashed_password);
      $stmt_password->execute();
      $stmt_password->close();

      // Set session variable upon successful registration
      $_SESSION['student_id'] = $student_id;
        
      //header("Location: profile.php");
      //exit();

      
   }
    
} else if (isset($_POST["submit"])){
  
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $DOB = $_POST['DOB'];
    $student_email = $_POST['student_email'];
    $program = $_POST['program'];
    $street_number = $_POST['street_number'];
    $street_name = $_POST['street_name'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $postal_code = $_POST['postal_code'];
    $avatar = $_POST['avatar'];
   
    // Update the user's information in the database
    $conn = new mysqli($servername, $username, $password, $database_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update users_info table
    $sql_update_user_info = "UPDATE users_info SET first_name=?, last_name=?, dob=?, student_email=? WHERE student_id=?";
    $stmt = $conn->prepare($sql_update_user_info);
    $stmt->bind_param("ssssi", $first_name, $last_name, $DOB, $student_email, $_SESSION['student_id']);
    $stmt->execute();
    $stmt->close();

    // Update user_address table
    $sql_update_user_address = "UPDATE users_address SET street_number=?, street_name=?, city=?, province=?, postal_code=? WHERE student_id=?";
    $stmt = $conn->prepare($sql_update_user_address);
    $stmt->bind_param("sssssi", $street_number, $street_name, $city, $province, $postal_code, $_SESSION['student_id']);
    $stmt->execute();
    $stmt->close();

    // Update user_avatar table
    $sql_update_user_avatar = "UPDATE users_avatar SET avatar=? WHERE student_id=?";
    $stmt = $conn->prepare($sql_update_user_avatar);
    $stmt->bind_param("ii", $avatar, $_SESSION['student_id']);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    // Redirect user to profile page after updating
    header("Location: profile.php");
    exit();
   }
    
// If the form is not submitted, retrieve the user's information
$conn = new mysqli($servername, $username, $database_password, $database_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// $sql_select_user_id = "SELECT student_id FROM users_info ORDER BY student_id DESC LIMIT 1";
// $result = $conn->query($sql_select_user_id);

// if ($result->num_rows > 0) {
//     $row = $result->fetch_assoc();
//     $_SESSION['user_id'] = $row['student_id'];
// }

//$conn->close();

 if (!isset($_SESSION['student_id'])) {
      header("Location: login.php");
       exit;
  }

// Retrieve user's information for display

$first_name = $last_name = $DOB = $student_email = $program = "";
$street_number = $street_name = $city = $province = $postal_code = "";
$avatar = "";

if (isset($_SESSION['student_id']) && !empty($_SESSION['student_id'])) {
    $conn = new mysqli($servername, $username, $database_password, $database_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM users_permissions WHERE student_id = '".$_SESSION['student_id']."';";
    $results =$conn->query($sql)->fetch_assoc();
    $_SESSION["account_type"] = $results["account_type"];

    $sql_select = "SELECT u.first_name, u.last_name, u.dob, u.student_email, p.program,
                           a.street_number, a.street_name, a.city, a.province, a.postal_code,
                           up.avatar
                   FROM users_info u
                   INNER JOIN users_program p ON u.student_id = p.student_id
                   INNER JOIN users_address a ON u.student_id = a.student_id
                   INNER JOIN users_avatar up ON u.student_id = up.student_id
                   WHERE u.student_id = ?";
      

    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("i", $_SESSION['student_id']);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $DOB, $student_email, $program,
                       $street_number, $street_name, $city, $province, $postal_code,
                       $avatar);


    if ($stmt->fetch()) {
        $stmt->close();
        $conn->close();
    } else {
        $stmt->close();
        $conn->close();
        die("Error: Unable to retrieve profile information.");
    }

   
} else {
    echo "User ID not set or empty"; 
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Update SYSCX profile</title>
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
         <section>
            <h2>UPDATED PROFILE INFORMATION</h2>
            <h2>Personal Information</h2>
            <table>
            <tr>
               <td><label for="first_name">First Name:</label></td>
               <td><input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>"></td>
               <td><label for="last_name">Last Name:</label></td>
               <td><input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>"></td>
               <td><label for="DOB">DOB:</label></td>
               <td><input type="date" id="DOB" name="DOB" value="<?php echo $DOB; ?>"></td>
            </tr>
            </table>
         </section>

         <section>
            <h2>Address</h2>
            <table>
               <tr>
                  <td><label>Street Number:</label></td>
                  <td><input type="text" id ="street_number" name="street_number" value="<?php echo $street_number; ?>"></td>
                  <td><label>Street Name:</label></td>
                  <td><input type="text" id="street_name" name="street_name" value="<?php echo $street_name; ?>"></td>
               </tr>
               <tr>
                  <td><label>City:</label></td>
                  <td><input type="text" id="city" name="city" value="<?php echo $city; ?>"></td>
                  <td><label for="province">Province:</label></td>
                  <td><input type="text" id ="province" name="province" value="<?php echo $province; ?>"></td>
               </tr>
               <tr>
                  <td><label>Postal Code:</label></td>
                  <td><input type="text"id ="postal_code" name="postal_code"value="<?php echo $postal_code; ?>" ></td>
               </tr>
            </table>
         </section>

         <section>
            <h2>Profile Information</h2>
            <table>
               <tr>
                   <td><label>Email Address:</label></td>
                   <td><input type="email" name="student_email" value = "<?php echo $student_email; ?>"></td>
               </tr>
               <tr>
               <td>
    <label for="program">Program:</label>
</td>
   <td>
      <select name="program">
         <option value="Choose Program" <?php if ($program == "Choose Program") echo "selected"; ?>>Choose Program</option>
         <option value="Computer Systems Engineering" <?php if ($program == "Computer Systems Engineering") echo "selected"; ?>>Computer Systems Engineering</option>
         <option value="Software Engineering" <?php if ($program == "Software Engineering") echo "selected"; ?>>Software Engineering</option>
         <option value="Communications Engineering" <?php if ($program == "Communications Engineering") echo "selected"; ?>>Communications Engineering</option>
         <option value="Biomedical and Electrical" <?php if ($program == "Biomedical and Electrical") echo "selected"; ?>>Biomedical and Electrical</option>
         <option value="Electrical Engineering" <?php if ($program == "Electrical Engineering") echo "selected"; ?>>Electrical Engineering</option>
         <option value="Special" <?php if ($program == "Special") echo "selected"; ?>>Special</option>
      </select>
   </td>

               </tr>
               <tr>
                  <th colspan="2">Choose Avatar</th>
              </tr>
              <tr>
                  <td colspan="10"> 
                      <input type="radio" name="avatar" value="1" id="avatar1">
                      <label for="avatar1"><img src="images/img_avatar1.png" alt="Avatar 1"></label>
              
                      <input type="radio" name="avatar" value="2" id="avatar2">
                      <label for="avatar2"><img src="images/img_avatar2.png" alt="Avatar 2"></label>
              
                      <input type="radio" name="avatar" value="3" id="avatar3">
                      <label for="avatar3"><img src="images/img_avatar3.png" alt="Avatar 3"></label>
              
                      <input type="radio" name="avatar" value="4" id="avatar4">
                      <label for="avatar4"><img src="images/img_avatar4.png" alt="Avatar 4"></label>
              
                      <input type="radio" name="avatar" value="5" id="avatar5">
                      <label for="avatar5"><img src="images/img_avatar5.png" alt="Avatar 5"></label>
                  </td>
              </tr>
           </table>
         </section>
         <button type="submit" name= submit>Submit</button>
         <button type="reset" name = Reset>Reset</button>
      </form>
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