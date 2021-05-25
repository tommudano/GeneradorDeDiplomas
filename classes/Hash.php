<?php
class Hash {
    private static $keyEnc;

    public function __construct() {
        self::$keyEnc = "rand4564564kdjskhjfddcd54652135c4ds657467879713145645789";
    }

    public static function pwdHash($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function pwdVerify($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function encrypt($data) {
        if (!defined('AES_256_CBC')) {
            define('AES_256_CBC', 'aes-256-cbc');
        }
        $iv = base64_encode(self::$keyEnc);
        $encrypted = @openssl_encrypt($data, AES_256_CBC, self::$keyEnc, 0, $iv);
        return $encrypted . ':' . base64_encode($iv);
    }

    public static function decrypt($data) {
        if (!defined('AES_256_CBC')) {
            define('AES_256_CBC', 'aes-256-cbc');
        }
        $parts = explode(':', $data);
        $decrypted = @openssl_decrypt($parts[0], AES_256_CBC, self::$keyEnc, 0, base64_decode($parts[1]));
        return $decrypted;
    }
}