<?php
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn()) {
    $user = null;
    Redirect::to("login.php");
} else {
    $profesor = new Profesor();
    if(isset($_POST['eliminar']) && $user->data()->privilegio == "admin") {
        try {
            $profesor->deleteById($_POST['id'], "profesores");
            Session::flash('consultaProfesor', 'success', 'El profesor fue eliminado con &eacute;xito');
            Redirect::to("consultarProfesor.php");
        } catch (Exception $e) {
            Session::flash('consultaProfesor', 'danger', $e->getMessage());
            Redirect::to("consultarProfesor.php");
            die();
        }
    }
}

function loadPage() {
    $profesor = new Profesor();
    $incluibles = new Incluibles();
    $menu = $incluibles->menu();
    $msjTag = Session::message('consultaProfesor');
    $token = Token::generate();

    $resultsCount = $profesor->contarTotal('profesores');
    $per_page = 3;
    
    if (ctype_digit(Input::get('pagina'))) {
        if (Input::get('pagina') > ceil($resultsCount / $per_page) && ceil($resultsCount / $per_page)) {
            $current_page = (int)ceil($resultsCount / $per_page);
        } else if (Input::get('pagina') !== '' && Input::get('pagina') > 0) {
            $current_page = (int)Input::get('pagina');
        } else {
            $current_page = 1;
        }
    } else {
        $current_page = 1;
    }
    
    $pagination = new Pagination($current_page, $per_page, $resultsCount);
    $paginationData = $pagination->generatePaginate();

    $resultsToCast = $profesor->getOffLim($pagination->offset(), $pagination->_per_page, 'profesores');
    $resultsToTemp = Profesor::arrConInfo($resultsToCast);

    $results = $incluibles->crearTarjetaEntidad($resultsToTemp);
    $paginaTodas = <<<PROFESOR
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
            <div class="container mt-5">
                <h1 class="text-center">Profesores Registrados</h1>
                {$msjTag}
                <div>
                    {$results}
                </div>
            
PROFESOR;

    $pageBtn = '<nav aria-label="..." class="mb-5"><ul class="pagination">';
                                            
    if ($paginationData['total'] > 0){
        $pagLink = ''; 

        $k = (($pagination->_current_page + 4 > $paginationData['total']) ? $paginationData['total'] - 4 : (($pagination->_current_page - 4 < 1) ? 5 : $pagination->_current_page));         
        $pagLink = ""; 
        if($pagination->_current_page >= 2){ 
            $pageBtn .= "<li class='page-item'><a class='page-link' href='consultarProfesor.php?&pagina=1'> << </a></li>"; 
            $pageBtn .= "<li class='page-item'><a class='page-link' href='consultarProfesor.php?&pagina=" . ($pagination->_current_page - 1) . "'> < </a></li>"; 
        } 
        for ($i = -4; $i <= 4; $i++) { 
            if($k + $i == $pagination->_current_page) {
                $pagLink .= "<li class='page-item active'><a class='page-link' href='consultarProfesor.php?&pagina=" . ($k + $i) . "'>" . ($k + $i) . "</a></li>"; 
            } else {
                if ($k + $i > 0 && $k + $i <= $paginationData['total']) {
                    $pagLink .= "<li class='page-item'><a class='page-link' href='consultarProfesor.php?&pagina=" . ($k + $i) . "'>" . ($k + $i) . "</a></li>";
                }
            }

        }
        $pageBtn .= $pagLink; 

        if($pagination->_current_page < $paginationData['total']){ 
            $pageBtn .= "<li class='page-item'><a class='page-link' href='consultarProfesor.php?&pagina=" . ($pagination->_current_page + 1) . "'> > </a></li>"; 
            $pageBtn .= "<li class='page-item'><a class='page-link' href='consultarProfesor.php?&pagina=" . $paginationData['total'] . "'> >> </a></li>"; 
        }
        
    }

    $pageBtn .= '</ul></nav>';

    $paginaTodas .= $pageBtn . "</div></body></html>";


    
    return $paginaTodas;
}

$pagina = loadPage();
print($pagina);
?>