<?php
class Input {
    public static function exists($type = 'post') {
        switch($type) {
            case 'post':
                return (!empty($_POST)) ? true : false;
            break;
            case 'get':
                return (!empty($_GET)) ? true : false;
            break;
            default:
                return false;
            break;
        }
    }

    public static function get($item, $file = false) {
        if ($file) {
            return $_FILES[$item];
        } else {
            if(isset($_POST[$item])) {
                return $_POST[$item];
            } else if (isset($_GET[$item])) {
                return $_GET[$item];
            } else if (isset($_FILES[$item])) {
                return $_FILES[$item];
            }
        }
        return '';
    }

    public static function getAll($fields) {
        $inputs = array();
        foreach ($fields as $field) {
            $field = escape($field);
            array_push($inputs, self::get($field));
        }

        return $inputs;
    }
}