<?php
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn()) {
    $user = null;
    Redirect::to("login.php");
}

if (Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'alumnoId' => array(
                'required' => true,
                'typeValue' => 'int'
            ),
            'profesorId' => array(
                'required' => true,
                'typeValue' => 'int'
            ),
            'cursoId' => array(
                'required' => true,
                'typeValue' => 'int'
            ), 
            'mes' => array(
                'required' => true,
                'regExp' => '/^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i'
            ),
            'anio' => array(
                'required' => true,
                'regExp' => '/(?:(?:20|21)[0-9]{2})/'
            )
        ));
        
        if($validation->passed()) {
            $diploma = new Diploma();
            try {
                $diploma->crear(array(
                    'idAlumno' => escape(Input::get('alumnoId')),
                    'idProfesor' => escape(Input::get('profesorId')),
                    'idCurso' => escape(Input::get('cursoId')),
                    'mes' => escape(Input::get('mes')),
                    'anio' => escape(Input::get('anio'))
                ));

                $idCreada = $diploma->getDiplomaId(Input::get('alumnoId'), Input::get('profesorId'), Input::get('cursoId'));
                Session::flash('diplomaCreado', 'success', '¡El diploma ha sido creado correctamente! <a href="./preDiploma.php?id=' . Hash::encrypt($idCreada) . '">Click aqu&iacute;</a> para ver.');
            } catch(Exception $e) {
                Session::flash('diplomaCreado', 'danger', $e->getMessage());
            }
        } 
        else {
            foreach($validation->errors() as $error => $value) {
                Session::flash($error, 'danger', $value);
            }
        }
    }
}

function loadPage() {
    $incluibles = new Incluibles();
    $menu = $incluibles->menu();
    // Generar errores
    $camposVal = array('alumnoId', 'profesorId', 'cursoId', 'mes', 'anio');
    $msjTag = Session::message('diplomaCreado');
    
    foreach ($camposVal as $campo) {
        $msjCampo = Session::message($campo);
        if ($msjCampo != '') {
            if (strpos($msjTag, $msjCampo) === false) {
                $msjTag .= $msjCampo;
            }
        }
    }

    $token = Token::generate();

    $paginaDiploma = <<<DIPLOMA
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Diplomas</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <script src="./Js/classValidation.js"></script>
        <script src="./Js/searchClass.js"></script>
        <script src="./Js/search.js"></script>
        <script src="./Js/validacionDiploma.js"></script>
        <style>
            .opcion {
                cursor: pointer;
                transition: background .25s ease-in-out;
            }
            .opcion:hover {
                cursor: pointer;
                background: #f2f2f2;
                transition: background .25s ease-in-out;
            }
        </style>
    </head>
    <body>
        {$menu}
        <div class="container mt-5">
            <h1 class="text-center">Crear Diploma</h1>
            {$msjTag}
            <div class="col-md-12 mx-auto">
                <form action="" method="POST" id="form" autocomplete="off">
                    <div class="form-group">
                        <label for="nombreAlumno">Alumno<span style="color:red;">*</span></label><br>
                        <button class="btn btn-outline-secondary dropdown-toggle btn-block" type="button" id="alumno" data-toggle="dropdown">Alumno</button>
                        <div class="dropdown-menu dropdown-primary btn-block px-2" id="alumnoDrop">
                            <input type="text" class="form-control busqueda noObligatorio" id="nombreAlumno" placeholder="Nombre Alumno">
                            <div class="containerOpciones"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombreProfesor">Profesor<span style="color:red;">*</span></label>
                        <button class="btn btn-outline-secondary dropdown-toggle btn-block" type="button" id="dropdownMenu1-1" data-toggle="dropdown">Profesor</button>
                        <div class="dropdown-menu dropdown-primary btn-block px-2" id="profesorDrop">
                            <input type="text" class="form-control busqueda noObligatorio" id="nombreProfesor" placeholder="Nombre Profesor">
                            <div class="containerOpciones"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombreCurso">Curso<span style="color:red;">*</span></label>
                        <button class="btn btn-outline-secondary dropdown-toggle btn-block" type="button" id="dropdownMenu1-1" data-toggle="dropdown">Curso</button>
                        <div class="dropdown-menu dropdown-primary btn-block px-2" id="cursoDrop">
                            <input type="text" class="form-control busqueda noObligatorio" id="nombreCurso" placeholder="Nombre Curso">
                            <div class="containerOpciones"></div>
                        </div>
                    </div>

                    <div class="row g-2 form-group">
                        <div class="col-sm form-group">
                            <label for="mes">Mes de Finalizaci&oacute;n<span style="color:red;">*</span></label>
                            <input type="text" class="form-control" name="mes" id="mes" placeholder="Mes de Finalizaci&oacute;n">
                        </div>
                        <div class="col-sm form-group">
                            <label for="anio">A&ntilde;o de finalizaci&oacute;n<span style="color:red;">*</span></label>
                            <input type="text" class="form-control" name="anio" id="anio" placeholder="A&ntilde;o de Finalizaci&oacute;n">
                        </div>
                    </div>

                    <input type="hidden" id="alumnoId" name="alumnoId">
                    <input type="hidden" id="profesorId" name="profesorId">
                    <input type="hidden" id="cursoId" name="cursoId">
                    <input type="hidden" name="token" value="{$token}">
                    <button type="submit" class="btn btn-danger" id="btn" disabled>Crear Diploma</button>
                    <a href="./verDiplomas.php" class="btn btn-warning">Cancelar</a>
                </form>
            </div>
        </div>
    </body>
    </html>
DIPLOMA;
    
    return $paginaDiploma;
}

$pagina = loadPage();
print($pagina);


?>
