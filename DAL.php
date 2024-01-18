<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include 'db.php';

function getAllTasks() {
    global $conn;
    $sql = "SELECT * FROM tasks";
    $result = $conn->query($sql);
    return $result;
}

function addTask() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "INSERT INTO tasks (name, description, completed) VALUES (?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $data['name'], $data['description']);
    $stmt->execute();
    return $stmt->insert_id;
}

function updateTask() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "UPDATE tasks SET name=?, description=?, completed=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $data['name'], $data['description'], $data['completed'], $data['id']);
    $stmt->execute();
}

function deleteTask() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "DELETE FROM tasks WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $data['id']);
    $stmt->execute();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $tasks = getAllTasks();
        header('Content-Type: application/json');
        echo json_encode($tasks->fetch_all(MYSQLI_ASSOC));
        break;
    case 'POST':
        $taskId = addTask();
        echo json_encode(['id' => $taskId]);
        break;
    case 'PUT':
        updateTask();
        echo json_encode(['status' => 'success']);
        break;
    case 'DELETE':
        deleteTask();
        echo json_encode(['status' => 'success']);
        break;
}
?>
