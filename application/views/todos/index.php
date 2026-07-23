<?php $this->load->view('todos/header'); ?>

        <div class="header-actions">
            <h1>Todo List</h1>
            <a href="<?php echo site_url('create-todo-page'); ?>" class="button button-primary">+ Create New Todo</a>
        </div>

        <?php
        $success_msg = $this->session->flashdata('success');
        $error_msg = $this->session->flashdata('error');
        ?>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo html_escape($success_msg); ?></div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo html_escape($error_msg); ?></div>
        <?php endif; ?>

        <?php if (empty($todos)): ?>
            <div class="empty-state">
                <p>No todos yet. Create your first todo!</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todos as $todo): ?>
                        <tr>
                            <td>
                                <span class="<?php echo $todo['is_completed'] ? 'status-completed' : 'status-pending'; ?>">
                                    <?php echo $todo['is_completed'] ? '✓ Done' : '● Pending'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo site_url('todos/' . $todo['id']); ?>">
                                    <?php echo html_escape($todo['title']); ?>
                                </a>
                            </td>
                            <td class="priority-<?php echo html_escape($todo['priority']); ?>">
                                <?php echo html_escape(ucfirst($todo['priority'])); ?>
                            </td>
                            <td>
                                <?php echo $todo['due_date'] ? date('M d, Y', strtotime($todo['due_date'])) : '-'; ?>
                            </td>
                            <td class="actions">
                                <?php echo form_open('toggle-todo/' . $todo['id'], array('class' => 'toggle-form')); ?>
                                    <button type="submit" class="button button-success button-small">
                                        <?php echo $todo['is_completed'] ? 'Undo' : 'Complete'; ?>
                                    </button>
                                <?php echo form_close(); ?>
                                <a href="<?php echo site_url('todos/edit/' . $todo['id']); ?>" class="button button-warning button-small">Edit</a>
                                <?php echo form_open('delete-todo/' . $todo['id'], array('class' => 'delete-form')); ?>
                                    <button type="submit" class="button button-danger button-small">Delete</button>
                                <?php echo form_close(); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo site_url('todos/page/' . ($current_page - 1)); ?>" class="button button-small">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="<?php echo site_url('todos/page/' . $i); ?>"
                       class="button button-small <?php echo $i === $current_page ? 'button-primary' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo site_url('todos/page/' . ($current_page + 1)); ?>" class="button button-small">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>

<?php $this->load->view('todos/footer'); ?>
