<?php
class Incluibles {
    public function menu() {
        return $this->construirMenu();
    }

    private function construirMenu() {
        $user = new User();
        $menu = "";
        $classExpand = $user->isLoggedIn() ? "" : "navbar-expand";
        if ($user->isLoggedIn()) {
            $menuAgregar = '';
            if ($user->data()->privilegio == "admin") {
                $menuAgregar = '<li><a class="dropdown-item" href="./agregarCurso.php">Cursos</a></li>
                <li><a class="dropdown-item" href="./agregarDiplomatura.php">Diplomaturas</a></li>
                <li><a class="dropdown-item" href="./agregarEstudiante.php">Estudiantes</a></li>
                <li><a class="dropdown-item" href="./agregarProfesor.php">Profesores</a></li>';
            } else {
                $menuAgregar = '<li><a class="dropdown-item" href="./agregarEstudiante.php">Estudiantes</a></li>';
            }
            $menu .= '
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item active">
                                <div class="dropdown active">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-toggle="dropdown" aria-expanded="false">
                                    Agregar
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                        ' . $menuAgregar . '
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="./crearDiploma.php">Crear Diploma </a>
                            </li>
                            <li class="nav-item active">
                                <div class="dropdown active">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-toggle="dropdown" aria-expanded="false">
                                    Consultar
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                        <li><a class="dropdown-item" href="./consultarCurso.php">Cursos</a></li>
                                        <li><a class="dropdown-item" href="./consultarDiplomatura.php">Diplomaturas</a></li>
                                        <li><a class="dropdown-item" href="./consultarEstudiante.php">Estudiantes</a></li>
                                        <li><a class="dropdown-item" href="./consultarProfesor.php">Profesores</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="./verDiplomas.php">Ver todos los Diplomas</a>
                            </li>
                    ';
            if($user->data()->privilegio == "admin") {
                $menu .= '
                        <li class="nav-item active">
                            <a class="nav-link" href="crearAdmin.php">Crear Usuario</a>
                        </li>
                        ';
            }

            $menu .= '
                            <li class="nav-item active">
                                <a class="nav-link" href="./logout.php">Cerrar Sesi&oacute;n</a>
                            </li>
                        </ul>
                    </div>
            ';


        }
        $menu = <<<MENU
            <nav class="navbar navbar-expand-lg navbar-light bg-light {$classExpand}">
                <a class="navbar-brand" href="./verDiplomas.php">Logo</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                {$menu}
            </nav>
MENU;

        return $menu;
    }

    public function crearTarjetaEntidad($entidades) {
        $cont = 0;
        $res = '';
        if (empty($entidades)) {
            $res = 'No hay nada creado a&uacute;n';
        } else {
            foreach ($entidades as $entidad) {
                if ($cont % 3 == 0) {
                    $res .= "<div class='row justify-content-around'>";
                }
                $res .= $entidad->crearTarjeta();
                if (($cont + 1) % 3 == 0) {
                    $res .= "</div>";
                }
                $cont++;
            }
        }

        return $res;
    }

    public function diplomas($diplomas) {
        $res = '';
        if (empty($diplomas)) {
            $res = '¡No hay Diplomas Creado s!';
        } else {
            foreach ($diplomas as $diploma) {
                $res .= $diploma->crearTarjeta();
            }
        }

        return $res;
    }
}