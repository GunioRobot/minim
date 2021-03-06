<?php $this->extend('admin_base') ?>

<?php $this->set('page_content') ?>
    <form id="model-edit-form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
<?php if ($errors): ?>
    <ul class="errors">
    <?php foreach ($errors as $error): ?>
     <li><?php echo $error ?></li>
    <?php endforeach ?>
    </ul>
<?php endif ?>
<?php foreach ($form->_fields as $field => $val): ?>
    <?php echo $form->$field->render_as_div() ?>
<?php endforeach ?>
    <div class="form-row">
      <input type="submit" class="submit" value="Save">
    </div>
    </form>
<?php $this->end() ?>

<?php $this->set('title') ?>Admin<?php $this->end() ?>
