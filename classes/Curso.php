<?php
class Curso extends Entidad {
    private $id, $nombre, $diplomaturId, $diplomatura;

    public static function conInfo($object) {
        $instancia = new Self();
        $instancia->id = $object->id;
        $instancia->nombre = $object->nombre;
        $instancia->diplomaturaId = $object->diplomaturaId;
        $instancia->diplomatura = $instancia->getDiplomatura($object->diplomaturaId);
        return $instancia;
    }

    public static function arrConInfo($arr) {
        $res = [];
        foreach ($arr as $object) {
            $curso = self::conInfo($object);
            array_push($res, $curso);
        }
        return $res;
    }

    public function crearTarjeta() {
        $user = new User();
        $acciones = "";
        if ($user->data()->privilegio == "admin") {
            $acciones .= "
                    <form action='actualizarCurso.php' method='POST' class='mb-3'>
                        <input type='hidden' name='id' value='{$this->id}'>
                        <input type='hidden' name='nombreV' value='{$this->nombre}'>
                        <input type='hidden' name='diplomaturaId' value='{$this->diplomaturaId}'>
                        <input type='hidden' name='diplomaturaV' value='{$this->diplomatura}'>
                        <input type='hidden' name='editar'>
                        <button type='submit' class='btn btn-warning' id='btn'>Editar Curso</button>
                    </form>
                    <form action='' method='POST'>
                        <input type='hidden' name='id' value='{$this->id}'>
                        <input type='hidden' name='eliminar'>
                        <button type='submit' class='btn btn-danger' id='btn'>Eliminar Curso</button>
                    </form>
            ";
        }
        $tarjeta = "<div class='card col-3 my-3'>
                <div class='card-body'>
                    <h5 class='card-title'>{$this->nombre}</h5>
                    <p class='card-title'>Diplomatura en {$this->diplomatura}</p>
                    {$acciones}
                </div>
            </div>
        ";
        return $tarjeta;
    }

    public function crear($fields = array()) {
        if(!$this->db->insert('cursos', $fields)) {
            throw new Exception('Hubo un problema agregando al curso. Intente m&aacute;s tarde.');
        }
    }
    
    public function getObjeto($nombre) {
        $curso = $this->db->get('cursos', array('nombre','=',$nombre));
        return $curso->first();
    }
    
    public function eliminarObjeto($nombre) {
        if(!$this->db->delete('cursos', array('nombre','=',$nombre))) {
            throw new Exception('Hubo un problema eliminando al curso. Intente m&aacute;s tarde.');
        }
    }
    
    public function getDiplomatura($idDiplomatura) {
        $diplomatura = $this->getById('diplomaturas', $idDiplomatura);
        return $diplomatura->nombre;
    }
}
