<?php
class Search {
    private $_db;

    public function __construct() {
        $this->_db = DB::getInstance();
    }

    private function filter($table, $searchResults = array()) {
        $filteredResults = array();
        foreach ($searchResults as $type => $searchResult) {
            foreach($searchResult as $result) {
                if ($table == 'alumnos' || $table == 'profesores') {
                    $found = array(
                        'id' => $result->id,
                        'nombre' => $result->nombre . ' ' . $result->apellido
                    );
                    array_push($filteredResults, $found);
                } else if ($table == 'cursos' || $table == 'diplomaturas') {
                    $found = array(
                        'id' => $result->id,
                        'nombre' => $result->nombre
                    );
                    array_push($filteredResults, $found);
                } else {
                    die();
                }
            }        
        }
        return $filteredResults;
    }
    
    public function search($search, $table, $offset = 0, $limit = -1) {

        $searchTable = array();

        if ($table == 'alumnos' || $table == 'profesores') {
            $searchTable[$table] = array('nombre', 'apellido', 'concat(nombre, " ",apellido)');
        } else if ($table == 'cursos' || $table == 'diplomaturas') {
            $searchTable[$table] = array('nombre');
        } else {
            die();
        }


        $data = $this->_db->getSearch($searchTable, array('LIKE', 'OR'), "%$search%");

        if (count($data) > 0) {
            $filteredResults = $this->filter($table, $data);

            if($limit === -1) {
                $limit = count($filteredResults);
            }

            $filteredData = array();
            for ($i = 0; $i < $limit; $i++) {
                $k = $i + $offset;
                if ($k < count($filteredResults) && $filteredResults[$k] !== null) {
                    array_push($filteredData, $filteredResults[$k]);
                }
            }
            return $filteredData;
        }
        return $data;
    }
}