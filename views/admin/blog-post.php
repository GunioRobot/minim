<?php
require_once '../../lib/minim.php';
require_once minim()->lib('breve-refactor');
require_once minim()->lib('defer');
require_once minim()->lib('pagination');
require_once minim()->models('blog');

$action = @$_REQUEST['action'];
$id = (int) @$_REQUEST['id'];

if ($id)
{
    $post = breve('BlogPost')->all()->filter(array(
        'id__eq' => $id
    ));
    if (!$post->items)
    {
        minim()->render_404();
    }
    $post = $post->items[0];
}
else
{
    $post = NULL;
}

if ($action == 'delete')
{
    $post->delete();
    minim()->user_message("Deleted post \"{$post->title}\"");
    minim()->redirect('admin/blog');
}

$errors = NULL;
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // save post
    $post = breve('BlogPost')->from($_POST);
    $post->author = 1;
    if ($post->isValid())
    {
        $post->save();
        minim()->user_message("Saved post \"{$post->title}\"");
        minim()->redirect('admin/blog');
    }
    else
    {
        $errors = $post->errors();
    }
}

minim()->render('admin/blog-post-form', array(
    'create' => is_null($post),
    'post' => $post,
    'errors' => $errors,
));