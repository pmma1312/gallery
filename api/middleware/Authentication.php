<?php

class Authentication {

    public static function isAuthenticated($next) {

        if(false) {
            $next();
        } else {
            header("Location: /login", 401);
            die();
        }

    }

}

?>