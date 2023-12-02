<?php
session_start();
include 'connect.php';
include 'taskmanager.php';

$user_id = $_SESSION['user_id'];
$taskManager = new TaskManager($con, $user_id);

if(!isset($_SESSION['username'])){
    header("Location: user.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $due_date = $_POST['due_date'];
        $category = $_POST['category'];

        $taskManager->createTask($title, $description, $due_date, $category);
    }

    if (isset($_POST['delete'])) {
        $task_id = $_POST['task_id'];

        $taskManager->deleteTask($task_id);
    }

    if (isset($_POST['edit'])) {
        $task_id = $_POST['task_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $due_date = $_POST['due_date'];
        $category = $_POST['category'];

        $taskManager->updateTask($task_id, $title, $description, $due_date, $category);
    }

    if(isset($_POST['log_out'])){
        unset($_SESSION['username']);
        session_destroy();
        exit();
    }
}

$tasks = $taskManager->getTasks();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Fira Sans', sans-serif;
        }

        #Addbtn,
        #Hidebtn {
            margin: 0;
            position: absolute;
            left: 50%;
            border-radius: 20px;
        }

        #Addbtn:hover,
        #Hidebtn:hover {
            transform: translateY(-1px);
        }

        #Form {
            margin: 20px;
        }

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

        #scrollback {
            position: fixed;
            right: 2%;
            bottom: 2%;
        }

        #Showtl:hover,
        #Hidetl:hover {
            transform: translateY(-1px);
        }

        #TimeLine {
            margin: 5em auto;
            max-width: 34rem;
        }

        #timeline {
            max-width: 34em;
            padding-top: 2em;
            padding-bottom: 2em;
            position: relative;
        }

        #timeline .card {
            border: 2px solid #888;
            border-radius: 1em;
            padding: 1.5em;
        }

        #timeline .card:nth-child(odd) {
            border-left: 3px solid #888;
            padding-left: 3em;
            transform: translateX(17em);
        }

        #timeline .card:nth-child(even) {
            border-right: 3px solid #888;
            padding-right: 3em;
            transform: translateX(-17em);
        }

        @media screen and (max-width: 1150px) {

            #timeline .card:nth-child(odd),
            #timeline .card:nth-child(even) {
                border: 2px solid #888;
                padding-left: 1.5em;
                padding-right: 1.5em;
                transform: none;
            }

            #scrollback {
                display: none;
            }
        }
    </style>
    <title>Task Manager</title>
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
                        <a class="nav-link" aria-current="page" href="#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="#TimeLine">Timeline</a>
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
                                <a href="/user.php" style="margin: 10px;"><button class="btn btn-danger">Yes</button></a>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close" style="margin: 10px;">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <a href="#" id="scrollback"><button class="btn btn-info"><i class="bi bi-arrow-up"></i></button></a>
    <i class="bi bi-moon-fill" id="light-toggle"></i>
    <i class="bi bi-brightness-high-fill" id="dark-toggle"></i>
    <div class="container">
        <h1>Task Manager</h1>
        <button id="Addbtn" class="btn btn-primary" style="border-radius: 25px;"><i class="bi bi-plus-lg"></i></button>
        <button style="display: none; border-radius: 25px;" id="Hidebtn" class="btn btn-danger"><i class="bi bi-dash-lg"></i></button>

        <form method="POST" style="display: none;" id="Form">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" id="description" name="description" required>
            </div>
            <div class="mb-3">
                <label for="due_date" class="form-label">Due date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" required>
            </div>
            <label for="category" class="form-label">Category</label>
            <select class="form-control" id="category" name="category">
                <option value="todo">To do</option>
                <option value="inprog">In Progress</option>
                <option value="finished">Finished</option>
            </select>
            <button type="submit" class="btn btn-primary" name="submit">Add</button>
        </form>

    </div>
    <div class="container">
        <h2>Tasks List</h2>
        <div class="row">
            <div class="col-md-4" id="list">
                <h2>To do</h2>
                <?php foreach ($tasks as $task) : ?>
                    <?php if ($task['category'] === 'todo') : ?>
                        <div class="card mb-3" id="li">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $task['title']; ?></h5>
                                <p class="card-text"><?php echo $task['description']; ?></p>
                                <p class="card-text"><?php echo $task['due_date']; ?></p>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="btn btn-danger" name="delete">Delete</button>
                                </form>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editTaskModal<?php echo $task['id']; ?>">Edit</button>
                            </div>
                            <!-- hiện form sửa tác vụ -->
                            <div class="modal" id="editTaskModal<?php echo $task['id']; ?>" tabindex="-1" aria-labelledby="editTaskModalLabel<?php echo $task['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editTaskModalLabel<?php echo $task['id']; ?>">Edit task</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $task['title']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <input type="text" class="form-control" id="description" name="description" value="<?php echo $task['description']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="due_date" class="form-label">Due Date</label>
                                                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo $task['due_date']; ?>" required>
                                                </div>
                                                <label for="category" class="form-label">Category</label>
                                                <select class="form-control" id="category" name="category">
                                                    <option value="todo">To do</option>
                                                    <option value="inprog">In Progress</option>
                                                    <option value="finished">Finished</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary" name="edit">Save</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="col-md-4">
                <h2>In Progress</h2>
                <?php foreach ($tasks as $task) : ?>
                    <?php if ($task['category'] === 'inprog') : ?>
                        <div class="card mb-3" id="li">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $task['title']; ?></h5>
                                <p class="card-text"><?php echo $task['description']; ?></p>
                                <p class="card-text"><?php echo $task['due_date']; ?></p>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="btn btn-danger" name="delete">Delete</button>
                                </form>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editTaskModal<?php echo $task['id']; ?>">Edit</button>
                            </div>
                            <!-- hiện form sửa tác vụ -->
                            <div class="modal" id="editTaskModal<?php echo $task['id']; ?>" tabindex="-1" aria-labelledby="editTaskModalLabel<?php echo $task['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editTaskModalLabel<?php echo $task['id']; ?>">Edit task</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $task['title']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <input type="text" class="form-control" id="description" name="description" value="<?php echo $task['description']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="due_date" class="form-label">Due Date</label>
                                                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo $task['due_date']; ?>" required>
                                                </div>
                                                <label for="category" class="form-label">Category</label>
                                                <select class="form-control" id="category" name="category">
                                                    <option value="todo">To do</option>
                                                    <option value="inprog">In Progress</option>
                                                    <option value="finished">Finished</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary" name="edit">Save</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="col-md-4">
                <h2>Finished</h2>
                <?php foreach ($tasks as $task) : ?>
                    <?php if ($task['category'] === 'finished') : ?>
                        <div class="card mb-3" id="li">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $task['title']; ?></h5>
                                <p class="card-text"><?php echo $task['description']; ?></p>
                                <p class="card-text"><?php echo $task['due_date']; ?></p>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="btn btn-danger" name="delete">Delete</button>
                                </form>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editTaskModal<?php echo $task['id']; ?>">Edit</button>
                            </div>
                            <!-- hiện form sửa tác vụ -->
                            <div class="modal" id="editTaskModal<?php echo $task['id']; ?>" tabindex="-1" aria-labelledby="editTaskModalLabel<?php echo $task['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editTaskModalLabel<?php echo $task['id']; ?>">Edit task</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $task['title']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <input type="text" class="form-control" id="description" name="description" value="<?php echo $task['description']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="due_date" class="form-label">Due Date</label>
                                                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo $task['due_date']; ?>" required>
                                                </div>
                                                <label for="category" class="form-label">Category</label>
                                                <select class="form-control" id="category" name="category">
                                                    <option value="todo">To do</option>
                                                    <option value="inprog">In Progress</option>
                                                    <option value="finished">Finished</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary" name="edit">Save</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <section id="TimeLine" class="container">
        <h1>TimeLine</h1>
        <button id="Showtl" class="btn btn-primary" style="border-radius: 25px;"><i class="bi bi-plus-lg"></i></button>
        <button style="display: none; border-radius: 25px;" id="Hidetl" class="btn btn-danger"><i class="bi bi-dash-lg"></i></button>
        <div id="timeline" style="display: none;">
            <?php foreach ($tasks as $task) : ?>
                <!-- phần này được javascript ở dưới tạo -->
            <?php endforeach; ?>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let addbtn = document.getElementById('Addbtn');
        let hidebtn = document.getElementById('Hidebtn');
        let form = document.getElementById('Form');
        const light = document.getElementById('light-toggle');
        const dark = document.getElementById('dark-toggle');
        const body = document.querySelector('body');
        const timeline = document.getElementById('timeline');
        const Showtl = document.getElementById('Showtl');
        const Hidetl = document.getElementById('Hidetl');
        var taskCards = Array.from(timeline.getElementsByClassName('card'));

        //xử lí hiện form
        addbtn.addEventListener('click', () => {
            hidebtn.style.display = 'block';
            form.style.display = 'block';
            addbtn.style.display = 'none';
        });
        hidebtn.addEventListener('click', () => {
            hidebtn.style.display = 'none';
            form.style.display = 'none';
            addbtn.style.display = 'block';
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

        //xử lí hiện timeline 
        Showtl.addEventListener('click', () => {
            Showtl.style.display = 'none';
            Hidetl.style.display = 'block';
            timeline.style.display = 'block';
        });
        Hidetl.addEventListener('click', () => {
            Showtl.style.display = 'block';
            Hidetl.style.display = 'none';
            timeline.style.display = 'none';
        });

        //sắp xếp các task theo ngày tháng
        var tasks = <?php echo json_encode($tasks); ?>;

        tasks.sort(function(a, b) {
            var dateA = new Date(a.due_date);
            var dateB = new Date(b.due_date);

            return dateA - dateB;
        });

        tasks.forEach(function(task) {
            var card = document.createElement('div');
            card.className = 'card mb-3';
            card.innerHTML = `
            <div class="card-body">
                <h5 class="card-title">${task.title}</h5>
                <p class="card-text">${task.description}</p>
                <p class="card-text">${task.due_date}</p>
                <p class="card-text">${task.category}</p>
            </div>`;
            timeline.appendChild(card);
        });
    </script>
</body>

</html>