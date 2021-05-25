<?php
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn() || $user->data()->privilegio != "admin") {
    $user = null;
    Redirect::to("login.php");
}

if (Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'nombre' => array(
                'required' => true,
                'unique' => 'diplomaturas',
                'regExp' => '/^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i'
            )
        ));
        
        if($validation->passed()) {
            $diplomatura = new Diplomatura();
            try {
                $diplomatura->crear(array(
                    'nombre' => escape(Input::get('nombre'))
                ));

                Session::flash('diplomaturaCreada', 'success', '¡La diplomatura ha sido a&ntilde;adida con exito!');
                Redirect::to("agregarDiplomatura.php");

            } catch(Exception $e) {
                Session::flash('diplomaturaCreada', 'danger', $e->getMessage());
                Redirect::to("agregarDiplomatura.php");
                die();
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
    $msjTag = Session::message('diplomaturaCreada');

    foreach ($camposVal as $campo) {
        $msjTag .= Session::message($campo);
    }

    if ($errors[0] || $msjTag !== '') {
        $used = Session::getUsedInputs($camposVal, true);
    } else {
        $used = Session::getUsedInputs($camposVal);
    }
    

    $token = Token::generate();


    $paginaDiplomatura = <<<DIPLOMATURA
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
                <h1 class="text-center">Agregar una Diplomatura al Sistema</h1>
                {$msjTag}
                <div class="col-md-12 mx-auto">
                    <form action="" method="POST" id="form" autocomplete="off">
                        <div class="form-group">
                            <label for="nombre">Ingrese el Nombre de la Diplomatura <span style="color:red;">*</span></label>
                            <input type="text" name="nombre" class="form-control {$errors['nombre'][0]}" id="nombre" placeholder="Nombre" value="{$used[0]}">
                        </div>
                        
                        <input type="hidden" name="token" value="{$token}">
                        <button type="submit" class="btn btn-danger" id="btn" disabled>Agregar Diplomatura</button>
                    </form>
                </div>
            </div>
        </body>
        </html>
DIPLOMATURA;
    
    return $paginaDiplomatura;
}

$pagina = loadPage();
print($pagina);

