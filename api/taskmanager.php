<?

$filePath = 'data/tasks.json';

function readTasks(){
    global $filePath;
    echo $filePath;
    if(file_exists($filePath)){
        file_put_contents($filePath, '[]');
    }
    $tasks = json_decode(file_get_contents($filePath), true);
    return $tasks ?? [];
}

function writeTasks($task){
    global $filePath;
    file_put_contents($filePath, json_encode($task));   
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
    $task = $tasks[$id];
    if(!empty($task))
    {
        array_splice($tasks,$id, 1);
        writeTasks($tasks);
        return true;
    }
    return false;
    
}

function updateTask($id, $title){
    $tasks = readTasks();
    $task = $tasks[$id];
    if (!empty($task)) {
        $task->setTitle($title);
        writeTasks($tasks);
        return $task;
    }
    return false;
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        handleGetRequest();
    break;
        
    case 'POST':
        handlePostRequest();
    break;
    
    case 'PUT':
        handlePutRequest();
    break;
                
    case 'DELETE':
        handleDeleteRequest();
    break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    break;
}

function handleGetRequest(){
    $tasks = readTasks();
    echo json_encode($tasks);
}

function handlePostRequest(){
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['title'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Task title cannot be empty']);
        return;
    }
    $retval = createTask($input['title']);

   /* $tasks = readTasks();
    $newTask = [
        'id' => count($tasks) > 0 ? $tasks[count($tasks) - 1]['id'] + 1 : 1,
        'title' => $input['title'],
        'created_at' => date('Y-m-d H:i:s')
    ];

    $tasks[] = $newTask;
    writeTasks($tasks);*/

    http_response_code(201); // Created
    echo json_encode($retval);
}

function handlePutRequest(){
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['id']) || empty($input['title'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Task ID and title are required']);
        return;
    }

    /*$tasks = readTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] == $input['id']) {
            $task['title'] = $input['title'];
            writeTasks($tasks);
            http_response_code(200); // OK
            echo json_encode($task);
            return;
        }
    }*/

    $retval = updateTask($input['id'], $input['title']);
    if($retval == false)
    {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Task not found']);
        return;
    }
    http_response_code(200); // OK
    echo json_encode($$retval);

    
}

function handleDeleteRequest(){
    $taskId = isset($_GET['id']) ? (int) $_GET['id'] : null;

    if ($taskId === null) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Task ID is required']);
        return;
    }

    /*
    $tasks = readTasks();
    foreach ($tasks as $index => $task) {
        if ($task['id'] == $taskId) {
            array_splice($tasks, $index, 1);
            writeTasks($tasks);
            
            return;
        }
    }*/
    $retval = deleteTask($taskId);

    if($retval)
    {
        http_response_code(204); // No Content
        return;
    }
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Task not found']);
    


    
}
?>