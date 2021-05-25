<?php 
class Session {
    public static function exists($name) {
        return (isset($_SESSION[$name])) ? true : false;
    }

    public static function put($name, $value = array()) {
        return $_SESSION[$name] = $value;
    }

    public static function get($name) {
        return $_SESSION[$name];
    }

    public static function delete($name) {
        if (self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }

    public static function flash($name, $type = '', $string = '') {
        if(self::exists($name)) {
            $session = self::get($name);
            self::delete($name);
            return $session;
        } else {
            self::put($name, array($type, $string));
        }
    }

    public static function getUsedInputs($fields = array(), $bool = false) {
        if ($bool) {
            $used = Input::getAll($fields);
        } else {
            $used = array();
            foreach ($fields as $field) {
                array_push($used, '');
            }
        }

        return $used;
    }

    public static function message($name) {
        if (self::exists($name)) {
            $msj = self::flash($name);  
            $msjTag = "
                        <div class='alert alert-{$msj[0]} alert-dismissible fade show mt-3 mb-3' role='alert'>
                            <strong>{$msj[1]}</strong>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                        </div>
                    ";
        } else {
            $msjTag = '';
        }

        return $msjTag;
    }
    public static function formErrors($fields = array()) {
        $errors = array(false);
        $error_exists = false;

        foreach ($fields as $campo) {
            ${'input_' . $campo} = '';

            if (self::exists($campo)) { 
                $error_exists = true;
                $errors[0] = true;
                ${'input_' . $campo} = 'is-invalid';
            }
            $errors[$campo] = array(${'input_' . $campo});
        }

        return $errors;
    }
}