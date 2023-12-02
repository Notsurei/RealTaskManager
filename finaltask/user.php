<?php
session_start();
include 'connect.php';

$loginError = "";
$signupError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $user_id = $row['user_id'];
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $loginError = "Invalid username or password";
        }
    } elseif (isset($_POST['signup'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $checkQuery = "SELECT * FROM user WHERE username='$username'";
        $checkResult = mysqli_query($con, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $signupError = "This name is already used";
        } else {
            $sql = "INSERT INTO user (username, password) VALUES ('$username', '$password')";

            if (mysqli_query($con, $sql)) {
                $user_id = mysqli_insert_id($con);
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                header("Location: user.php");
                exit();
            } else {
                $signupError = "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login/ Sign up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Fira Sans', sans-serif;
        }

        body {
            background-color: #add8e6;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin: 20px;
            width: 300px;
        }

        h1 {
            font-size: 20px;
            text-align: center;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">KVtask</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/Final_PJ/present.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/adminlogin.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="form-container" id="login">
            <h1>Log in</h1>
            <form method="POST">
                <input type="text" id="username" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" id="password" required>
                <button type="submit" class="btn btn-primary" name="login">Log in</button>
                <a href="#" id="signuplink" style="text-decoration: none;">Sign up</a>
                <?php if (!empty($loginError)) : ?>
                    <p style="color: red;"><?php echo $loginError; ?></p>
                <?php endif; ?>
            </form>
        </div>
        <div class="form-container" id="regis" style="display: none;">
            <h1>Sign up</h1>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn btn-primary" name="signup">Sign up</button>
                <a href="#" id="loginlink" style="text-decoration: none;">Log in</a>
                <?php if (!empty($signupError)) : ?>
                    <p style="color: red;"><?php echo $signupError; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const login = document.getElementById('login');
        const regis = document.getElementById('regis');
        const signuplink = document.getElementById('signuplink');
        const loginlink = document.getElementById('loginlink');

        //toggle đăng kí 
        signuplink.addEventListener('click', (e) => {
            e.preventDefault();
            regis.style.display = 'block';
            login.style.display = 'none';
        });

        loginlink.addEventListener('click', (e) => {
            e.preventDefault();
            regis.style.display = 'none';
            login.style.display = 'block';
        });
    </script>
</body>

</html>