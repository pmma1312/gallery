<?php

class Authentication {

    public static function isAuthenticated($next) {

        if(Auth::isAuthorized()) {
            $next();
        } else {
            header("Location: /login", 401);
            die();
        }

    }

}

?>