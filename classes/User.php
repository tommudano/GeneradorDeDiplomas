<?php
class User {
    private $_db,
            $_data,
            $_sessionName,
            $_isLoggedIn;

    public function __construct($user = null) {
        $this->_db = DB::getInstance();

        $this->_sessionName = Config::get('session/session_name');

        if($user) {
            $this->find($user);
        } else {
            if(Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);

                if ($this->find($user)) {
                    $this->_isLoggedIn = true;
                }
            }
        }
    }
    
    public function create($fields = array()) {
        if(!$this->_db->insert('usuarios', $fields)) {
            throw new Exception('Hubo un problema creando la cuenta. Intente m&aacute;s tarde.');
        } else {
            $id = $this->_db->get('usuarios', array('usuario', '=', $fields['usuario']))->first()->id;
        }
    }

    public function find($user = null) {
        if($user) {
            if (is_numeric($user)) {
                $field = 'id';
            } else {
                $field = 'usuario';
            }

            $data = $this->_db->get('usuarios', array($field, '=', $user));

            if ($data->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }

    public function login($logData = null, $password = null) {
        $user = $this->find($logData);
        if ($user) {
            if(Hash::pwdVerify($password, $this->data()->pwd)) {
                Session::put($this->_sessionName, $this->data()->id);
                return true;
            }
        }

        return false;
    }

    public function exists() {
        return (!empty($this->_data)) ? true : false;
    }
    
    public function logout() {
        Session::delete($this->_sessionName);
    }

    public function data() {
        return $this->_data;
    }

    public function isLoggedIn() {
        return $this->_isLoggedIn;
    }

}