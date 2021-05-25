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
                'unique' => 'cursos',
                'regExp' => '/^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i'
            ),
            'diplomaturaId' => array(
                'required' => true,
                'typeValue' => 'int'
            )
        ));
        
        if($validation->passed()) {
            
            $curso = new Curso();
            try {
                $curso->crear(array(
                    'nombre' => escape(Input::get('nombre')),
                    'diplomaturaId' => escape(Input::get('diplomaturaId'))
                ));

                Session::flash('cursoCreado', 'success', '¡El curso ha sido a&ntilde;adido con exito!');
                Redirect::to("agregarCurso.php");

            } catch(Exception $e) {
                Session::flash('cursoCreado', 'danger', $e->getMessage());
                Redirect::to("agregarCurso.php");
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
    $camposVal = array('nombre', 'diplomatura');
    $errors = Session::formErrors($camposVal);
    $msjTag = Session::message('cursoCreado');

    foreach ($camposVal as $campo) {
        $msjTag .= Session::message($campo);
    }

    if ($errors[0] || $msjTag !== '') {
        $used = Session::getUsedInputs($camposVal, true);
    } else {
        $used = Session::getUsedInputs($camposVal);
    }
    

    $token = Token::generate();


    $paginaCurso = <<<CURSO
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
            <script src="./Js/validacionCurso.js"></script>
            <script src="./Js/searchClass.js"></script>
            <script src="./Js/search.js"></script>
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
                <h1 class="text-center">Agregar un Curso al Sistema</h1>
                {$msjTag}
                <div class="col-md-12 mx-auto">
                    <form action="" method="POST" id="form" autocomplete="off">
                        <div class="form-group">
                            <label for="nombre">Ingrese el Nombre del Curso <span style="color:red;">*</span></label>
                            <input type="text" name="nombre" class="form-control {$errors['nombre'][0]}" id="nombre" placeholder="Nombre" value="{$used[0]}">
                        </div>

                        <div class="form-group">
                            <label for="nombreDiplomatura">Diplmatura<span style="color:red;">*</span></label>
                            <button class="btn btn-outline-secondary dropdown-toggle btn-block" type="button" id="dropdownMenu1-1" data-toggle="dropdown">Diplmatura</button>
                            <div class="dropdown-menu dropdown-primary btn-block px-2" id="diplomaturaDrop">
                                <input type="text" class="form-control busqueda noObligatorio" id="nombreDiplomatura" placeholder="Nombre Diplomatura">
                                <div class="containerOpciones"></div>
                            </div>
                        </div>
                        
                        <input type="hidden" id="diplomaturaId" name="diplomaturaId">
                        <input type="hidden" name="token" value="{$token}">
                        <button type="submit" class="btn btn-danger" id="btn" disabled>Agregar Curso</button>
                    </form>
                </div>
            </div>
        </body>
        </html>
CURSO;
    
    return $paginaCurso;
}

$pagina = loadPage();
print($pagina);

