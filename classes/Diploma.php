<?php
class Diploma extends Entidad{
    private $id, $alumnoId, $alumno, $profesorId, $profesor, $curso, $mes, $anio, $diplomatura;

    public static function conInfo($object) {
        $instancia = new Self();
        $instancia->id = Hash::encrypt($object->id);
        $instancia->alumnoId = $object->idAlumno;
        $instancia->alumno = $instancia->getAlumnoData($object->idAlumno);
        $instancia->profesorId = $object->idProfesor;
        $instancia->profesor = $instancia->getProfesorData($object->idProfesor);
        $cursoDataArr = $instancia->getCursoData($object->idCurso);
        $instancia->curso = $cursoDataArr[0];
        $instancia->diplomatura = $cursoDataArr[1];
        $instancia->mes = $object->mes;
        $instancia->anio = $object->anio;
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

    private function getAlumnoData($alumnoId) {
        $alumno = new Alumno();
        $res = $alumno->getById('alumnos', $alumnoId);
        $alumno = null;
        if ($res) {
            return $res->nombre . " " . $res->apellido;
        } else {
            throw new Exception('No se pudo obtener tal usuario');
        }
    }

    private function getProfesorData($profesorId) {
        $profesor = new Profesor();
        $res = $profesor->getById('profesores', $profesorId);
        $profesor = null;
        if ($res) {
            return $res->nombre . " " . $res->apellido;
        } else {
            throw new Exception('No se pudo obtener tal profesor');
        }
    }

    private function getCursoData($cursoId) {
        $curso = new Curso();
        $res = $curso->getById('cursos', $cursoId);
        if ($res) {
            $diplomatura = $curso->getDiplomatura($res->diplomaturaId);
            $curso = null;
            return array($res->nombre, $diplomatura);
        } else {
            $curso = null;
            throw new Exception('No se pudo obtener tal curso');
        }
    }

    public function crearTarjeta() {
        $tarjeta = "<div class='card my-3'>
                        <div class='card-body'>
                            <h5 class='card-title'>Alumno: {$this->alumno}</h5>
                            <p class='card-text'>Profesor: {$this->profesor}</p>
                            <p class='card-text'>Curso: {$this->curso}</p>
                            <a href='./preDiploma.php?id={$this->id}' class='btn btn-danger' >Ver Diploma</a>
                        </div>
                    </div>";
        return $tarjeta;
    }
    
    public function crear($fields = array()) {
        $idAlumno = $fields['idAlumno'];
        $idProfesor = $fields['idProfesor'];
        $idCurso = $fields['idCurso'];
        $sql = "SELECT * FROM diplomas WHERE idAlumno=$idAlumno AND idProfesor=$idProfesor AND idCurso=$idCurso";

        $diplomas = $this->db->query($sql);
        if(count($diplomas->results()) > 0) {
            throw new Exception('Este diploma ya fue creado');
        } else {
            if(!$this->db->insert('diplomas', $fields)) {
                throw new Exception('Hubo un problema creando el curso. Intente m&aacute;s tarde.');
            }
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getIdAlumno() { 
        return $this->alumnoId;
    }

    public function getAlumno() { 
        return $this->alumno;
    }

    public function getIDProfesor() {
        return $this->profesorId;
    }

    public function getProfesor() {
        return $this->profesor;
    }

    public function getCurso() {
        return $this->curso;
    }

    public function getMes() {
        return $this->mes;
    }

    public function getAnio() {
        return $this->anio;
    }

    public function getDiplomaId($idAlumno, $idProfesor, $idCurso) {
        $sql = "SELECT * FROM diplomas WHERE idAlumno=$idAlumno AND idProfesor=$idProfesor AND idCurso=$idCurso";

        $diplomas = $this->db->query($sql);
        return $diplomas->first()->id;
    }

    public function getFechaDiploma() {
        $fechaDiploma = $this->mes . " " . $this->anio;
        return $fechaDiploma;
    }

    public function getDiplomatura() {
        return $this->diplomatura;
    }

    public function getObjeto($id){}

    public function eliminarObjeto($id){
        if(!$this->db->delete('diplomas', array('id','=',$id))) {
            throw new Exception('Hubo un problema eliminando el diploma. Intente m&aacute;s tarde.');
        }
    }

    public function buscarDiplomas($param, $offset='', $limit='') {
        $sql = "SELECT * FROM alumnos INNER JOIN diplomas ON alumnos.id = diplomas.idAlumno WHERE alumnos.nombre like '%$param%'";
        
        if ($limit != '') {
            $sql .= " LIMIT $limit";
        }

        if ($offset != '') {
            $sql .= " OFFSET $offset";
        }

        $diplomas = $this->db->query($sql);
        if($diplomas->error()) {
            return false;
        }

        return $diplomas->results();
    }
}