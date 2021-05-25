<?php
require_once 'core/init.php';
$user = new User();

if(!$user->isLoggedIn()) {
    $user = null;
    Redirect::to("login.php");
} else {
    if (Input::get('search_ajax') && Input::get('sender')) {
        $data = Input::get('search_ajax');
        if (!preg_match('/^([a-z\sÁÉÍÓÚñáéíóúÑ]{1,50})$/i', $data)) {
            echo false;
        } else {
            $sender = Input::get('sender');
            $table = '';
            if ($sender == "nombreAlumno") {
                $table = "alumnos";
            } else if ($sender == "nombreProfesor") {
                $table = "profesores";
            } else if ($sender == "nombreCurso") {
                $table = "cursos";
            } else if ($sender == "nombreDiplomatura") {
                $table = "diplomaturas";
            } else {
                die();
            }
            $search = new Search();
            $results = $search->search($data, $table, 0, 10);
            echo json_encode($results);
        }
    } else {
        echo false;
    }
}