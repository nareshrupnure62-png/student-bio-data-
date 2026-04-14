<?php
session_start();
$con = mysqli_connect("localhost","root","","test");

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql    = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($con,$sql);

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']     = $row['role'];

        if($row['role'] == 'student'){
            header("Location: biodata.php");
        } else {
            header("Location: admin.php");
        }
        exit();
    } else {
        echo "<script>alert('Invalid Login');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<style>
.gh {
    width:100%; position:absolute; z-index:-1; opacity:0.4;
}
form {
    width:450px; margin:40px auto; background:#47badd;
    padding:30px; border-radius:10px;
    box-shadow:0 10px 25px rgba(129,198,238,0.2);
}
</style>
</head>
<body>
<img class="gh" src="gh.jpg" alt="college">
<form method="post" align="center">
    <h2>Login</h2>
    Username: <input type="text"     name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <input type="submit" name="login" value="Login">
</form>
</body>
</html>