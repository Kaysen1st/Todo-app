<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DOCU: Business logic layer for todo operations <br>
 * Triggered by: Todos controller <br>
 * Last Updated Date: July 17, 2026
 * @author Sam
 */
class TodoService
{
    protected $ci;
    protected $todo_model;
    protected $todo_validation;

    /**
     * DOCU: Default constructor <br>
     * Triggered: Automatically when loaded as library <br>
     * Last Updated Date: July 14, 2026
     * @author Sam
     */
    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->model('TodoModel', 'Todo_model');
        $this->ci->load->library('TodoValidation', null, 'todo_validation');

        $this->todo_model = $this->ci->Todo_model;
        $this->todo_validation = $this->ci->todo_validation;
    }

    /**
     * DOCU: Build a clean record array from raw input data <br>
     * Triggered by: createTodo(), updateTodo() <br>
     * Last Updated Date: July 23, 2026
     * @param array $data - Raw input data
     * @return array - Cleaned record with title, description, priority, due_date
     * @author Sam
     */
    private function buildTodoRecord($data)
    {
        return array(
            'title' => trim($data['title']),
            'description' => isset($data['description']) ? trim($data['description']) : '',
            'priority' => isset($data['priority']) ? $data['priority'] : TODO_PRIORITY_MEDIUM,
            'due_date' => isset($data['due_date']) && !empty($data['due_date']) ? $data['due_date'] : null
        );
    }

    /**
     * DOCU: Get paginated todos <br>
     * Triggered by: Todos.index() <br>
     * Last Updated Date: July 17, 2026
     * @param int $page - Current page number
     * @param int $per_page - Items per page
     * @return array - Todos list with pagination metadata
     * @author Sam
     */
    public function getTodosPaginated($page = 1, $per_page = 10)
    {
        $offset = ((int) $page - 1) * (int) $per_page;
        $offset = $offset < 0 ? 0 : $offset;

        $todos = $this->todo_model->fetchTodos(array(
            'limit' => (int) $per_page,
            'offset' => $offset,
            'order_by' => array('created_at' => 'DESC')
        ));

        $total_count = $this->todo_model->countTodos();
        $total_pages = ceil($total_count / (int) $per_page);

        return array(
            'todos' => $todos,
            'total_count' => (int) $total_count,
            'total_pages' => (int) $total_pages
        );
    }

    /**
     * DOCU: Get a single todo by ID <br>
     * Triggered by: Controller methods (view, edit, update) <br>
     * Last Updated Date: July 17, 2026
     * @param int $todo_id - Todo ID
     * @return array|null - Todo record or null
     * @author Sam
     */
    public function getTodoById($todo_id)
    {
        $id_validation = $this->todo_validation->validateTodoId($todo_id);

        if (!$id_validation['is_valid']) {
            return null;
        }

        return $this->todo_model->getTodoById((int) $todo_id);
    }

    /**
     * DOCU: Create a new todo <br>
     * Triggered by: Todos.store() <br>
     * Last Updated Date: July 9, 2026
     * @param array $data - Todo data
     * @return array - Result with 'success' bool and 'message' string
     * @author Sam
     */
    public function createTodo($data)
    {
        try {
            $validation = $this->todo_validation->validateTodoData($data);

            if (!$validation['is_valid']) {
                return array(
                    'success' => false,
                    'message' => implode(' ', $validation['errors'])
                );
            }

            $record = $this->buildTodoRecord($data);

            $new_id = $this->todo_model->insertTodo($record);

            if (!$new_id) {
                return array(
                    'success' => false,
                    'message' => 'Failed to create todo.'
                );
            }

            return array(
                'success' => true,
                'message' => 'Todo created successfully.',
                'todo' => $this->todo_model->getTodoById($new_id)
            );
        } catch (Exception $exception) {
            log_message('error', 'TodoService::createTodo - ' . $exception->getMessage());
            throw $exception;
        }
    }

    /**
     * DOCU: Update an existing todo <br>
     * Triggered by: Todos.update() <br>
     * Last Updated Date: July 9, 2026
     * @param int $todo_id - Todo ID
     * @param array $data - Updated todo data
     * @return array - Result with 'success' bool and 'message' string
     * @author Sam
     */
    public function updateTodo($todo_id, $data)
    {
        try {
            $id_validation = $this->todo_validation->validateTodoId($todo_id);

            if (!$id_validation['is_valid']) {
                return array(
                    'success' => false,
                    'message' => implode(' ', $id_validation['errors'])
                );
            }

            $existing = $this->todo_model->getTodoById((int) $todo_id);

            if (!$existing) {
                return array(
                    'success' => false,
                    'message' => 'Todo not found.'
                );
            }

            $validation = $this->todo_validation->validateTodoData($data);

            if (!$validation['is_valid']) {
                return array(
                    'success' => false,
                    'message' => implode(' ', $validation['errors'])
                );
            }

            $record = $this->buildTodoRecord($data);

            if (!$this->todo_model->updateTodo((int) $todo_id, $record)) {
                return array(
                    'success' => false,
                    'message' => 'No changes detected.'
                );
            }

            return array(
                'success' => true,
                'message' => 'Todo updated successfully.',
                'todo' => $this->todo_model->getTodoById((int) $todo_id)
            );
        } catch (Exception $exception) {
            log_message('error', 'TodoService::updateTodo - ' . $exception->getMessage());
            throw $exception;
        }
    }

    /**
     * DOCU: Delete a todo by ID <br>
     * Triggered by: Todos.delete() <br>
     * Last Updated Date: July 9, 2026
     * @param int $todo_id - Todo ID
     * @return array - Result with 'success' bool and 'message' string
     * @author Sam
     */
    public function deleteTodo($todo_id)
    {
        try {
            $id_validation = $this->todo_validation->validateTodoId($todo_id);

            if (!$id_validation['is_valid']) {
                return array(
                    'success' => false,
                    'message' => implode(' ', $id_validation['errors'])
                );
            }

            if (!$this->todo_model->getTodoById((int) $todo_id)) {
                return array(
                    'success' => false,
                    'message' => 'Todo not found.'
                );
            }

            $this->todo_model->deleteTodo((int) $todo_id);

            return array(
                'success' => true,
                'message' => 'Todo archived successfully.'
            );
        } catch (Exception $exception) {
            log_message('error', 'TodoService::deleteTodo - ' . $exception->getMessage());
            throw $exception;
        }
    }

    /**
     * DOCU: Toggle is_completed status atomically <br>
     * Triggered by: Todos.toggle() <br>
     * Last Updated Date: July 9, 2026
     * @param int $todo_id - Todo ID
     * @return array - Result with 'success' bool and 'message' string
     * @author Sam
     */
    public function toggleTodo($todo_id)
    {
        try {
            $id_validation = $this->todo_validation->validateTodoId($todo_id);

            if (!$id_validation['is_valid']) {
                return array(
                    'success' => false,
                    'message' => implode(' ', $id_validation['errors'])
                );
            }

            $existing = $this->todo_model->getTodoById((int) $todo_id);

            if (!$existing) {
                return array(
                    'success' => false,
                    'message' => 'Todo not found.'
                );
            }

            $status = $this->todo_model->toggleCompletedAtomic((int) $todo_id);

            if ($status === TODO_TOGGLE_ERROR) {
                return array(
                    'success' => false,
                    'message' => 'Failed to toggle todo status.'
                );
            }

            $existing['is_completed'] = $status;

            return array(
                'success' => true,
                'message' => $status ? 'Todo marked as completed.' : 'Todo marked as pending.',
                'todo' => $existing
            );
        } catch (Exception $exception) {
            log_message('error', 'TodoService::toggleTodo - ' . $exception->getMessage());
            throw $exception;
        }
    }
}
