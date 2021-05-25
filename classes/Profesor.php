<?php
class Profesor extends Entidad {
    private $id, $nombre, $apellido, $dni;

    public static function conInfo($object) {
        $instancia = new Self();
        $instancia->id = $object->id;
        $instancia->nombre = $object->nombre;
        $instancia->apellido = $object->apellido;
        $instancia->dni = Hash::decrypt($object->dni);
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
                <form action='actualizarProfesor.php' method='POST' class='mb-3'>
                    <input type='hidden' name='id' value='{$this->id}'>
                    <input type='hidden' name='nombreV' value='{$this->nombre}'>
                    <input type='hidden' name='apellidoV' value='{$this->apellido}'>
                    <input type='hidden' name='dniV' value='{$this->dni}'>
                    <input type='hidden' name='editar'>
                    <button type='submit' class='btn btn-warning' id='btn'>Editar Profesor</button>
                </form>
                <form action='' method='POST'>
                    <input type='hidden' name='id' value='{$this->id}'>
                    <input type='hidden' name='eliminar'>
                    <button type='submit' class='btn btn-danger' id='btn'>Eliminar Profesor</button>
                </form>
            ";
        }
        $tarjeta = "<div class='card col-3 my-3'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$this->nombre} {$this->apellido}</h5>
                            <p class='card-text'>{$this->dni}</p>
                            {$acciones}
                        </div>
                    </div>";
        return $tarjeta;
    }

    public function crear($fields = array()) {
        if(!$this->db->insert('profesores', $fields)) {
            throw new Exception('Hubo un problema agregando al profesor. Intente m&aacute;s tarde.');
        }
    }

    public function getObjeto($dni) {
        $profesor = $this->db->get('profesores', array('dni','=',$dni));
        return $profesor->first();
    }

    public function eliminarObjeto($dni) {
        if(!$this->db->delete('profesores', array('dni','=',$dni))) {
            throw new Exception('Hubo un problema eliminando al profesor. Intente m&aacute;s tarde.');
        }
    }
}
