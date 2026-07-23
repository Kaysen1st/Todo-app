<?php $this->load->view('todos/header'); ?>

        <h1><?php echo html_escape($todo['title']); ?></h1>

        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value <?php echo $todo['is_completed'] ? 'status-completed' : 'status-pending'; ?>">
                <?php echo $todo['is_completed'] ? 'Completed' : 'Pending'; ?>
            </span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Priority</span>
            <span class="detail-value priority-<?php echo html_escape($todo['priority']); ?>">
                <?php echo html_escape(ucfirst($todo['priority'])); ?>
            </span>
        </div>

        <?php if ($todo['due_date']): ?>
            <div class="detail-row">
                <span class="detail-label">Due Date</span>
                <span class="detail-value"><?php echo date('F d, Y', strtotime($todo['due_date'])); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($todo['description']): ?>
            <div class="detail-row">
                <span class="detail-label">Description</span>
                <span class="detail-value"><?php echo nl2br(html_escape($todo['description'])); ?></span>
            </div>
        <?php endif; ?>

        <div class="detail-row">
            <span class="detail-label">Created</span>
            <span class="detail-value"><?php echo date('F d, Y h:i A', strtotime($todo['created_at'])); ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Last Updated</span>
            <span class="detail-value"><?php echo date('F d, Y h:i A', strtotime($todo['updated_at'])); ?></span>
        </div>

        <div class="form-actions">
            <a href="<?php echo site_url('todos/edit/' . $todo['id']); ?>" class="button button-warning">Edit</a>
            <a href="<?php echo site_url('todos'); ?>" class="button button-secondary">Back to List</a>
        </div>

<?php $this->load->view('todos/footer'); ?>
