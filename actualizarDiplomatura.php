<?php
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn() || $user->data()->privilegio != "admin") {
    $user = null;
    Redirect::to("login.php");
}

if (Input::get('nombreV') == '' || Input::get('id') == '') {
    Redirect::to("consultarDiplomatura.php");
}

if (Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'nombre' => array(
                'unique' => 'diplomaturas',
                'required' => true,
                'regExp' => '/^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i'
            ),
            'id' => array(
                'required' => true,
                'typeValue' => 'int'
            )
        ));
        
        if($validation->passed()) {
            $diplomatura = new Diplomatura();
            try {
                $diplomatura->actualizarPorId('diplomaturas', escape(Input::get('id')), array(
                    'nombre' => escape(Input::get('nombre'))
                ));

                Session::flash('diplomaturaActualizada', 'success', '¡La Diplomatura ha sido actualizada correctamente!');
            } catch(Exception $e) {
                Session::flash('diplomaturaActualizada', 'danger', $e->getMessage());
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
    $camposVal = array('nombre');
    $errors = Session::formErrors($camposVal);
    $msjTag = Session::message('diplomaturaActualizada');

    $idCurso = escape(Input::get('id'));
    $nombreCurso = escape(Input::get('nombreV'));
    
    foreach ($camposVal as $campo) {
        $msjTag .= Session::message($campo);
    }
    
    if ($errors[0] || $msjTag !== '') {
        $used = Session::getUsedInputs($camposVal, true);
    } else {
        $used = Session::getUsedInputs($camposVal);
    }
    
    $used[0] = $used[0] == '' ? $nombreCurso : $used[0];

    $token = Token::generate();

    $paginaDiplomatura = <<<CURSO
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
            <script src="./Js/validacionDiplomatura.js"></script>
        </head>
        <body>
            {$menu}
            <div class="container mt-5">
                <h1 class="text-center">Actualizar Diplomatura</h1>
                {$msjTag}
                <div class="col-md-12 mx-auto">
                    <form action="" method="POST" id="form" autocomplete="off">
                        <div class="form-group">
                            <label for="nombre">Ingrese el Nombre del Curso <span style="color:red;">*</span></label>
                            <input type="text" name="nombre" class="form-control {$errors['nombre'][0]}" id="nombre" placeholder="Nombre" value="{$used[0]}">
                        </div>

                        <input type='hidden' name='id' value='{$idCurso}'>
                        <input type='hidden' name='nombreV' value='{$nombreCurso}'>
                        <input type="hidden" name="token" value="{$token}">
                        <button type="submit" class="btn btn-danger" id="btn" disabled>Actualizar Diplomatura</button>
                        <a href="./consultarDiplomatura.php" class="btn btn-warning">Volver</a>
                    </form>
                </div>
            </div>
        </body>
        </html>
CURSO;
    
    return $paginaDiplomatura;
}

$pagina = loadPage();
print($pagina);

