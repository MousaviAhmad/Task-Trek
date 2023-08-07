// Function mainApp() returns an object containing the data and methods to manage the TODO list
function mainApp() {
    return {
        todos: [], // Holds the list of todos fetched from the backend
        newTask: '', // Holds the new task input value
        newDueDate: '',// Holds the new due date input value
        editingTodoId: null, // Stores the id of the task being edited
        isModalOpen: false, // Controls the visibility of the delete confirmation modal
        todoToDelete: null, // Holds the id of the task to be deleted
        editingDueDateTodoId: null, 

        // The init() function is automatically called when the application loads to fetch existing todos
        init() {
            this.fetchTodos();
        },

        // Fetches todos from the server and updates the todos array
        fetchTodos() {
            fetch('/Case/controller/todo')
                .then(response => response.json())
                .then(data => {
                    this.todos = data;
                })
                .catch(error => {
                    console.error(error);
                });
        },

// Adds a new todo to the server and updates the todos array
addTodo() {
    const errorMessageElement = document.getElementById('errorMessage');
    
    if (!this.newTask.trim()) {
        errorMessageElement.style.display = 'block';
        errorMessageElement.querySelector('span.block').innerText = "Task cannot be empty.";
        return;
    }
    
    errorMessageElement.style.display = 'none'; // Hide any existing error message

    fetch('/Case/controller/todo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ task: this.newTask, dueDate: this.newDueDate }) 
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Failed to add todo.');
        }
    })
    .then(data => {
        this.newTask = '';
        this.newDueDate = ''; // Reset the due date input after adding
        this.fetchTodos();
    })
    .catch(error => {
        console.error(error);
    });
},

        

        // Toggles the status (completed/incomplete) of a todo item
        toggleTodoStatus(todoId) {
            const todo = this.todos.find((item) => item.id === todoId);

            if (todo) {
                const status = todo.status === 0 ? 1 : 0;

                fetch(`/Case/controller/todo/${todoId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        todo.status = status;
                    } else {
                        console.error(data.error);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            }
        },

        // Enables editing of a specific todo
        editTodo(todoId) {
            this.editingTodoId = todoId;
        },

        editTodoDueDate(todoId) {
            this.editingTodoId = todoId;
        },

        // Updates the due date of a todo item
        updateTodoDueDate(todoId) {
            const todo = this.todos.find((item) => item.id === todoId);

            if (todo) {
                fetch(`/Case/controller/todo/${todoId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ dueDate: todo.dueDate }) 
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.editingTodoId = null; // Close the due date editor after updating
                    } else {
                        console.error(data.error);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            }
        },

        

        // Updates the task text and due date of a todo item
        updateTodo(todoId) {
            const todo = this.todos.find((item) => item.id === todoId);
        
            if (todo) {
                fetch(`/Case/controller/todo/${todoId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ task: todo.task, dueDate: todo.dueDate }) 
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.editingTodoId = null;
                        this.editingDueDateTodoId = null; 
                    } else {
                        console.error(data.error);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            }
        },
        

        // Opens the delete confirmation modal and sets the todoId to be deleted
        deleteTodoModal(todoId) {
            this.isModalOpen = true;
            this.todoToDelete = todoId;
        },

        // Deletes a todo item after confirming the action
        deleteConfirmed(todoId) {
            this.isModalOpen = false;
            if (todoId) {
                fetch('/Case/controller/todo', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: todoId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.fetchTodos();
                    } else {
                        console.error(data.error);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            }
        },

        // Closes the delete confirmation modal and resets the todoId to be deleted
        closeModal() {
            this.isModalOpen = false;
            this.todoToDelete = null;
        },
    };
}

