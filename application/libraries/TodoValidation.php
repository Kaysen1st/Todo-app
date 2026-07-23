<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DOCU: Validation helper for todo data <br>
 * Triggered by: TodoService functions <br>
 * Last Updated Date: July 17, 2026
 * @author Sam
 */
class TodoValidation
{
    /**
     * DOCU: Validate todo input data <br>
     * Triggered by: TodoService before create/update <br>
     * Last Updated Date: July 9, 2026
     * @param array $data - Todo data to validate
     * @return array - Array with 'is_valid' bool and 'errors' array
     * @author Sam
     */
    public function validateTodoData($data)
    {
        $validation_result = array(
            'is_valid' => true,
            'errors' => array()
        );

        $title = isset($data['title']) ? trim($data['title']) : '';

        if (empty($title)) {
            $validation_result['is_valid'] = false;
            $validation_result['errors'][] = 'Title is required.';
        } elseif (strlen($title) < TODO_MIN_TITLE_LENGTH) {
            $validation_result['is_valid'] = false;
            $validation_result['errors'][] = 'Title must be at least ' . TODO_MIN_TITLE_LENGTH . ' characters.';
        } elseif (strlen($title) > TODO_MAX_TITLE_LENGTH) {
            $validation_result['is_valid'] = false;
            $validation_result['errors'][] = 'Title must not exceed ' . TODO_MAX_TITLE_LENGTH . ' characters.';
        }

        if (isset($data['priority']) && !empty($data['priority'])) {
            $allowed_priorities = array(TODO_PRIORITY_LOW, TODO_PRIORITY_MEDIUM, TODO_PRIORITY_HIGH);

            if (!in_array($data['priority'], $allowed_priorities)) {
                $validation_result['is_valid'] = false;
                $validation_result['errors'][] = 'Priority must be low, medium, or high.';
            }
        }

        if (isset($data['due_date']) && !empty($data['due_date'])) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['due_date'])) {
                $validation_result['is_valid'] = false;
                $validation_result['errors'][] = 'Due date must be in YYYY-MM-DD format.';
            } else {
                $parts = explode('-', $data['due_date']);

                if (!checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
                    $validation_result['is_valid'] = false;
                    $validation_result['errors'][] = 'Due date is not a valid calendar date.';
                } elseif (strtotime($data['due_date']) < strtotime(date('Y-m-d'))) {
                    $validation_result['is_valid'] = false;
                    $validation_result['errors'][] = 'Due date must be today or a future date.';
                }
            }
        }

        return $validation_result;
    }

    /**
     * DOCU: Validate that a todo ID is a positive integer <br>
     * Triggered by: TodoService before get/update/delete/toggle <br>
     * Last Updated Date: July 17, 2026
     * @param int $todo_id - Todo ID to validate
     * @return array - Array with 'is_valid' bool and 'errors' array
     * @author Sam
     */
    public function validateTodoId($todo_id)
    {
        $validation_result = array(
            'is_valid' => true,
            'errors' => array()
        );

        if (!is_numeric($todo_id) || (int) $todo_id < 1) {
            $validation_result['is_valid'] = false;
            $validation_result['errors'][] = 'Todo ID must be a positive integer.';
        }

        return $validation_result;
    }
}