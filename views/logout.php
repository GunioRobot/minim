<?php
require_once '../config.php';

// unset user cookie
if (minim()->user())
{
    unset($_SESSION['user']);
}

$continue = @$_GET['continue'];
if (!$continue)
{
    $continue = 'home';
}

minim('routing')->redirect($continue);
