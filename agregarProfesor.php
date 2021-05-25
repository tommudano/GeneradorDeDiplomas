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
                'regExp' => '/^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i'
            ),
            'dni' => array(
                'required' => true,
                'unique' => 'profesores',
                'typeValue' => 'int'
            ),
            'apellido' => array(
                'required' => true,
                'regExp' => '/^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i'
            ),
            'firma' => array(
                // 'imgReq' => false,
                'fileRegExp' => '/^\w*(\.(png)){1}$/i',
                'imgSize' => 63000
            )
        ));

        /// Pasar a binario
        if($validation->passed()) {
            $profesor = new Profesor();
            try {
                $img = Input::get('firma');
                $img64 = base64_encode(file_get_contents($img["tmp_name"]));
                $profesor->crear(array(
                    'nombre' => escape(Input::get('nombre')),
                    'apellido' => escape(Input::get('apellido')),
                    'dni' => escape(Hash::encrypt(Input::get('dni'))),
                    'firma' => $img64
                ));

                Session::flash('profesorCreado', 'success', '¡El profesor ha sido a&ntilde;adido con exito!');
                Redirect::to("agregarProfesor.php");

            } catch(Exception $e) {
                Session::flash('profesorCreado', 'danger', $e->getMessage());
                Redirect::to("agregarProfesor.php");
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
    $camposVal = array('dni', 'nombre', 'apellido', 'firma');
    $errors = Session::formErrors($camposVal);
    $msjTag = Session::message('profesorCreado');

    foreach ($camposVal as $campo) {
        $msjTag .= Session::message($campo);
    }

    if ($errors[0] || $msjTag !== '') {
        $used = Session::getUsedInputs($camposVal, true);
    } else {
        $used = Session::getUsedInputs($camposVal);
    }

    $token = Token::generate();


    $paginaProfesor = <<<PROFESOR
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
            <script src="./Js/validacionProfesor.js"></script>
        </head>
        <body>
            {$menu}
            <div class="container mt-5">
                <h1 class="text-center">Agregar un Profesor al Sistema</h1>
                {$msjTag}
                <div class="col-md-12 mx-auto">
                    <form action="" method="POST" id="form" autocomplete="off" enctype="multipart/form-data">
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

                        <div class="mb-3">
                            <label for="firma" class="form-label">Cargue la Firma</label>
                            <input style="padding-bottom:32px !important;" class="form-control form-control-sm noObligatorio" type="file" id="firma" name="firma" accept="image/x-png">
                        </div>
                        <input type="hidden" name="token" value="{$token}">
                        <button type="submit" class="btn btn-danger" id="btn">Agregar al Profesor</button>
                    </form>
                </div>
            </div>
        </body>
        </html>
PROFESOR;
    
    return $paginaProfesor;
}

$pagina = loadPage();
print($pagina);

