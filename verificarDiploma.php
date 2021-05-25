<?php
require_once 'core/init.php';

if (Input::get('id') !== '') {
    $diploma = new Diploma();
    $id = Hash::decrypt(Input::get('id'));
    $data = $diploma->getById('diplomas', $id);
    if (!empty($data)) {
        $diploma = Diploma::conInfo($data);
        $idAlumno = $diploma->getIdAlumno();
        $alumno = new Alumno();
        $alumnoData = $alumno->getById('alumnos', $idAlumno);
        $dniAlumno = Hash::decrypt($alumnoData->dni);
        $nombreAlumno = $diploma->getAlumno();
        $nombreProfesor = $diploma->getProfesor();
        $nombreCurso = $diploma->getCurso();
        $diplomatura = $diploma->getDiplomatura();
        cargarPagina($dniAlumno, $nombreAlumno, $nombreProfesor, $nombreCurso, $diplomatura);
    } else {
        Redirect::to("verDiplomas.php");
    }
} else {
    Redirect::to("verDiplomas.php");
}

function cargarPagina ($dni, $alumno, $profesor, $curso, $diplomatura) {
    $incluibles = new Incluibles();
$menu = $incluibles->menu();
    $pagina = <<<DIPLOMA
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Diplomas</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    </head>
    <body>
        {$menu}
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="mx-auto mt-3">
                        <h3>| Estudiante</h3>
                        <p> Estudiante:  <strong>{$alumno}</strong></p>
                        <p> D.N.I:  <strong>{$dni}</strong></p>
                        <p> Ha cumplido el curso: <strong>{$curso}</strong> de la <strong>Diplomatura en {$diplomatura}</strong></p>
                        <p> Profesor:  <strong>{$profesor}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
DIPLOMA;

    print($pagina);
}