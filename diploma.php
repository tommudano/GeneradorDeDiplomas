<?php
require_once 'core/init.php';
require_once 'TCPDF/tcpdf.php';

if (!Session::exists('diploma')) {
    Redirect::to("verDiplomas.php");
}

$pdf = new TCPDF('L', 'cm', array(1056, 816));

$pdf->AddPage();

$dataArr = Session::flash('diploma');
$dataImg = $dataArr[1][1];
$datosDiploma = $dataArr[1][0];

$nombreAlumno = html_entity_decode($datosDiploma['nombreAlumno']);
$nombreCurso = html_entity_decode($datosDiploma['nombreCurso']);
$nombreProfesor = html_entity_decode($datosDiploma['nombreProfesor']);
$fecha = html_entity_decode($datosDiploma['fecha']);
$diplomatura = html_entity_decode($datosDiploma['diplomatura']);
$nombreCursoDiplo = $nombreCurso . ' - ' . $diplomatura;

$ptosAlumno = 27;
$ptosCurso = 20;
$ptosProfesor = 14.5;
$ptosDiplomatura = 10;

$white_image="diploma.png"; //873 x 622 
$im = imagecreatefrompng($white_image);
$im2 = imagecreatefrompng($dataImg);
$colorTxt = imagecolorallocate($im, 0, 0, 0);
$colorComp = imagecolorallocate($im, 0, 0, 0);
$anchoImg = imagesx($im);  
$altoImg = imagesy($im);

$font_path_N = __DIR__ . '/fonts/NixieOne.ttf';
$font_path_S = __DIR__ . '/fonts/SourceSansPro.ttf';

// Alumno
$textoAlumno = imagettfbbox($ptosAlumno, 0, $font_path_N, $nombreAlumno);
$anchoTxt = $textoAlumno[2]-$textoAlumno[0];
$x = ($anchoImg/2) - ($anchoTxt/2);
imagettftext($im, $ptosAlumno, 0, $x, 355, $colorTxt, $font_path_N, $nombreAlumno);

// Curso
$textoCurso = imagettfbbox($ptosCurso, 0, $font_path_N, $nombreCursoDiplo);
$anchoTxt = $textoCurso[2]-$textoCurso[0];
$x = ($anchoImg/2) - ($anchoTxt/2);
imagettftext($im, $ptosCurso, 0, $x, 410, $colorTxt, $font_path_N, $nombreCursoDiplo);

// Fecha
$textoFecha = imagettfbbox($ptosProfesor, 0, $font_path_S, $fecha);
$anchoTxt = $textoFecha[2]-$textoFecha[0];
$x = ($anchoImg/2) - ($anchoTxt/2);
imagettftext($im, $ptosProfesor, 0, $x, 450, $colorTxt, $font_path_S, $fecha);

// Profesor
$textoProfesor = imagettfbbox($ptosProfesor, 0, $font_path_S, $nombreProfesor);
$anchoTxt = $textoProfesor[2]-$textoProfesor[0];
$x = ($anchoImg/2) - ($anchoTxt/2);
imagettftext($im, $ptosProfesor, 0, $x, 730, $colorTxt, $font_path_S, $nombreProfesor);

// Firma
$firmaEdit = '';
if ($datosDiploma['firma'] == '') {
    $firmaEdit = ImageResize::createFromString(base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==
    "));
} else {
    $firmaEdit = ImageResize::createFromString(base64_decode($datosDiploma['firma']));
}
$firmaEdit->resizeToHeight(120);
$firmaEdited = 'data:image/png;base64,' . base64_encode((string)$firmaEdit);
$firma = imagecreatefrompng($firmaEdited);
$anchoFirma = imagesx($firma);
imagecopy($im, $firma, ($anchoImg - $anchoFirma)/2, 2.1*(imagesy($im)/3), 0, 0, imagesx($firma), imagesy($firma));

// QR
imagecopy($im, $im2, 1.9622*(imagesx($im2)/3), 2.083*(imagesy($im)/3), 0, 0, imagesx($im2), imagesy($im2));

ob_start();
imagepng($im);
$stringdata = ob_get_contents();
ob_end_clean();
$img = $stringdata;
$pdf->Image('@'.$img, 0, 0, 1056, 816);
$pdf->Output('diploma.pdf', 'D');
imagedestroy($im);
imagedestroy($im2);