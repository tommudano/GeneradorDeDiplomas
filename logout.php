<?php
require_once 'core/init.php';

$user = new User();
if($user->isLoggedIn()) {
    Session::flash('login', 'success', 'Has cerrado la sesi&oacute;n correctamente');
    $user->logout();
}
$user = null;

Redirect::to('./login.php');