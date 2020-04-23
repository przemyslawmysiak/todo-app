<section class="todo-container">
  <ul id="todo-tasks">
    <li class="add-new">
<input type="checkbox" disabled>
    <input type="text" class="add-new" placeholder="Write your todo here" />
    </li>
<?php if ( !empty($this->get_todo_list()) ) { ?>
  <?php foreach ( $this->get_todo_list() as $task ) { ?>
    <li class="tasks" data-id="<?php echo $task['task_id']; ?>">
    <input type="checkbox" <?php echo ($task['task_status'] ? 'checked' : ''); ?>/>
    <input type="text" class="tasks" value="<?php echo $task['task_title']; ?>"/>
    </li>
  <?php
  }
}
  ?>
  </ul>
</section>
