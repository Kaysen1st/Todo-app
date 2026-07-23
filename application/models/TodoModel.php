<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * DOCU: Model for todos table operations <br>
 * Triggered by: TodoService (all CRUD methods) <br>
 * Last Updated Date: July 17, 2026
 * @author Sam
 */
class TodoModel extends CI_Model
{
    /**
     * DOCU: Validate column names against allowlist before SQL interpolation <br>
     * Triggered by: fetchTodos() <br>
     * Last Updated Date: July 17, 2026
     * @param string $select_columns - Comma-separated column list
     * @param array $where_conditions - WHERE clause keys
     * @param array $order_by_rules - ORDER BY clause keys
     * @return void
     * @author Sam
     */
    private function validateColumnNames($select_columns, $where_conditions, $order_by_rules)
    {
        $allowed_columns = array_map('trim', explode(',', TODO_FETCH_COLUMNS));

        $select_parts = array_map('trim', explode(',', $select_columns));
        foreach ($select_parts as $col) {
            if (!in_array($col, $allowed_columns)) {
                throw new Exception('Invalid select column: ' . $col);
            }
        }
        foreach (array_keys($where_conditions) as $col) {
            if (!in_array($col, $allowed_columns)) {
                throw new Exception('Invalid where column: ' . $col);
            }
        }
        foreach (array_keys($order_by_rules) as $col) {
            if (!in_array($col, $allowed_columns)) {
                throw new Exception('Invalid order_by column: ' . $col);
            }
        }
    }

    /**
     * DOCU: Fetch todo records with raw SQL query building <br>
     * Triggered by: TodoService.getTodosPaginated() <br>
     * Last Updated Date: July 17, 2026
     * @param array $fetch_params - Query parameters (select, where, order_by, limit, offset)
     * @return array - Fetched records
     * @author Sam
     */
    public function fetchTodos($fetch_params = array())
    {
        $select_columns = isset($fetch_params['select']) ? $fetch_params['select'] : TODO_FETCH_COLUMNS;
        $where_conditions = isset($fetch_params['where']) ? $fetch_params['where'] : array();
        $order_by_rules = isset($fetch_params['order_by']) ? $fetch_params['order_by'] : array();
        $query_limit = isset($fetch_params['limit']) ? (int) $fetch_params['limit'] : NULL;
        $query_offset = isset($fetch_params['offset']) ? (int) $fetch_params['offset']: NULL;

        if (!isset($fetch_params['with_archived']) || !$fetch_params['with_archived']) {
            $where_conditions['is_archived'] = TODO_ACTIVE;
        }

        $this->validateColumnNames($select_columns, $where_conditions, $order_by_rules);

        $fetch_todos_query = 'SELECT ' . $select_columns . ' FROM todos';
        $fetch_todos_query_params = array();

        if (!empty($where_conditions)) {
            $first = TRUE;
            foreach ($where_conditions as $column => $value) {
                $fetch_todos_query .= $first ? ' WHERE ' : ' AND ';
                $fetch_todos_query .= $column . ' = ?';
                $fetch_todos_query_params[] = $value;
                $first = FALSE;
            }
        }

        if (!empty($order_by_rules)) {
            $order_clauses = array();
            foreach ($order_by_rules as $column => $direction) {
                $order_clauses[] = $column . ' ' . (strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC');
            }
            $fetch_todos_query .= ' ORDER BY ' . implode(', ', $order_clauses);
        }

        if ($query_limit !== NULL && $query_limit > 0) {
            $fetch_todos_query .= ' LIMIT ' . (int) $query_limit;
            if ($query_offset !== NULL && $query_offset > 0) {
                $fetch_todos_query .= ' OFFSET ' . (int) $query_offset;
            }
        }

        $fetch_todos_result = $this->db->query($fetch_todos_query, $fetch_todos_query_params);

        if ($fetch_todos_result === FALSE) {
            throw new Exception('Fetch query failed: ' . $this->db->error()['message']);
        }

        return $fetch_todos_result->result_array();
    }

    /**
     * DOCU: Get a single todo by ID <br>
     * Triggered by: TodoService.getTodoById(), createTodo(), updateTodo(), deleteTodo(), toggleTodo() <br>
     * Last Updated Date: July 17, 2026
     * @param int $todo_id - Todo ID
     * @return array|null - Todo record or null
     * @author Sam
     */
    public function getTodoById($todo_id)
    {
        $get_todo_query = 'SELECT ' . TODO_FETCH_COLUMNS . ' FROM todos WHERE id = ? AND is_archived = ?';
        $get_todo_result = $this->db->query($get_todo_query, array($todo_id, TODO_ACTIVE));

        if ($get_todo_result === FALSE) {
            throw new Exception('Fetch query failed: ' . $this->db->error()['message']);
        }

        $get_todo_row = $get_todo_result->row_array();

        return empty($get_todo_row) ? null : $get_todo_row;
    }

    /**
     * DOCU: Count all active (non-archived) todos <br>
     * NOTE: Returns 0 on query failure instead of throwing — the controller's <br>
     *       index() has no try/catch, so this provides graceful degradation. <br>
     * Triggered by: TodoService.getTodosPaginated() <br>
     * Last Updated Date: July 17, 2026
     * @return int - Total count of active todos
     * @author Sam
     */
    public function countTodos()
    {
        $count_todos_query = 'SELECT COUNT(*) AS total FROM todos WHERE is_archived = ?';
        $count_todos_result = $this->db->query($count_todos_query, array(TODO_ACTIVE));

        if ($count_todos_result === FALSE) {
            return 0;
        }

        $count_todos_row = $count_todos_result->row_array();
        return isset($count_todos_row['total']) ? (int) $count_todos_row['total'] : 0;
    }

    /**
     * DOCU: Insert a new todo record <br>
     * NOTE: Hardcoded to exactly 4 columns (title, description, priority, due_date). <br>
     *       Service layer must provide all four keys before calling this method. <br>
     * Triggered by: TodoService.createTodo() <br>
     * Last Updated Date: July 17, 2026
     * @param array $todo_data - Todo data with title, description, priority, due_date
     * @return int - Inserted record ID
     * @author Sam
     */
    public function insertTodo($todo_data)
    {
        $insert_todo_query = 'INSERT INTO todos (title, description, priority, due_date) VALUES (?, ?, ?, ?)';
        $insert_todo_result = $this->db->query($insert_todo_query, array(
            $todo_data['title'],
            $todo_data['description'],
            $todo_data['priority'],
            $todo_data['due_date']
        ));

        if ($insert_todo_result === FALSE) {
            throw new Exception('Insert failed: ' . $this->db->error()['message']);
        }

        if ($this->db->affected_rows() === 0) {
            throw new Exception('Insert affected 0 rows');
        }

        return $this->db->insert_id();
    }

    /**
     * DOCU: Update a todo record by ID <br>
     * NOTE: Hardcoded to exactly 4 columns (title, description, priority, due_date). <br>
     *       Service layer must provide all four keys before calling this method. <br>
     * Triggered by: TodoService.updateTodo() <br>
     * Last Updated Date: July 17, 2026
     * @param int $todo_id - Todo ID
     * @param array $update_data - Data with title, description, priority, due_date
     * @return bool - True if query executed successfully
     * @author Sam
     */
    public function updateTodo($todo_id, $update_data)
    {
        $update_todo_query = 'UPDATE todos SET title = ?, description = ?, priority = ?, due_date = ? WHERE id = ?';
        $update_todo_result = $this->db->query($update_todo_query, array(
            $update_data['title'],
            $update_data['description'],
            $update_data['priority'],
            $update_data['due_date'],
            $todo_id
        ));

        if ($update_todo_result === FALSE) {
            throw new Exception('Update failed: ' . $this->db->error()['message']);
        }

        return $this->db->affected_rows() > 0;
    }

    /**
     * DOCU: Soft delete a todo by setting is_archived = 1 <br>
     * Triggered by: TodoService.deleteTodo() <br>
     * Last Updated Date: July 14, 2026
     * @param int $todo_id - Todo ID
     * @return bool - True if at least one row was updated
     * @author Sam
     */
    public function deleteTodo($todo_id)
    {
        $delete_todo_query = 'UPDATE todos SET is_archived = ? WHERE id = ? AND is_archived = ?';
        $delete_todo_result = $this->db->query($delete_todo_query, array(TODO_ARCHIVED, $todo_id, TODO_ACTIVE));

        if ($delete_todo_result === FALSE) {
            throw new Exception('Delete failed: ' . $this->db->error()['message']);
        }

        if ($this->db->affected_rows() === 0) {
            throw new Exception('Delete affected 0 rows');
        }

        return TRUE;
    }

    /**
     * DOCU: Toggle is_completed atomically (single SQL statement) <br>
     * Triggered by: TodoService.toggleTodo() <br>
     * Last Updated Date: July 9, 2026
     * @param int $todo_id - Todo ID
     * @return int - 1 if completed, 0 if pending, -1 if no row affected
     * @author Sam
     */
    public function toggleCompletedAtomic($todo_id)
    {
        $toggle_todo_query = 'UPDATE todos SET is_completed = 1 - is_completed WHERE id = ? AND is_archived = ?';
        $toggle_todo_result = $this->db->query($toggle_todo_query, array($todo_id, TODO_ACTIVE));

        if ($toggle_todo_result === FALSE) {
            throw new Exception('Toggle update failed: ' . $this->db->error()['message']);
        }

        if ($this->db->affected_rows() === 0) {
            return TODO_TOGGLE_ERROR;
        }

        $select_todo_query = 'SELECT is_completed FROM todos WHERE id = ?';
        $toggle_todo_select_result = $this->db->query($select_todo_query, array($todo_id));

        if ($toggle_todo_select_result === FALSE) {
            throw new Exception('Toggle query failed: ' . $this->db->error()['message']);
        }

        $toggle_todo_row = $toggle_todo_select_result->row_array();

        return isset($toggle_todo_row['is_completed']) ? (int) $toggle_todo_row['is_completed'] : TODO_TOGGLE_ERROR;
    }
}
