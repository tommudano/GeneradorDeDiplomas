<?php
require_once 'core/init.php';
require_once __DIR__.'/vendor/autoload.php';
use chillerlan\QRCode\{QRCode, QROptions};

if (Input::get('id') !== '') {
	$user = new User();
	$id = Hash::decrypt(Input::get('id'));
	$diploma = new Diploma();
	if ($user->isLoggedIn()) {
		if(isset($_POST['eliminar']) && $user->data()->privilegio == "admin") {
			try {
				$diploma->eliminarObjeto($id);
				Session::flash('diploma', 'success', 'El diploma fue eliminado con exito.');
				Redirect::to("verDiplomas.php");
			} catch (Exception $e) {
				Session::flash('diploma', 'danger', $e->getMessage());
				Redirect::to("verDiplomas.php");
				die();
			}
		}
	}

	$diploma = $diploma->getById('diplomas', $id);
	if (!empty($diploma)) {
		$diplomaObtenido = Diploma::conInfo($diploma);
		loadPage($diplomaObtenido);
	} else {
		Redirect::to('./verDiplomas.php');
	}
} else {
	Redirect::to('./verDiplomas.php');
}

function crearQR($diploma, $data, $scale = 10) {
	if (isset($_POST['descargar'])) {
		$scale = 3;
	}
	$options = new QROptions([
		'version'      => 7,
		'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
		'eccLevel'     => QRCode::ECC_L,
		'scale'        => $scale,
		'imageBase64'  => true		
	]);

	$qrcode = new QRCode($options);
	if (isset($_POST['descargar'])) {
		$idProfesor = $diploma->getIdProfesor();
		$profesor = new Profesor();
		$datosProfesor = $profesor->getById('profesores', $idProfesor);
		$datos = array(
			"nombreAlumno" => $diploma->getAlumno(),
			"nombreCurso" => $diploma->getCurso(),
			"nombreProfesor" => $diploma->getProfesor(),
			"firma" => $datosProfesor->firma,
			"fecha" => $diploma->getFechaDiploma(),
			"diplomatura" => $diploma->getDiplomatura()
		);
		Session::flash('diploma', '', array($datos, $qrcode->render($data)));
		Redirect::to("diploma.php");
	}
}

function loadPage($diploma) {
	$data = "http://localhost/diplomas/verificarDiploma.php?id=" . $diploma->getId();
	if (isset($_POST['descargar'])) {
		crearQR($diploma, $data);
	}
	$incluibles = new Incluibles();
	$menu = $incluibles->menu();
	$user = new User();
	$acciones = '<form action="" method="POST" class="mb-3">
					<input type="hidden" name="descargar">
					<button type="submit" class="btn btn-warning" id="btn">Descargar Diploma</button>
				</form>';

	if ($user->isLoggedIn() && $user->data()->privilegio == "admin") {
		$acciones .= '<form action="" method="POST">
						<input type="hidden" name="eliminar">
						<button type="submit" class="btn btn-danger" id="btn">Eliminar Diploma</button>
					</form>';
	}

	$paginaQR = <<<QR
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Diplomas</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
            <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
			<style>
				@media (max-width: 990px) {
					.row {
						display: flex;
						flex-direction: column-reverse;
					}
				
					.row > div {
						margin: auto;
					}
				
					.row img {
						width: 70vw;
						height: auto;        
					}
				}
			</style>
        </head>
        <body>
            {$menu}
			<div class="container mt-4">
				<div class="row">
					<div class="content-heading">
						<h4 class=card-title">Alumno: {$diploma->getAlumno()}</h4>
						<h5 "card-title">Profesor: {$diploma->getProfesor()}</h5>
						<h5 "card-title">Curso: {$diploma->getCurso()}</h5>
						{$acciones}
					</div>
				</div>
			</div>
		</body>
		</html>
QR;
    
	print($paginaQR);
}



