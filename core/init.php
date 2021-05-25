<?php

session_start();



$GLOBALS['config'] = array(

    'mysql' => array(

        'host' => 'localhost',

        'username' => 'root',

        'password' => '',

        'db' => 'diplomas'

    ),

    'session' => array(

        'session_name' => 'user',

        'token_name' => 'token',

        'redirect_login' => './index.php'

    )

);



spl_autoload_register(function($class) {

    require_once 'classes/' . $class . '.php';

});



require_once 'functions/sanitize.php';