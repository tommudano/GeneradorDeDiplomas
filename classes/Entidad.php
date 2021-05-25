<?php
abstract class Entidad {   
    protected $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function getAll($table) {
        $all = $this->db->getAll($table);
        return $all->results();
    }

    public function contarTotal($table) {
        return count($this->getAll($table));
    }

    public function getOffLim ($offset, $limit, $table) {
        $res = $this->db->getAllOffLim($table, $offset, $limit);
        return $res->results();
    }

    public function getById($table, $id) {
        $obj = $this->db->get($table, array('id','=',$id));
        return $obj->first();
    }

    public function deleteById($id, $table) {
        if(!$this->db->delete($table, array('id','=',$id))) {
            throw new Exception('Hubo un problema con la eliminaci&oacute;n. Intente m&aacute;s tarde.');
        }
    }

    public function actualizarPorId($table, $id, $fields) {
        if(!$this->db->update($table, $id, $fields)) {
            throw new Exception('Hubo un problema con la actualizaci&oacute;n. Intente m&aacute;s tarde.');
        }
    }

    abstract public function crear($fields = array());
    abstract public function getObjeto($dni);
    abstract public function eliminarObjeto($dni);
    abstract public function crearTarjeta();
    abstract public static function conInfo($object);
    abstract public static function arrConInfo($arr);
}
