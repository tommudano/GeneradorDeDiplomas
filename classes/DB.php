<?php
class DB {
    private static $_instance = null;
    private $_pdo,
            $_query,
            $_error = false,
            $_results,
            $_count = 0;

    private function __construct() {
        try {
            $this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
        } catch (PDOException $e) {
            Session::flash('home', 'error', $e->getMessage());
            Redirect::to('./index.php');
            die();
        }
    }

    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    public function query($sql, $params = array()) {
        $this->_error = false;

        if($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if(count($params)) {
                foreach($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }

            if ($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }
        $this->_query = null;
        return $this;
    }

    private function action($action, $table, $where = array(), $filter = '') {
        if (count($where) === 3) {
            $operators = array('=', '>', '<', '>=', '<=', '!=');

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            if (in_array($operator, $operators)) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ? {$filter}";

                if(!$this->query($sql, array($value))->error()) {
                    return $this;
                } 
            }
        }
        
        return false;
    }

    public function get($table, $where, $filter = '') {
        return $this->action('SELECT *', $table, $where, $filter);
    }

    public function getAll($table) {
        $sql = "SELECT * FROM $table";

        if(!$this->query($sql)->error()) {
            return $this;
        } 
    }

    public function getAllOffLim ($table, $offset, $limit) {
        $sql = "SELECT * FROM $table LIMIT $limit OFFSET $offset";

        if(!$this->query($sql)->error()) {
            return $this;
        } 
    }

    public function delete($table, $where) {
        return $this->action('DELETE', $table, $where);
    }

    public function insert($table, $fields = array()) {
        $keys = array_keys($fields);
        $values = '';
        $x = 1;

        foreach($fields as $field) {
            $values .= '?';
            if ($x < count($fields)) {
                $values .= ', ';
            }
            $x++;
        }

        $sql = "INSERT INTO {$table} (`" . implode('`,`', $keys) . "`) VALUES ({$values})";
        if(!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    public function update($table, $id, $fields, $typeId = 'id') {
        $set = '';
        $x = 1;

        foreach($fields as $name => $value) {
            $set .= "{$name} = ?";
            if($x < count($fields)) {
                $set .= ', ';
            }
            $x++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE {$typeId} = {$id}";
        if (!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    public function results() {
        return $this->_results;
    }

    public function first() {
        return $this->results()[0];
    }

    public function error() {
        return $this->_error;
    }

    public function count() {
        return $this->_count;
    }


private function actionSearch($action, $tables = array(), $operators = array(), $search) {
        $operatorsAllowed = array('LIKE', 'OR');
        $errorOp = 0;
        foreach ($operators as $operator) {
            if (!in_array($operator, $operatorsAllowed)) {
                $errorOp++;
            }
        }

        if ($errorOp === 0) {
            $calls = array();
            
            foreach ($tables as $table => $fields) {
                $searchSql = '';
                $param = array();
                $i = 0;
                $pos = count($fields);
                
                foreach ($fields as $field) {
                    if ($i === ($pos - 1)) {
                        $searchSql .= "{$field} {$operators[0]} ?";
                    } else {
                        $searchSql .= "{$field} {$operators[0]} ? {$operators[1]} ";
                    }
                    array_push($param, $search);
                    $i++;
                }

                $sql = "{$action} FROM {$table} WHERE {$searchSql}";

                $calls[$table] = array($sql, $param);
            }

            $results = array();

            foreach ($calls as $call => $queries) {
                if (!$this->query($sql, $param)->error()) {
                    if (!empty($this->query($queries[0], $queries[1])->results())) {
                        $results[$call] = $this->query($queries[0], $queries[1])->results();
                    }
                } else {
                    return false;
                }
            }
            
            return $results;
        }
        return false;
        
    }


    public function getSearch($table, $operators, $search) {
        return $this->actionSearch('SELECT *', $table, $operators, $search);
    }




    

    public function __destruct() {
        try {
            $this->_pdo = null;
        } catch (PDOException $e) {
            Session::flash('home', 'error', $e->getMessage());
            Redirect::to('./index.php');
            die();
        }
    }
}