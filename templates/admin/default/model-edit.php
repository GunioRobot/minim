<?php minim()->extend('base') ?>

<?php minim()->def_block('title') ?>Admin<?php minim()->end_block('title') ?>

<?php minim()->def_block('body_class') ?>admin<?php minim()->end_block('body_class') ?>

<?php minim()->def_block('page_content') ?>
    <h1>Edit <?php echo $model_name ?></h1>
    <ul class="messages">
    <?php foreach (minim()->user_messages() as $msg): ?>
      <li><?php echo $msg ?></li>
    <?php endforeach ?>
    </ul>
    <form id="model-edit-form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
<?php if ($errors): ?>
    <ul class="errors">
    <?php foreach ($errors as $error): ?>
     <li><?php echo $error ?></li>
    <?php endforeach ?>
    </ul>
<?php endif ?>
<?php foreach ($form->_fields as $field => $val): ?>
    <div class="form-row">
      <?php echo $form->$field->label ?>
      <?php echo $form->$field->render() ?>
    </div>
<?php endforeach ?>
    <div class="form-row">
      <input type="submit" class="submit" value="Save">
    </div>
    </form>
<?php minim()->end_block('page_content') ?>