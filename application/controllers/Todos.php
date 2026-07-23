<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Todos
 * Controller for todo CRUD operations
 * Last Updated Date: July 9, 2026
 * @author Sam
 */
class Todos extends CI_Controller
{
    public $todo_service;

    /**
     * DOCU: Default constructor <br>
     * Triggered: Automatically on every request to this controller <br>
     * Last Updated Date: July 9, 2026
     * @author Sam
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('TodoService', null, 'todo_service');
    }

    /**
     * DOCU: Display paginated list of todos <br>
     * Triggered by: GET /todos, GET /todos/page/(:num) <br>
     * Last Updated Date: July 17, 2026
     * @param int $page - Current page number (defaults to 1)
     * @return void
     * @author Sam
     */
    public function index($page = 1)
    {
        $page = (int) $page;

        if ($page < 1) {
            redirect('todos');
            return;
        }

        $result = $this->todo_service->getTodosPaginated($page, TODO_DEFAULT_PER_PAGE);

        $view_data = array(
            'page_title' => 'Todo List',
            'container_class' => 'container-wide',
            'todos' => $result['todos'],
            'total_count' => $result['total_count'],
            'per_page' => TODO_DEFAULT_PER_PAGE,
            'current_page' => (int) $page,
            'total_pages' => $result['total_pages']
        );

        $this->load->view('todos/index', $view_data);
    }

    /**
     * DOCU: Show create todo form <br>
     * Triggered by: GET /todos/create <br>
     * Last Updated Date: July 9, 2026
     * @return void
     * @author Sam
     */
    public function create()
    {
        $old_input = $this->session->flashdata('old_input');
        $view_data = array(
            'page_title' => 'Create Todo',
            'old_input' => $old_input ? $old_input : array(),
            'errors' => ''
        );

        $this->load->view('todos/create', $view_data);
    }

    /**
     * DOCU: Store new todo <br>
     * Triggered by: POST /todos/store <br>
     * Last Updated Date: July 9, 2026
     * @return void
     * @author Sam
     */
    public function store()
    {
        try {
            $post_data = array(
                'title'       => $this->input->post('title', TRUE),
                'description' => $this->input->post('description', TRUE),
                'priority'    => $this->input->post('priority', TRUE),
                'due_date'    => $this->input->post('due_date', TRUE),
            );

            $result = $this->todo_service->createTodo($post_data);

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
                redirect('todos');
                return;
            }

            $this->session->set_flashdata('old_input', $post_data);
            $this->session->set_flashdata('error', $result['message']);
            redirect('todos/create');
        } catch (Exception $exception) {
            log_message('error', 'Todos::store - ' . $exception->getMessage());
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
            redirect('todos');
        }
    }

    /**
     * DOCU: View single todo <br>
     * Triggered by: GET /todos/(:num) <br>
     * Last Updated Date: July 9, 2026
     * @param int $todo_id - Todo ID
     * @return void
     * @author Sam
     */
    public function view($todo_id)
    {
        $todo_id = (int) $todo_id;

        try {
            $fetched_todo = $this->todo_service->getTodoById($todo_id);

            if (!$fetched_todo) {
                show_404();
                return;
            }

            $view_data = array(
                'page_title' => $fetched_todo['title'],
                'todo' => $fetched_todo
            );

            $this->load->view('todos/view', $view_data);
        } catch (Exception $exception) {
            log_message('error', 'Todos::view - ' . $exception->getMessage());
            show_404();
        }
    }

    /**
     * DOCU: Show edit todo form <br>
     * Triggered by: GET /todos/edit/(:num) <br>
     * Last Updated Date: July 9, 2026
     * @param int $todo_id - Todo ID
     * @return void
     * @author Sam
     */
    public function edit($todo_id)
    {
        $todo_id = (int) $todo_id;

        try {
            $fetched_todo = $this->todo_service->getTodoById($todo_id);

            if (!$fetched_todo) {
                show_404();
                return;
            }

            $old_input = $this->session->flashdata('old_input');
            $view_data = array(
                'page_title' => 'Edit Todo',
                'todo' => $old_input ? $old_input : $fetched_todo,
                'errors' => ''
            );

            $this->load->view('todos/edit', $view_data);
        } catch (Exception $exception) {
            log_message('error', 'Todos::edit - ' . $exception->getMessage());
            show_404();
        }
    }

    /**
     * DOCU: Update existing todo <br>
     * Triggered by: POST /todos/update/(:num) <br>
     * Last Updated Date: July 9, 2026
     * @param int $todo_id - Todo ID
     * @return void
     * @author Sam
     */
    public function update($todo_id)
    {
        $todo_id = (int) $todo_id;

        try {
            $existing_todo = $this->todo_service->getTodoById($todo_id);

            if (!$existing_todo) {
                show_404();
                return;
            }

            $post_data = array(
                'title'       => $this->input->post('title', TRUE),
                'description' => $this->input->post('description', TRUE),
                'priority'    => $this->input->post('priority', TRUE),
                'due_date'    => $this->input->post('due_date', TRUE),
            );

            $result = $this->todo_service->updateTodo($todo_id, $post_data);

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
                redirect('todos');
                return;
            }

            $this->session->set_flashdata('old_input', array_merge($existing_todo, $post_data));
            $this->session->set_flashdata('error', $result['message']);
            redirect('todos/edit/' . $todo_id);
        } catch (Exception $exception) {
            log_message('error', 'Todos::update - ' . $exception->getMessage());
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
            redirect('todos');
        }
    }

    /**
     * DOCU: Delete a todo <br>
     * Triggered by: POST /todos/delete/(:num) <br>
     * Last Updated Date: July 9, 2026
     * @param int $todo_id - Todo ID
     * @return void
     * @author Sam
     */
    public function delete($todo_id)
    {
        $todo_id = (int) $todo_id;

        try {
            $result = $this->todo_service->deleteTodo($todo_id);

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
            } else {
                $this->session->set_flashdata('error', $result['message']);
            }
            redirect('todos');
        } catch (Exception $exception) {
            log_message('error', 'Todos::delete - ' . $exception->getMessage());
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
            redirect('todos');
        }
    }

    /**
     * DOCU: Toggle todo completed status <br>
     * Triggered by: POST /todos/toggle/(:num) <br>
     * Last Updated Date: July 9, 2026
     * @param int $todo_id - Todo ID
     * @return void
     * @author Sam
     */
    public function toggle($todo_id)
    {
        $todo_id = (int) $todo_id;

        try {
            $result = $this->todo_service->toggleTodo($todo_id);

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
            } else {
                $this->session->set_flashdata('error', $result['message']);
            }
            redirect('todos');
        } catch (Exception $exception) {
            log_message('error', 'Todos::toggle - ' . $exception->getMessage());
            $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
            redirect('todos');
        }
    }
}
