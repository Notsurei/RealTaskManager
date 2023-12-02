<?php
class TaskManager
{
    private $con;
    private $user_id;

    public function __construct($con, $user_id)
    {
        $this->con = $con;
        $this->user_id = $user_id;
    }

    public function createTask($title, $description, $due_date, $category)
    {
        $sql = "INSERT INTO tasks (title, description, due_date, category, user_id) VALUES ('$title', '$description', '$due_date', '$category', '$this->user_id')";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            echo "Error: " . mysqli_error($this->con);
        }
    }

    public function deleteTask($task_id)
    {
        $sql = "DELETE FROM tasks WHERE id = '$task_id' AND user_id = '$this->user_id'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            echo "Error: " . mysqli_error($this->con);
        }
    }

    public function updateTask($task_id, $title, $description, $due_date, $category)
    {
        $sql = "UPDATE tasks SET title='$title', description='$description', due_date='$due_date', category='$category' WHERE id='$task_id' AND user_id = '$this->user_id'";
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            echo "Error: " . mysqli_error($this->con);
        }
    }

    public function getTasks()
    {
        $sql = "SELECT * FROM tasks WHERE user_id = '$this->user_id'";
        $result = mysqli_query($this->con, $sql);
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $tasks;
    }

}
