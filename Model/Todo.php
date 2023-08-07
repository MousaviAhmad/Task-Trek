<?php

namespace Core\Model;

class Todo
{
    public $id; // Unique identifier for the todo item
    public $task; // The task text of the todo item
    public $status; // The status of the todo item (0 - incomplete, 1 - completed)
    public $dueDate; 
    
    public function __construct($task = "", $status = 0)
    {
        // Constructor for the Todo class, sets the initial values for task and status, and generates a unique id
        $this->task = $task; // The task text of the todo item
        $this->status = $status; // The status of the todo item (0 - incomplete, 1 - completed)
        $this->id = uniqid(); // Generates a unique id for the todo item using uniqid() function
    }

    public function toArray()
    {
        // Converts the Todo object to an associative array for easier serialization to JSON
        return [
            'id' => $this->id, // Unique identifier for the todo item
            'task' => $this->task, // The task text of the todo item
            'status' => $this->status, // The status of the todo item (0 - incomplete, 1 - completed)
            'dueDate' => $this->dueDate 
        ];
    }
}
