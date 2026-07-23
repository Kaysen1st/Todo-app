<?php $this->load->view('todos/header'); ?>

        <h1>Create Todo</h1>

        <div id="error-container">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error"><?php echo $errors; ?></div>
            <?php endif; ?>
        </div>

        <?php echo form_open('create-todo', array('id' => 'create-form')); ?>
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required maxlength="255"
                       value="<?php echo isset($old_input['title']) ? html_escape($old_input['title']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo isset($old_input['description']) ? html_escape($old_input['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="<?php echo TODO_PRIORITY_LOW; ?>" <?php echo (isset($old_input['priority']) && $old_input['priority'] == TODO_PRIORITY_LOW) ? 'selected' : ''; ?>>Low</option>
                    <option value="<?php echo TODO_PRIORITY_MEDIUM; ?>" <?php echo (!isset($old_input['priority']) || $old_input['priority'] == TODO_PRIORITY_MEDIUM) ? 'selected' : ''; ?>>Medium</option>
                    <option value="<?php echo TODO_PRIORITY_HIGH; ?>" <?php echo (isset($old_input['priority']) && $old_input['priority'] == TODO_PRIORITY_HIGH) ? 'selected' : ''; ?>>High</option>
                </select>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date"
                       value="<?php echo isset($old_input['due_date']) ? html_escape($old_input['due_date']) : ''; ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Create Todo</button>
                <a href="<?php echo site_url('todos'); ?>" class="button button-secondary">Cancel</a>
            </div>
        <?php echo form_close(); ?>

<?php $this->load->view('todos/footer'); ?>
