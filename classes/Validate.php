<?php
class Validate {
    private $_passed = false,
            $_errors = array(),
            $_db = null;

    public function __construct() {
        $this->_db = DB::getInstance();
    }

    public function check($source, $items = array()) {
        foreach($items as $item => $rules) {
            foreach($rules as $rule => $rule_value) {
                if ($rule === 'imgReq' || $rule === 'fileRegExp' || $rule === 'imgSize') {
                    $source = $_FILES;
                }
                
                $value = $source[$item];
                $item_value = $item;
                $item = escape($item);
                $item_value = escape($item_value);
                
                if($rule === 'required' && empty($value)) {
                    $this->addError("Este campo es obligatorio", $item);
                } else if (!empty($value)) {
                    switch($rule) {
                        case 'unique':
                            $check = $this->_db->get($rule_value, array($item, '=', Hash::encrypt($value)));
                            if($check->count()) {
                                $this->addError("Este {$item_value} ya est&aacute; registrado.", $item);
                            }
                        break;
                        case 'unique2':
                            $check = $this->_db->get($rule_value, array($item, '=', Hash::encrypt($value)));
                             if($check->count()) {
                                 $this->addError("Este {$item_value} ya est&aacute; registrado.", $item);
                             }
                         break;
                         case 'uniqueExcept':
                            $check = $this->_db->get($rule_value[0], array($item, '=', Hash::encrypt($value)));
                            if($check->count() && $check->first()->id !== $rule_value[1]) {
                                $this->addError("Este {$item_value} ya est&aacute; registrado.", $item);
                            }
                        break;
                         case 'regExp':
                            if (!preg_match($rule_value, $value)) {
                                if(!array_key_exists('regExp2', $rules) || !preg_match($rules['regExp2'], $value)) {
                                    $this->addError("Ingrese un {$item_value} v&aacute;lido", $item);
                                }
                            }
                        break;
                        case 'typeValue':
                            if ($rule_value === 'int') {
                                if (!ctype_digit($value)) {
                                    $this->addError("Dato no v&aacute;lido", $item);
                                }
                            }                            
                        break;
                        case 'min':
                            if(strlen($value) < $rule_value) {
                                $this->addError("El campo debe tener un m&iacute;nimo de {$rule_value} caracteres.", $item);
                            }
                        break;
                        case 'imgReq':
                            if(!file_exists($value['tmp_name']) || !is_uploaded_file($value['tmp_name'])){
                                $this->addError("No se ha cargado la firma", $item);
                            }  
                        break;
                        case 'fileRegExp':
                            if(file_exists($value['tmp_name']) && is_uploaded_file($value['tmp_name'])) {
                                if (!preg_match($rule_value, $value['name'])) {
                                    $this->addError("Archivo no v&aacute;lido", $item);
                                }
                            }
                        break;
                        case 'imgSize':
                            if($value['size'] > $rule_value) {
                                $this->addError("El archivo es muy pesado. Intente con uno m&aacute;s liviano", $item);
                            }
                        break;
                    }
                            
                }
            }
        }
        
        if (empty($this->_errors)) {
            $this->_passed = true;
        }
        
        return $this;
    }
    
    private function addError($error, $item) {
        $this->_errors[$item] = $error;
    }
    
    public function errors() {
        return $this->_errors;
    }

    public function passed() {
        return $this->_passed;
    }
}