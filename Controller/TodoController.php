<?php

namespace Core\Controller;

use Core\Model\Todo;

class TodoController
{
    private $dbFile;

    public function __construct()
    {
        // Constructor sets the path to the database file (db.json) in the parent directory
        $this->dbFile = __DIR__ . "/../db.json";
    }

    public function getTodos()
    {
        // Fetches and returns all the todos from the database file
        $db = $this->getDb();
        return json_encode($db);
    }

    public function addTodo()
{
    $input = file_get_contents('php://input');
    $jsonData = json_decode($input, true);

    // Check if the task is provided in the input and it is not empty
    if (!isset($jsonData['task']) || empty(trim($jsonData['task']))) {
        return json_encode(["error" => "Task is missing or empty"]);
    }

    // Include the due date in the JSON data
    $dueDate = isset($jsonData['dueDate']) ? $jsonData['dueDate'] : null;

    $data = $this->getDb();

    // Create a new Todo object with the task and due date properties
    $todo = new Todo();
    $todo->task = $jsonData['task'];
    $todo->dueDate = $dueDate; // Add the due date to the Todo object

    $data[] = $todo->toArray();
    $this->saveDb($data);

    // Return the newly added task as a response
    return json_encode(["task" => $todo->task, "dueDate" => $todo->dueDate]);
}

    

    public function updateTodoStatus($todoId, $status)
    {
        // Updates the status (completed/incomplete) of a todo item in the database
        $data = $this->getDb();
        $updated = false;

        foreach ($data as &$todo) {
            if ($todo['id'] === $todoId) {
                $todo['status'] = $status;
                $updated = true;
                break;
            }
        }

        if ($updated) {
            // If the todo is found and updated, save the changes to the database
            $this->saveDb($data);
            return json_encode(["success" => "Todo status updated successfully"]);
        } else {
            // If the todo is not found, return an error message
            return json_encode(["error" => "Todo not found"]);
        }
    }

    public function updateTodoTask($todoId, $task)
    {
        // Updates the task text of a todo item in the database
        $data = $this->getDb();
        $updated = false;

        foreach ($data as &$todo) {
            if ($todo['id'] === $todoId) {
                $todo['task'] = $task;
                $updated = true;
                break;
            }
        }

        if ($updated) {
            // If the todo is found and updated, save the changes to the database
            $this->saveDb($data);
            return json_encode(["success" => "Todo task updated successfully"]);
        } else {
            // If the todo is not found, return an error message
            return json_encode(["error" => "Todo not found"]);
        }
    }

    public function deleteTodoById($todoId)
    {
        // Deletes a todo item from the database based on its ID
        $data = $this->getDb();
        $filteredData = array_filter($data, function ($todo) use ($todoId) {
            return $todo['id'] !== $todoId;
        });
        $this->saveDb(array_values($filteredData)); // Convert associative array to indexed array

        return json_encode(["success" => "Todo deleted successfully"]);
    }

    public function updateTodoDueDate($todoId, $dueDate)
    {
        // Updates the due date of a todo item in the database
        $data = $this->getDb();
        $updated = false;

        foreach ($data as &$todo) {
            if ($todo['id'] === $todoId) {
                $todo['dueDate'] = $dueDate;
                $updated = true;
                break;
            }
        }

        if ($updated) {
            // If the todo is found and updated, save the changes to the database
            $this->saveDb($data);
            return json_encode(["success" => "Todo due date updated successfully"]);
        } else {
            // If the todo is not found, return an error message
            return json_encode(["error" => "Todo not found"]);
        }
    }

    private function getDb()
    {
        // Reads and returns the content of the database file as an associative array
        $dbData = file_get_contents($this->dbFile);
        return json_decode($dbData, true) ?? [];
    }

    private function saveDb($data)
    {
        // Encodes the data as JSON and saves it to the database file (db.json)
        file_put_contents($this->dbFile, json_encode($data, JSON_PRETTY_PRINT));
        
    }
}