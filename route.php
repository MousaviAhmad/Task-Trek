<?php
require_once __DIR__ . "/Controller/TodoController.php";
require_once __DIR__ . "/Model/Todo.php";

use Core\Controller\TodoController;

$todoController = new TodoController(); // Create an instance of TodoController to handle todo-related actions

$request = $_SERVER['REQUEST_URI']; // Get the requested URI (e.g., /Case, /Case/controller/todo, /Case/controller/todo/123)
$method = $_SERVER['REQUEST_METHOD']; // Get the HTTP request method (e.g., GET, POST, PUT, DELETE)

// Start routing based on the requested URI
switch ($request) {
    case '/Case':
    case '/Case/':
        // If the request is for the root, display the index page (view)
        require __DIR__ . '/Views/index.phtml';
        break;
    case '/Case/controller/todo':
        // If the request is for /Case/controller/todo, handle todo-related actions
        header('Content-Type: application/json; charset=utf-8');
        switch ($method) {
            case 'GET':
                // If the method is GET, retrieve all todos from the controller and send them as a JSON response
                echo $todoController->getTodos();
                break;
            case 'POST':
                // If the method is POST, add a new todo by reading the request body, adding the todo, and sending the added todo as a JSON response
                echo $todoController->addTodo();
                break;
            case 'DELETE':
                // If the method is DELETE, extract the todo ID from the request body, delete the todo by ID, and send a JSON response indicating success or failure
                $input = json_decode(file_get_contents('php://input'), true);
                $todoId = $input['id'] ?? null;
                if ($todoId !== null) {
                    echo $todoController->deleteTodoById($todoId);
                } else {
                    echo json_encode(["error" => "Invalid request"]);
                }
                break;
            default:
                // If the method is not allowed, return a JSON response with an error message and set the HTTP response code to 405 (Method Not Allowed)
                http_response_code(405);
                echo json_encode(["error" => "Method Not Allowed"]);
                break;
        }
        break;
    case preg_match('/\/Case\/controller\/todo\/(.+)/', $request, $matches) ? true : false:
        // If the request matches the pattern /Case/controller/todo/{todoId}, handle todo-related actions for a specific todo
        header('Content-Type: application/json; charset=utf-8');
        $todoId = $matches[1]; // Extract the todoId from the request URI

        switch ($method) {
            case 'PUT':
                // If the method is PUT, update the task text, due date or status of the todo based on the request body, and send a JSON response indicating success or failure
                $input = json_decode(file_get_contents('php://input'), true);
                $task = $input['task'] ?? null;
                $dueDate = $input['dueDate'] ?? null;
            
                if ($task !== null) {
                    echo $todoController->updateTodoTask($todoId, $task);
                } elseif ($dueDate !== null) {
                    echo $todoController->updateTodoDueDate($todoId, $dueDate);
                } else {
                    $status = $input['status'] ?? null;
                    if ($status !== null) {
                        echo $todoController->updateTodoStatus($todoId, $status);
                    } else {
                        echo json_encode(["error" => "Invalid request"]);
                    }
                }
                break;
            
            default:
                // If the method is not allowed, return a JSON response with an error message and set the HTTP response code to 405 (Method Not Allowed)
                http_response_code(405);
                echo json_encode(["error" => "Method Not Allowed"]);
                break;
        }
        break;
    default:
        // If the requested URI does not match any of the defined routes, return a 404 (Not Found) response and display the 404 page (view)
        http_response_code(404);
        require __DIR__ . '/Views/404.phtml';
        break;
}
?>
