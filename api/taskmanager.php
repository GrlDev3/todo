<?php

include "Task.php";

$filePath = '../data/tasks.json';

function readTasks(){
    global $filePath;
    //echo $filePath;
    if(file_exists($filePath)){
        file_put_contents($filePath, '[]');
    }
    $tasks = json_decode(file_get_contents($filePath), true);
    return isset($tasks) ? $tasks : [];
}

function writeTasks($task){
    global $filePath;
    file_put_contents($filePath, json_encode($task, JSON_PRETTY_PRINT));
}

function createTask($title){
    $tasks = readTasks();
    $id = uniqid();
    $newTask = new Task($id, $title);
    $tasks[] = $newTask->getTaskData();
    writeTasks($tasks);
    return $newTask;
}

function deleteTask($id){
    $tasks = readTasks();
    /*$task = $tasks[$id];
    if(!empty($task))
    {
        array_splice($tasks,$id, 1);
        writeTasks($tasks);
        return true;
    }*/
    foreach( $tasks as $key => $task)
    {
        if($task['id'] == $id)
        {
            array_splice($tasks, $key, 1);
            writeTasks($tasks);
            return true;
        }
    }
    return false;
    
}

function updateTask($id, $title){
    $tasks = readTasks();
/*    $task = $tasks[$id];
    if (!empty($task)) {
        $task->setTitle($title);
        writeTasks($tasks);
        return $task;
    }
*/
    foreach ($tasks as $task) {
        if($task['id'] == $id)
        {
            $task['title'] = $title;
            writeTasks($tasks);
            return $task;
        }
    }
    return false;
}

function handleGetRequest(){
    $tasks = readTasks();
    //echo json_encode($tasks);
    return json_encode($tasks);
}

function handlePostRequest(){
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['title'])) {
        http_response_code(400); // Bad Request
        //echo json_encode(['error' => 'Task title cannot be empty']); return;
        return json_encode(['error' => 'Task title cannot be empty']);
    }
    $retval = createTask($input['title']);

    http_response_code(201); // Created
    //echo json_encode($retval);
    return json_encode($retval);
}

function handlePutRequest(){
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['id']) || empty($input['title'])) {
        http_response_code(400); // Bad Request
        //echo json_encode(['error' => 'Task ID and title are required']); return;
        return json_encode(['error' => 'Task ID and title are required']);
    }

    $retval = updateTask($input['id'], $input['title']);
    if(!$retval)
    {
        http_response_code(404); // Not Found
        //echo json_encode(['error' => 'Task not found']); return;
        return json_encode(['error' => 'Task not found']);
    }
    http_response_code(200); // OK
    //echo json_encode($retval);
    return json_encode($retval);

    
}

function handleDeleteRequest(){
    $taskId = isset($_GET['id']) ? $_GET['id'] : null;

    if ($taskId === null) {
        http_response_code(400); // Bad Request
        //echo json_encode(['error' => 'Task ID is required']); return;
        return json_encode(['error' => 'Task ID is required']);
    }

    $retval = deleteTask($taskId);

    if($retval)
    {
        http_response_code(204); // No Content
        return;
    }
    http_response_code(404); // Not Found
    //echo json_encode(['error' => 'Task not found']);
    return json_encode(['error' => 'Task not found']);

}


$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        echo handleGetRequest();
    break;

    case 'POST':
        echo handlePostRequest();
    break;

    case 'PUT':
        echo handlePutRequest();
    break;

    case 'DELETE':
        echo handleDeleteRequest();
    break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    break;
}

?>