<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteTasksSql = "DELETE FROM tasks WHERE user_id = $deleteId";
    mysqli_query($con, $deleteTasksSql);

    $deleteUserSql = "DELETE FROM user WHERE user_id = $deleteId";
    mysqli_query($con, $deleteUserSql);
}

if(isset($_POST['log_out'])){
    unset($_SESSION['admin']);
    session_destroy();
    exit();
}

$sql = "SELECT * FROM user";
$result = mysqli_query($con, $sql);
$tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">
    <style>
        #light-toggle,
        #dark-toggle {
            font-size: 20px;
            cursor: pointer;
            margin-left: 10px;
        }

        i {
            font-size: 20px;
            cursor: pointer;
        }

        .dark-mode {
            background-color: #06061c;
            color: white;
        }

        .light-mode {
            background-color: #add8e6;
            color: black;
        }

        .hidden {
            display: none;
        }

        #namesearch {
            border-radius: 5px;
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
                        <a class="nav-link" aria-current="page" href="/taskmng.php">Task Manager</a>
                    </li>
                </ul>
                <form class="d-flex" method="post" name="log_out">
                    <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#Exit">Log out</button>
                </form>
                <div class="modal" id="Exit" tabindex="-1" aria-labelledby="exitTask" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-center">
                                <h5 class="modal-title text-black">Are you sure?</h5>
                            </div>
                            <div class="modal-body d-flex justify-content-center">
                                <a href="/adminlogin.php" style="margin: 10px;"><button class="btn btn-danger">Yes</button></a>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close" style="margin: 10px;">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <i class="bi bi-moon-fill" id="light-toggle"></i>
    <i class="bi bi-brightness-high-fill" id="dark-toggle"></i>
    <div class="container">
        <h1>User Management</h1>
        <div class="container"><input type="text" id="namesearch"></div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task) : ?>
                    <tr class="user-row">
                        <td><?php echo $task['user_id']; ?></td>
                        <td><?php echo $task['username']; ?></td>
                        <td><?php echo $task['password']; ?></td>
                        <td>
                            <a href="?delete_id=<?php echo $task['user_id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const light = document.getElementById('light-toggle');
        const dark = document.getElementById('dark-toggle');
        const body = document.querySelector('body');
        const namesearch = document.getElementById('namesearch');
        const userRows = document.querySelectorAll('.user-row');

        //xử lí chức năng tìm kiếm
        namesearch.addEventListener('input', () => {
            const searchTerm = namesearch.value.toLowerCase();
            userRows.forEach((row) => {
                const username = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                if (username.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });


        //xử lí dark mode
        light.addEventListener('click', () => {
            light.style.display = 'none';
            dark.style.display = 'block';
            dark.style.color = 'white';
            body.style.color = 'white';
            body.style.backgroundColor = '#06061c';
            body.style.transition = '1s';
            var theme = 'dark';
            localStorage.setItem('Realtheme', JSON.stringify(theme));
        });
        dark.addEventListener('click', () => {
            light.style.display = 'block';
            dark.style.display = 'none';
            light.style.color = 'black';
            body.style.color = 'black';
            body.style.backgroundColor = '#add8e6';
            body.style.transition = '1s';
            var theme = 'light';
            localStorage.setItem('Realtheme', JSON.stringify(theme));
        });

        let Gettheme = JSON.parse(localStorage.getItem('Realtheme'));
        if (Gettheme === 'dark') {
            body.classList = 'dark-mode';
            light.classList.add('hidden');
        } else {
            body.classList = 'light-mode';
            dark.classList.add('hidden');
        }

        
    </script>
</body>

</html>