<?php $this->load->view('todos/header'); ?>

        <h1>Edit Todo</h1>

        <div id="error-container">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error"><?php echo $errors; ?></div>
            <?php endif; ?>
        </div>

        <?php echo form_open('update-todo/' . $todo['id'], array('id' => 'update-form')); ?>
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required maxlength="255"
                       value="<?php echo html_escape($todo['title']); ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo html_escape($todo['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="<?php echo TODO_PRIORITY_LOW; ?>" <?php echo $todo['priority'] == TODO_PRIORITY_LOW ? 'selected' : ''; ?>>Low</option>
                    <option value="<?php echo TODO_PRIORITY_MEDIUM; ?>" <?php echo $todo['priority'] == TODO_PRIORITY_MEDIUM ? 'selected' : ''; ?>>Medium</option>
                    <option value="<?php echo TODO_PRIORITY_HIGH; ?>" <?php echo $todo['priority'] == TODO_PRIORITY_HIGH ? 'selected' : ''; ?>>High</option>
                </select>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date"
                       value="<?php echo html_escape($todo['due_date']); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Update Todo</button>
                <a href="<?php echo site_url('todos'); ?>" class="button button-secondary">Cancel</a>
            </div>
        <?php echo form_close(); ?>

<?php $this->load->view('todos/footer'); ?>
