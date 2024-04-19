<?php
require_once "db_connection.php";
if(isset($_GET['signIn'])) {
  $signUpDisplay = "none"; 
  $signinDisplay = "block"; 
} elseif(isset($_GET['signUp'])) {
$signUpDisplay = "flex";  
$signinDisplay = "none";  
}
$message = '';
$sucsess = '';
if(isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    if($password === $passwordConfirm){
      $sql = "INSERT INTO user (username, email, password ,phone ,address) VALUES ('$username', '$email', '$password','$phone','$address')";
      if ($conn->query($sql) === TRUE) {
        $sucsess = "User registered successfully. Please sign in.";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
    }else{
      $message = 'Passwords do not match';
    }
}
if(isset($_POST['signin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM user WHERE email='$email'";
    $result = $conn->query($sql);
    if (!$result) {
        die("Error executing query: " . $conn->error);
    }elseif ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
          session_start();
          $_SESSION['username'] = $row['username'];
          $_SESSION['id'] = $row['id'];
          $_SESSION['address'] = $row['address'];
          $_SESSION['email'] = $row['email'];
          header("Location: home.php");
        } else {
            $message = "Incorrect email or password.";
        }
    } else {
        $message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alson MotoParts - Authentication</title>
    <link rel="icon" type="image/x-icon" href="/img/logo.jpg" />
    <link rel="stylesheet" href="css/auth.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/logo.jpg" />
</head>
<body>
    <div class="container">
    <form class="sign-up" method="post" style="display: <?php echo $signUpDisplay; ?>">
        <p>sign up</p>
        <p class="succses"><?php echo $sucsess; ?></p>
        <div class="half1">
            <input type="text" placeholder="username" name="username" required/><br />
            <input type="email" placeholder="Email" name="email" required/><br />
            <input type="number" placeholder="phone number" name="phone" required/><br />
        </div>
        <div class="half2">
            <input type="text" placeholder="your address" name="address" required /><br />
            <input type="password" placeholder="Password" name="password" required/><br />
            <input type="password" placeholder="Password confirm" name="passwordConfirm" required/><br />
        </div>

        <input type="submit" name="signup" value="Sign up" /><br />
        <p class="message"><?php echo $message; ?></p>
        <a href="?signIn">sign in</a>
      </form>
        <form class="sign-in" method="post" style="display: <?php echo $signinDisplay; ?>">
            <p>Sign In</p>
            <input type="email" name="email" placeholder="Email" required /><br />
            <input type="password" name="password" placeholder="Password" required /><br />
            <input type="submit" name="signin" value="Sign In" /><br />
            <p class="message"><?php echo $message; ?></p>
            <a href="?signUp">Sign Up</a>
        </form>
        <div class="drops">
            <div class="drop drop-1"></div>
            <div class="drop drop-2"></div>
            <div class="drop drop-3"></div>
            <div class="drop drop-4"></div>
            <div class="drop drop-5"></div>
        </div>
    </div>
</body>
</html>
