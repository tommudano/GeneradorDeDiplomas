<?php
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn()) {
    $user = null;
    Redirect::to("login.php");
}

if (Input::get('nombreV') == '' || Input::get('apellidoV') == '' || Input::get('dniV') == '' || Input::get('id') == '') {
    Redirect::to("consultarEstudiante.php");
}

if (Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'dni' => array(
                'required' => true,
                'uniqueExcept' => array('alumnos', Input::get('id')),
                'typeValue' => 'int'
            ),
            'id' => array(
                'required' => true,
                'typeValue' => 'int'
            ),
            'nombre' => array(
                'required' => true,
                'regExp' => '/^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i'
            ),
            'apellido' => array(
                'required' => true,
                'regExp' => '/^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i'
            )
        ));
        
        if($validation->passed()) {
            $alumno = new Alumno();
            try {
                $alumno->actualizarPorId('alumnos', escape(Input::get('id')), array(
                    'dni' => Hash::encrypt(escape(Input::get('dni'))),
                    'nombre' => escape(Input::get('nombre')),
                    'apellido' => escape(Input::get('apellido'))
                ));

                Session::flash('alumnoActualizado', 'success', '¡El estudiante ha sido actualizado correctamente!');
            } catch(Exception $e) {
                Session::flash('alumnoActualizado', 'danger', $e->getMessage());
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
    $camposVal = array('dni', 'nombre', 'apellido');
    $errors = Session::formErrors($camposVal);
    $msjTag = Session::message('alumnoActualizado');

    $idAlumno = escape(Input::get('id'));
    $nombreAlumno = escape(Input::get('nombreV'));
    $apellidoAlumno = escape(Input::get('apellidoV'));
    $dniAlumno = escape(Input::get('dniV'));
    
    foreach ($camposVal as $campo) {
        $msjTag .= Session::message($campo);
    }
    
    if ($errors[0] || $msjTag !== '') {
        $used = Session::getUsedInputs($camposVal, true);
    } else {
        $used = Session::getUsedInputs($camposVal);
    }
    
    $used[0] = $used[0] == '' ? $dniAlumno : $used[0];
    $used[1] = $used[1] == '' ? $nombreAlumno : $used[1];
    $used[2] = $used[2] == '' ? $apellidoAlumno : $used[2];

    $token = Token::generate();

    $paginaAlumno = <<<ALUMNO
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Diplomas</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
            <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
            <script src="./Js/classValidation.js"></script>
            <script src="./Js/validacionAlumno.js"></script>
        </head>
        <body>
            {$menu}
            <div class="container mt-5">
                <h1 class="text-center">Actualizar Estudiante</h1>
                {$msjTag}
                <div class="col-md-12 mx-auto">
                    <form action="" method="POST" id="form" autocomplete="off">
                        <div class="form-group">
                            <label for="dni">Ingrese el DNI <span style="color:red;">*</span></label>
                            <input type="text" name="dni" class="form-control {$errors['dni'][0]}" id="dni" placeholder="DNI" value="{$used[0]}">
                        </div>

                        <div class="form-group">
                            <label for="nombre">Ingrese el Nombre <span style="color:red;">*</span></label>
                            <input type="text" name="nombre" class="form-control {$errors['nombre'][0]}" id="nombre" placeholder="Nombre" value="{$used[1]}">
                        </div>

                        <div class="form-group">
                            <label for="dni">Ingrese el Apellido <span style="color:red;">*</span></label>
                            <input type="text" name="apellido" class="form-control {$errors['apellido'][0]}" id="apellido" placeholder="Apellido" value="{$used[2]}">
                        </div>
                        <input type='hidden' name='id' value='{$idAlumno}'>
                        <input type='hidden' name='nombreV' value='{$nombreAlumno}'>
                        <input type='hidden' name='apellidoV' value='{$apellidoAlumno}'>
                        <input type='hidden' name='dniV' value='{$dniAlumno}'>
                        <input type="hidden" name="token" value="{$token}">
                        <button type="submit" class="btn btn-danger" id="btn" disabled>Actualizar Estudiante</button>
                        <a href="./consultarEstudiante.php" class="btn btn-warning">Volver</a>
                    </form>
                </div>
            </div>
        </body>
        </html>
ALUMNO;
    
    return $paginaAlumno;
}

$pagina = loadPage();
print($pagina);

