<?php
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
    echo "<p>Error: " . htmlspecialchars($error_message) . "</p>";
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
   <nav>
      <div class="sidebar">
         <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
         </ul>
      </div>
   </nav>

   <main>
      <form id ="signupForm" action="profile.php" method="POST">
         <section>
            <h2>UPDATED PROFILE INFORMATION</h2>
            <h2>Personal Information</h2>
            <table>
               <tr>
                  <td><label for="firstName">First Name:</label></td>
                  <td><input type="text" id="firstName" name="first_name"></td>
                  <td><label for="lastName">Last Name:</label></td>
                  <td><input type="text" id="lastName" name="last_name"></td>
                  <td><label for="dob">Date of Birth:</label></td>
                  <td><input type="date" id="dob" name="DOB"></td>
               </tr>
            </table>
         </section>

         <section>
            <h2>Profile Information</h2>
            <table>
               <tr>
                  <td><label for="email">Email Address:</label></td>
                  <td><input type="email" id="email" name="student_email"></td>
               </tr>
               <tr>
                  <td><label for="program">Program:</label></td>
                  <td>
                     <select id="program" name="program">
                        <option value="Choose Program">Choose Program</option>
                        <option value="Computer Systems Engineering">Computer Systems Engineering</option>
                        <option value="Software Engineering">Software Engineering</option>
                        <option value="Communications Engineering">Communications Engineering</option>
                        <option value="Biomedical and Electrical">Biomedical and Electrical</option>
                        <option value="Electrical Engineering">Electrical Engineering</option>
                        <option value="Special">Special</option>
                     </select>
                  </td>
               </tr>
               <tr>
                  <td>
                     <label for="password">Password:</label>
                     <input type="password" id="password" name="password" required>
                  </td>
                  <td>
                     <label for="confirm_password">Confirm Password:</label>
                     <input type="password" id="confirm_password" name="confirm_password">
                  </td>
                  </tr>
            </table>
         </section>
         <button type="submit" name = Register>Register</button>
         <button type="reset" name = Reset >Reset</button>
      </form>
      <p>Already have an account? <a href="login.php">Login</a></p>
   </main>

  

   <section id="informationsection">
      <div class="information">
      </div>
   </section>

</body>


</html>