<?php

use ReallySimpleJWT\Token;
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

class Auth {

    public static function getToken() {
        $token = null;

        $headers = self::getAuthorizationHeader();

        if(!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $token = $matches[1];
            } 
        }

        return $token;
    }

    private static function getAuthorizationHeader() {
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } else if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        } 
        
        if($headers == null && isset($_COOKIE['token'])) {
            $headers = $_COOKIE['token'];
        }

        return $headers;
    }

    public static function renewToken($payload) {
        if($payload['exp'] < (time() + (60 * 15))) {
            $payload['iat'] = time();
            $payload['exp'] = time() + (60 * 30);

            $token = Token::customPayload($payload, Config::TOKEN_SECRET);

            Cookie::setCookie("token", "Bearer $token", 30, "/");
        }
    }

    public static function isAuthorized() : bool {
        return self::validateToken(self::getToken());
    }

    private static function validateToken($token) : bool {
        $retval = false;

        if($token != null) {
            if(Token::validate($token, Config::TOKEN_SECRET)) {
                self::renewToken(self::getTokenPayload());
                $retval = true;
            } 
        } 

        return $retval;
    }

    public static function getTokenPayload() {
        $token = self::getToken();

        if($token)
            return Token::getPayload($token, Config::TOKEN_SECRET);
        else
            return null;
    }

    public static function getTokenVar(string $name) {
        return self::getTokenPayload()[$name];
    }

    public static function logout() {
        Cookie::removeCookie("token", "/");
        header("Location: /", 200);
        die();
    }

}

?>