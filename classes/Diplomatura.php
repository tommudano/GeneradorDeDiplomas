<?php
class Diplomatura extends Entidad {
    private $id, $nombre;

    public static function conInfo($object) {
        $instancia = new Self();
        $instancia->id = $object->id;
        $instancia->nombre = $object->nombre;
        return $instancia;
    }

    public static function arrConInfo($arr) {
        $res = [];
        foreach ($arr as $object) {
            $diplomatura = self::conInfo($object);
            array_push($res, $diplomatura);
        }
        return $res;
    }

    public function crearTarjeta() {
        $user = new User();
        $acciones = "";
        if ($user->data()->privilegio == "admin") {
            $acciones .= "
            <form action='actualizarDiplomatura.php' method='POST' class='mb-3'>
                <input type='hidden' name='id' value='{$this->id}'>
                <input type='hidden' name='nombreV' value='{$this->nombre}'>
                <input type='hidden' name='editar'>
                <button type='submit' class='btn btn-warning' id='btn'>Editar Diplomatura</button>
            </form>
            <form action='' method='POST'>
                <input type='hidden' name='id' value='{$this->id}'>
                <input type='hidden' name='eliminar'>
                <button type='submit' class='btn btn-danger' id='btn'>Eliminar Diplomatura</button>
            </form>
            ";
        }
        $tarjeta = "<div class='card col-3 my-3'>
                <div class='card-body'>
                    <h5 class='card-title'>{$this->nombre}</h5>
                    {$acciones}
                </div>
            </div>
        ";
        return $tarjeta;
    }

    public function crear($fields = array()) {
        if(!$this->db->insert('diplomaturas', $fields)) {
            throw new Exception('Hubo un problema agregando la diplomatura Intente m&aacute;s tarde.');
        }
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getObjeto($nombre) {
        $diplomatura = $this->db->get('diplomaturas', array('nombre','=',$nombre));
        return $diplomatura->first();
    }

    public function eliminarObjeto($nombre) {
        if(!$this->db->delete('diplomaturas', array('nombre','=',$nombre))) {
            throw new Exception('Hubo un problema eliminando al curso. Intente m&aacute;s tarde.');
        }
    }
}