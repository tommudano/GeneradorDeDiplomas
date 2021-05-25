<?php
require_once 'core/init.php';

$user = new User();

if($user->isLoggedIn()) {
    $user = null;
    Redirect::to("verDiplomas.php");
}

if (Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'usuario' => array(
                'required' => true,
                'regExp' => '/^(?![0-9]*$)[a-zA-Z0-9]{2,50}$/'
            ),
            'pwd' => array(
                'required' => true,
                'min' => 6
            )
        ));
        
        if($validation->passed()) {
            
            try {
                $login = $user->login(Input::get('usuario'), Input::get('pwd'));

                if($login) {
                    Redirect::to("verDiplomas.php");
                } else {
                    Session::flash('login', 'danger', 'Credenciales incorrectas');
                }

            } catch(Exception $e) {
                Session::flash('login', 'danger', $e->getMessage());
                Redirect::to('./login.php');
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
    // Generar errores
    $camposVal = array('usuario', 'pwd');
    $errors = Session::formErrors($camposVal);
    $msjTag = Session::message('login');

    foreach ($camposVal as $campo) {
        $msjTag .= Session::message($campo);
    }

    if ($errors[0] || $msjTag !== '') {
        $used = Session::getUsedInputs($camposVal, true);
    } else {
        $used = Session::getUsedInputs($camposVal);
    }
    

    $token = Token::generate();


    $paginaLogIn = <<<LOGIN
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
            <script src="./Js/validacionLogIn.js"></script>
        </head>
        <body>
            <div class="container mt-5">
                <h1 class="text-center">Iniciar Sesi&oacute;n</h1>
                {$msjTag}
                <div class="col-md-12 mx-auto">
                    <form action="" method="POST" id="form" autocomplete="off">
                        <div class="form-group">
                            <label for="usuario">Ingrese el usuario <span style="color:red;">*</span></label>
                            <input type="text" name="usuario" class="form-control {$errors['usuario'][0]}" id="usuario" placeholder="Usuario" value="{$used[0]}">
                        </div>

                        <div class="form-group">
                            <label for="pwd">Ingrese la Contrase&ntilde;a <span style="color:red;">*</span></label>
                            <input type="password" name="pwd" class="form-control {$errors['pwd'][0]}" id="pwd" placeholder="Contrase&ntilde;a" value="{$used[1]}">
                        </div>

                        <input type="hidden" name="token" value="{$token}">
                        <button type="submit" class="btn btn-danger" id="btn" disabled>Iniciar Sesi&oacute;n</button>
                    </form>
                </div>
            </div>
        </body>
        </html>
LOGIN;
    
    return $paginaLogIn;
}

$pagina = loadPage();
print($pagina);

